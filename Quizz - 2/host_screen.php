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
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        @keyframes popIn {
            0% { transform: scale(0.5); opacity: 0; }
            80% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-pop-in { animation: popIn 0.8s ease-out forwards; }
        
        .silhouette { filter: brightness(0); transition: filter 3s ease-in-out; }
        .revealed { filter: brightness(1); }
    </style>
</head>
<body class="bg-indigo-900 text-white flex flex-col h-screen overflow-hidden font-sans relative">

    <div class="flex justify-between items-center p-6 bg-black bg-opacity-40 shadow-md z-20 relative">
        <div>
            <span class="text-xs font-black text-gray-400 uppercase tracking-widest">CODE PIN DU SALON</span>
            <h1 class="text-5xl font-black text-yellow-400 tracking-widest drop-shadow-lg"><?= htmlspecialchars($pin) ?></h1>
        </div>
        <button id="btn-next" class="hidden bg-indigo-500 hover:bg-indigo-400 px-8 py-4 rounded-2xl font-black text-xl uppercase transition shadow-lg transform hover:scale-105">
            Suivant
        </button>
    </div>

    <div class="flex-grow flex flex-col items-center justify-center p-6 relative z-10 w-full h-full">
        
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

        <div id="leaderboard" class="w-full h-full flex flex-col items-center justify-start pt-10 pb-4 hidden">
            <h3 id="leaderboard-title" class="text-5xl font-black text-center mb-8 uppercase text-yellow-400 tracking-widest relative z-30"></h3>
            <div id="players-list" class="w-full h-full flex items-end justify-center mt-10"></div>
        </div>

    </div>

    <script>
        let currentStatus = '';
        let timerInterval;
        let transitionTimeout;
        let timeLeft = 0;
        let correctAns = 1;
        let podiumAnimated = false; 

        function sync() {
            fetch(`api_live.php?action=get_state&pin=<?= htmlspecialchars($pin) ?>`)
            .then(r => r.json())
            .then(data => {
                if(currentStatus !== data.status) {
                    currentStatus = data.status;
                    if(data.status !== 'finished') podiumAnimated = false;
                    updateUI(data);
                } else if (data.status === 'leaderboard') {
                    renderLeaderboard(data);
                } else if (data.status === 'finished') {
                    if(!podiumAnimated) {
                        podiumAnimated = true;
                        renderPodium(data);
                    }
                }
            });
        }

        function updateUI(data) {
            const elements = ['q-title', 'q-img', 'q-answers', 'timer-circle', 'leaderboard'];
            elements.forEach(id => document.getElementById(id).classList.add('hidden'));
            document.getElementById('timer-circle').classList.remove('text-red-500', 'border-red-500');
            for(let i=1; i<=4; i++) document.getElementById(`ans${i}`).classList.remove('opacity-20', 'scale-105', 'border-8', 'border-white', 'z-10');

            const btnNext = document.getElementById('btn-next');
            clearTimeout(transitionTimeout);
            clearInterval(timerInterval);

            if (data.status === 'reveal') {
                btnNext.classList.add('hidden');
                document.getElementById('q-title').innerText = data.question.question_text;
                document.getElementById('q-title').classList.remove('hidden');
                
                if (data.question.image_url && data.question.image_url.trim() !== '') {
                    document.getElementById('q-img').src = data.question.image_url;
                    document.getElementById('q-img').classList.remove('hidden');
                } else {
                    document.getElementById('q-img').src = '';
                    document.getElementById('q-img').classList.add('hidden');
                }
                
                transitionTimeout = setTimeout(() => { fetch(`api_live.php?action=activate_playing&pin=<?= htmlspecialchars($pin) ?>`).then(sync); }, 2000);
            } 
            else if (data.status === 'playing') {
                btnNext.classList.add('hidden');
                document.getElementById('q-title').innerText = data.question.question_text;
                document.getElementById('q-title').classList.remove('hidden');
                
                if (data.question.image_url && data.question.image_url.trim() !== '') {
                    document.getElementById('q-img').classList.remove('hidden');
                } else {
                    document.getElementById('q-img').classList.add('hidden');
                }
                
                for(let i=1; i<=4; i++) document.querySelector(`#ans${i} .text`).innerText = data.question[`opt${i}`];
                document.getElementById('q-answers').classList.remove('hidden');
                
                correctAns = data.question.correct_answer;
                timeLeft = data.question.timer || 20;
                let timerEl = document.getElementById('timer-circle');
                timerEl.innerText = timeLeft;
                timerEl.classList.remove('hidden');
                
                timerInterval = setInterval(() => {
                    timeLeft--;
                    timerEl.innerText = timeLeft;
                    if(timeLeft <= 3) timerEl.classList.add('text-red-500', 'border-red-500');
                    if(timeLeft <= 0) {
                        clearInterval(timerInterval);
                        fetch(`api_live.php?action=show_leaderboard&pin=<?= htmlspecialchars($pin) ?>`).then(sync);
                    }
                }, 1000);
            } 
            else if (data.status === 'leaderboard') {
                btnNext.classList.remove('hidden');
                btnNext.innerText = "Question suivante";
                btnNext.onclick = () => fetch(`api_live.php?action=next_step&pin=<?= htmlspecialchars($pin) ?>`).then(sync);
                
                document.getElementById('leaderboard').classList.remove('hidden');
                document.getElementById('leaderboard-title').innerText = "📊 CLASSEMENT PROVISOIRE 📊";
                
                renderLeaderboard(data);
            } 
            else if (data.status === 'finished') {
                btnNext.classList.remove('hidden');
                btnNext.innerText = "Retour au menu";
                btnNext.classList.replace('bg-indigo-500', 'bg-red-500');
                btnNext.onclick = () => window.location.href = 'dashboard.php';
                
                document.getElementById('leaderboard').classList.remove('hidden');
                document.getElementById('leaderboard-title').innerText = "🏆 PODIUM FINAL 🏆";
                
                renderPodium(data);
            }
        }

        function renderLeaderboard(data) {
            let sortedPlayers = [...data.players].sort((a, b) => (data.scores[b.nickname] || 0) - (data.scores[a.nickname] || 0));
            let html = `<div class="flex flex-wrap justify-center gap-6 mt-20">`;
            
            sortedPlayers.slice(0, 5).forEach((p, index) => {
                let badge = p.is_member ? `<div class="absolute -bottom-2 -right-2 bg-yellow-400 text-black text-[12px] font-black w-6 h-6 flex items-center justify-center rounded-full border-2 border-white z-40 shadow-lg">★</div>` : '';
                let zAura = (p.aura == 1 || p.aura == 5) ? 30 : 5;
                let auraHtml = p.aura > 0 ? `<img src="personnage/aura/aura${p.aura}.png" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] object-contain animate-pulse" style="z-index: ${zAura};">` : '';
                
                html += `
                    <div class="flex flex-col items-center">
                        <span class="text-yellow-400 font-black text-xl mb-2">#${index + 1}</span>
                        <div class="relative w-24 h-24 bg-white bg-opacity-10 rounded-full shadow-inner overflow-visible border-4 border-indigo-400 mb-3 flex items-end justify-center">
                            ${auraHtml}
                            <div class="relative w-full h-full overflow-hidden rounded-full flex items-end justify-center">
                                <img src="personnage/tenue/tenue${p.outfit}.png" class="absolute w-[90%] h-[90%] object-contain bottom-0" style="z-index: 10;">
                                <img src="personnage/cheveux/cheveux${p.hair}.png" class="absolute w-[90%] h-[90%] object-contain bottom-0" style="z-index: 20;">
                            </div>
                            ${badge}
                        </div>
                        <span class="font-black bg-white text-indigo-900 px-4 py-1 rounded-full text-sm uppercase">${p.nickname}</span>
                        <span class="font-bold text-indigo-200 mt-2">${data.scores[p.nickname] || 0} pts</span>
                    </div>`;
            });
            document.getElementById('players-list').innerHTML = html + `</div>`;
        }

        function renderPodium(data) {
            let sortedPlayers = [...data.players].sort((a, b) => (data.scores[b.nickname] || 0) - (data.scores[a.nickname] || 0));
            let html = `<div class="relative w-full h-full flex flex-col items-center justify-end">`;
            
            html += `<div class="absolute bottom-0 w-full flex justify-center gap-4 flex-wrap px-10 mb-2 z-0">`;
            for(let i = 3; i < sortedPlayers.length; i++) {
                let p = sortedPlayers[i];
                let delay = Math.random() * 0.5; 
                let badge = p.is_member ? `<div class="absolute -bottom-1 -right-1 bg-yellow-400 text-black text-[10px] font-black w-4 h-4 flex items-center justify-center rounded-full border border-white z-40">★</div>` : '';
                html += `
                <div class="opacity-0 animate-pop-in flex flex-col items-center" style="animation-delay: ${delay}s">
                    <div class="relative w-16 h-16 bg-white bg-opacity-10 rounded-full border-2 border-indigo-400 overflow-visible flex items-end justify-center">
                        <div class="relative w-full h-full overflow-hidden rounded-full flex items-end justify-center">
                            <img src="personnage/tenue/tenue${p.outfit}.png" class="absolute w-[90%] h-[90%] object-contain bottom-0" style="z-index:10;">
                            <img src="personnage/cheveux/cheveux${p.hair}.png" class="absolute w-[90%] h-[90%] object-contain bottom-0" style="z-index:20;">
                        </div>
                        ${badge}
                    </div>
                    <span class="text-[10px] font-bold mt-1 bg-black bg-opacity-50 px-2 rounded">${p.nickname}</span>
                </div>`;
            }
            html += `</div>`;

            html += `<div class="flex items-end justify-center gap-4 md:gap-8 z-10 w-full max-w-4xl mx-auto h-[400px]">`;

            const getWinnerHtml = (p, id, place, medal, heightClass, bgClass, colorClass, borderClass) => {
                if(!p) return `<div class="w-32 md:w-48"></div>`;
                let zAura = (p.aura == 1 || p.aura == 5) ? 30 : 5;
                let auraHtml = p.aura > 0 ? `<img src="personnage/aura/aura${p.aura}.png" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] object-contain animate-pulse" style="z-index: ${zAura};">` : '';
                let badge = p.is_member ? `<div class="absolute bottom-2 right-2 bg-yellow-400 text-black text-[14px] font-black w-8 h-8 flex items-center justify-center rounded-full border-2 border-white z-40 shadow-lg">★</div>` : '';
                
                let silhouetteClass = place === 1 ? 'silhouette' : ''; 

                return `
                <div id="${id}" class="opacity-0 flex flex-col items-center">
                    <span class="text-4xl md:text-6xl mb-4 ${place === 1 ? 'animate-bounce' : ''}">${medal}</span>
                    <div id="${place === 1 ? 'winner-block' : ''}" class="flex flex-col items-center ${silhouetteClass} transition-all duration-[3000ms]">
                        <div class="relative w-32 h-32 md:w-48 md:h-48 flex items-end justify-center mb-[-20px]">
                            ${auraHtml}
                            <img src="personnage/tenue/tenue${p.outfit}.png" class="absolute w-full h-full object-contain bottom-0" style="z-index: 10;">
                            <img src="personnage/cheveux/cheveux${p.hair}.png" class="absolute w-full h-full object-contain bottom-0" style="z-index: 20;">
                            ${badge}
                        </div>
                        <div class="${bgClass} w-36 md:w-56 ${heightClass} flex flex-col items-center justify-start pt-6 rounded-t-2xl border-4 ${borderClass} shadow-2xl relative z-20">
                            <span class="font-black ${colorClass} text-xl md:text-2xl text-center px-2 truncate w-full">${p.nickname}</span>
                            <span class="font-bold text-black opacity-60 text-lg md:text-xl">${data.scores[p.nickname]||0} pts</span>
                        </div>
                    </div>
                </div>`;
            };

            html += getWinnerHtml(sortedPlayers[1], 'podium-2', 2, '🥈', 'h-40', 'bg-gray-300', 'text-gray-800', 'border-gray-400');
            html += getWinnerHtml(sortedPlayers[0], 'podium-1', 1, '🥇', 'h-56', 'bg-yellow-400', 'text-yellow-900', 'border-yellow-500');
            html += getWinnerHtml(sortedPlayers[2], 'podium-3', 3, '🥉', 'h-28', 'bg-orange-400', 'text-orange-900', 'border-orange-500');

            html += `</div></div>`;
            document.getElementById('players-list').innerHTML = html;

            setTimeout(() => { 
                let p3 = document.getElementById('podium-3'); 
                if(p3) { p3.classList.remove('opacity-0'); p3.classList.add('animate-pop-in'); }
            }, 4000);
            
            setTimeout(() => { 
                let p2 = document.getElementById('podium-2'); 
                if(p2) { p2.classList.remove('opacity-0'); p2.classList.add('animate-pop-in'); }
            }, 8000);
            
            setTimeout(() => { 
                let p1 = document.getElementById('podium-1'); 
                if(p1) { p1.classList.remove('opacity-0'); p1.classList.add('animate-pop-in'); }
            }, 12000);
            
            setTimeout(() => { 
                let winner = document.getElementById('winner-block'); 
                if(winner) { 
                    winner.classList.remove('silhouette'); 
                    winner.classList.add('revealed'); 
                    
                    // CONFETTIS : On lance des salves de confettis sur tout l'écran !
                    fireConfetti();
                }
            }, 16000);
        }

        // --- FONCTION DE LANCER DE CONFETTIS (Grand spectacle) ---
        function fireConfetti() {
          const duration = 15 * 1000;
          const animationEnd = Date.now() + duration;
          const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 100 };

          function randomInRange(min, max) {
            return Math.random() * (max - min) + min;
          }

          const interval = setInterval(function() {
            const timeLeft = animationEnd - Date.now();

            if (timeLeft <= 0) {
              return clearInterval(interval);
            }

            const particleCount = 50 * (timeLeft / duration);
            // Salves depuis le haut-gauche et le haut-droit
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
          }, 250);
        }

        setInterval(sync, 1500);
        sync();
    </script>
</body>
</html>