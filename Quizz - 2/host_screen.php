<?php
require_once 'db.php';
$pin = $_GET['pin'] ?? '';
if (!$pin) { header("Location: dashboard.php"); exit; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Écran Hôte - Bernard Quizz</title>
</head>
<body class="bg-indigo-900 text-white flex flex-col h-screen overflow-hidden font-sans relative">

    <div class="flex justify-between items-center p-6 bg-black bg-opacity-40 shadow-md z-10">
        <div>
            <span class="text-xs font-black text-gray-400 uppercase tracking-widest">CODE PIN DU SALON</span>
            <h1 class="text-5xl font-black text-yellow-400 tracking-widest drop-shadow-lg"><?= htmlspecialchars($pin) ?></h1>
        </div>
        <button id="btn-next" class="hidden bg-indigo-500 hover:bg-indigo-400 px-8 py-4 rounded-2xl font-black text-xl uppercase transition shadow-[0_4px_14px_0_rgba(99,102,241,0.39)] transform hover:scale-105">
            Suivant
        </button>
    </div>

    <div class="flex-grow flex flex-col items-center justify-center p-6 relative z-0">
        
        <h2 id="q-title" class="text-4xl md:text-6xl font-black text-center mb-8 drop-shadow-2xl hidden px-4 leading-tight"></h2>
        
        <img id="q-img" src="" class="hidden max-h-72 rounded-3xl shadow-2xl mb-8 object-cover border-8 border-white">

        <div id="q-answers" class="hidden w-full max-w-5xl grid grid-cols-2 gap-6 mb-8">
            <div id="ans1" class="bg-red-500 p-8 rounded-3xl text-3xl font-bold flex items-center shadow-xl transition-all duration-300"><span class="text-5xl mr-6 drop-shadow-md">▲</span><span class="text drop-shadow-md"></span></div>
            <div id="ans2" class="bg-blue-500 p-8 rounded-3xl text-3xl font-bold flex items-center shadow-xl transition-all duration-300"><span class="text-5xl mr-6 drop-shadow-md">◆</span><span class="text drop-shadow-md"></span></div>
            <div id="ans3" class="bg-yellow-500 p-8 rounded-3xl text-3xl font-bold flex items-center shadow-xl transition-all duration-300"><span class="text-5xl mr-6 drop-shadow-md">●</span><span class="text drop-shadow-md"></span></div>
            <div id="ans4" class="bg-green-500 p-8 rounded-3xl text-3xl font-bold flex items-center shadow-xl transition-all duration-300"><span class="text-5xl mr-6 drop-shadow-md">■</span><span class="text drop-shadow-md"></span></div>
        </div>

        <div id="timer-circle" class="hidden text-8xl font-black bg-white text-indigo-900 w-40 h-40 rounded-full flex items-center justify-center shadow-[0_0_50px_rgba(255,255,255,0.3)] border-[10px] border-indigo-400 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50 transition-colors duration-300">
            20
        </div>

        <div id="leaderboard" class="w-full max-w-6xl mt-auto pb-4">
            <h3 id="leaderboard-title" class="text-2xl font-black text-center mb-6 uppercase text-indigo-300 tracking-widest hidden"></h3>
            <div id="players-list" class="flex flex-wrap justify-center gap-6 md:gap-10"></div>
        </div>

    </div>

    <script>
        let currentStatus = '';
        let timerInterval;
        let transitionTimeout;
        let timeLeft = 0;
        let correctAns = 1;

        function sync() {
            fetch(`api_live.php?action=get_state&pin=<?= htmlspecialchars($pin) ?>`)
            .then(r => r.json())
            .then(data => {
                if(currentStatus !== data.status) {
                    currentStatus = data.status;
                    updateUI(data);
                } else if (data.status === 'leaderboard' || data.status === 'finished') {
                    // Rafraîchir les scores en continu pendant le classement
                    renderPlayers(data, data.status === 'finished' ? 3 : 5);
                }
            });
        }

        function updateUI(data) {
            const qTitle = document.getElementById('q-title');
            const qImg = document.getElementById('q-img');
            const qAnswers = document.getElementById('q-answers');
            const timerEl = document.getElementById('timer-circle');
            const leaderboardTitle = document.getElementById('leaderboard-title');
            const btnNext = document.getElementById('btn-next');
            const playersList = document.getElementById('players-list');

            // Nettoyage des timers
            clearTimeout(transitionTimeout);
            clearInterval(timerInterval);

            // Réinitialisation des affichages
            qTitle.classList.add('hidden');
            qImg.classList.add('hidden');
            qAnswers.classList.add('hidden');
            timerEl.classList.add('hidden');
            leaderboardTitle.classList.add('hidden');
            timerEl.classList.remove('text-red-500', 'border-red-500');
            
            for(let i=1; i<=4; i++) {
                document.getElementById(`ans${i}`).classList.remove('opacity-20', 'scale-105', 'border-8', 'border-white', 'z-10');
            }

            if (data.status === 'reveal') {
                // ETAPE 1 : Révélation de la question (pendant 2 secondes)
                btnNext.classList.add('hidden'); // Le maître du jeu n'a rien à faire ici
                playersList.innerHTML = ''; // On cache les joueurs pour se concentrer sur la question
                
                qTitle.innerText = data.question.question_text;
                qTitle.classList.remove('hidden');
                
                if (data.question.image_url) {
                    qImg.src = data.question.image_url;
                    qImg.classList.remove('hidden');
                }

                // Automatisation : Passage à 'playing' après 2 secondes
                transitionTimeout = setTimeout(() => {
                    fetch(`api_live.php?action=activate_playing&pin=<?= htmlspecialchars($pin) ?>`).then(sync);
                }, 2000);

            } else if (data.status === 'playing') {
                // ETAPE 2 : Jeu en cours, réponses affichées, chrono lancé
                btnNext.classList.add('hidden');
                
                qTitle.innerText = data.question.question_text;
                qTitle.classList.remove('hidden');
                if (data.question.image_url) qImg.classList.remove('hidden');
                
                document.querySelector('#ans1 .text').innerText = data.question.opt1;
                document.querySelector('#ans2 .text').innerText = data.question.opt2;
                document.querySelector('#ans3 .text').innerText = data.question.opt3;
                document.querySelector('#ans4 .text').innerText = data.question.opt4;
                qAnswers.classList.remove('hidden');
                
                correctAns = data.question.correct_answer;
                timeLeft = data.question.timer || 20;
                timerEl.innerText = timeLeft;
                timerEl.classList.remove('hidden');
                
                // Chronomètre automatique
                timerInterval = setInterval(() => {
                    timeLeft--;
                    timerEl.innerText = timeLeft;
                    if(timeLeft <= 3) {
                        timerEl.classList.add('text-red-500', 'border-red-500');
                    }
                    if(timeLeft <= 0) {
                        clearInterval(timerInterval);
                        // Automatisation : Passage au classement à la fin du temps
                        fetch(`api_live.php?action=show_leaderboard&pin=<?= htmlspecialchars($pin) ?>`).then(sync);
                    }
                }, 1000);

            } else if (data.status === 'leaderboard') {
                // ETAPE 3 : Affichage du Top 5
                btnNext.classList.remove('hidden');
                btnNext.innerText = "Question suivante";
                btnNext.onclick = () => fetch(`api_live.php?action=next_step&pin=<?= htmlspecialchars($pin) ?>`).then(sync);
                
                qTitle.classList.remove('hidden');
                qAnswers.classList.remove('hidden');
                leaderboardTitle.classList.remove('hidden');
                leaderboardTitle.innerText = data.mode === 'br' ? "⚔️ ÉLIMINATIONS ET CLASSEMENT (TOP 5) ⚔️" : "📊 CLASSEMENT PROVISOIRE (TOP 5) 📊";
                
                // Mettre en valeur la bonne réponse
                for(let i=1; i<=4; i++) {
                    if (i != correctAns) document.getElementById(`ans${i}`).classList.add('opacity-20');
                    else document.getElementById(`ans${i}`).classList.add('scale-105', 'border-8', 'border-white', 'z-10');
                }
                
                renderPlayers(data, 5); // Affiche seulement le Top 5

            } else if (data.status === 'finished') {
                // ETAPE 4 : Fin de partie, Podium
                btnNext.classList.remove('hidden');
                btnNext.innerText = "Retour au menu";
                btnNext.classList.replace('bg-indigo-500', 'bg-red-500');
                btnNext.onclick = () => window.location.href = 'dashboard.php';
                
                leaderboardTitle.classList.remove('hidden');
                leaderboardTitle.innerText = "🏆 PODIUM FINAL 🏆";
                leaderboardTitle.classList.add('text-yellow-400', 'text-5xl');
                
                renderPlayers(data, 3); // Affiche le Top 3
            }
        }

        // Fonction d'affichage des avatars
        function renderPlayers(data, limit = 999) {
            const list = document.getElementById('players-list');
            list.innerHTML = '';
            
            // Si pas d'image, les personnages sont très gros
            let hasImg = data.question && data.question.image_url && data.question.image_url.trim() !== '';
            let sizeClass = hasImg ? 'w-24 h-24 md:w-32 md:h-32' : 'w-40 h-40 md:w-56 md:h-56'; 
            let textClass = hasImg ? 'text-sm' : 'text-xl';

            // Trier les joueurs par score décroissant
            let sortedPlayers = [...data.players].sort((a, b) => {
                let scoreA = data.scores[a.nickname] || 0;
                let scoreB = data.scores[b.nickname] || 0;
                return scoreB - scoreA;
            });

            // Ne garder que le nombre de joueurs demandé (Top 5 ou Top 3)
            let topPlayers = sortedPlayers.slice(0, limit);

            topPlayers.forEach((p, index) => {
                let isEliminated = data.eliminated && data.eliminated.includes(p.nickname);
                let elimClass = isEliminated ? 'grayscale opacity-30' : '';
                let cross = isEliminated ? `<div class="absolute inset-0 flex items-center justify-center text-red-600 font-black text-7xl md:text-9xl drop-shadow-2xl z-50">X</div>` : '';
                let score = data.scores[p.nickname] || 0;
                
                // Médaille pour le podium
                let rankBadge = '';
                if(data.status === 'finished') {
                    if(index === 0) rankBadge = '🥇 1er';
                    if(index === 1) rankBadge = '🥈 2e';
                    if(index === 2) rankBadge = '🥉 3e';
                } else {
                    rankBadge = `#${index + 1}`;
                }

                let html = `
                    <div class="flex flex-col items-center transition-all duration-700 transform ${isEliminated ? 'scale-75' : 'hover:-translate-y-4'}">
                        <span class="text-yellow-400 font-black text-xl drop-shadow mb-2">${rankBadge}</span>
                        <div class="relative ${sizeClass} ${elimClass} bg-white bg-opacity-10 rounded-full shadow-inner overflow-hidden mb-3 flex items-end justify-center border-4 border-indigo-400">
                            <img src="personnage/tenue/tenue${p.outfit}.png" class="absolute w-[90%] h-[90%] object-contain bottom-0" style="z-index: 10;">
                            <img src="personnage/cheveux/cheveux${p.hair}.png" class="absolute w-[90%] h-[90%] object-contain bottom-0" style="z-index: 20;">
                            ${p.aura > 0 ? `<img src="personnage/aura/aura${p.aura}.png" class="absolute w-[180%] h-[180%] object-contain animate-pulse" style="z-index: 30; top:-40%; left:-40%;">` : ''}
                            ${cross}
                        </div>
                        <span class="font-black bg-white text-indigo-900 px-4 py-1 rounded-full shadow-lg ${textClass} uppercase tracking-tight">${p.nickname}</span>
                        <span class="font-bold text-indigo-200 mt-2 text-lg">${score} pts</span>
                    </div>
                `;
                list.innerHTML += html;
            });
        }

        setInterval(sync, 1500);
        sync();
    </script>
</body>
</html>