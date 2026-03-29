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
        @keyframes popIn { 0% { transform: scale(0.5); opacity: 0; } 80% { transform: scale(1.1); opacity: 1; } 100% { transform: scale(1); opacity: 1; } }
        .animate-pop-in { animation: popIn 0.8s ease-out forwards; }
        .silhouette { filter: brightness(0); transition: filter 3s ease-in-out; }
        .revealed { filter: brightness(1); }
        
        /* Cosmétiques VIP CSS */
        @keyframes rainbow { 100% { filter: hue-rotate(360deg); } }
        .aura-rainbow { position: absolute; top: -15%; left: -15%; width: 130%; height: 130%; border-radius: 50%; box-shadow: 0 0 20px 5px #f43f5e, inset 0 0 20px 5px #f43f5e; animation: rainbow 2.5s linear infinite; z-index: 5; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }
        .aura-float { animation: float 3s ease-in-out infinite; }
        .neon-vip { text-shadow: 0 0 5px #fff, 0 0 10px #facc15, 0 0 20px #facc15; color: #facc15 !important; }
    </style>
</head>
<body class="bg-indigo-900 text-white flex flex-col h-screen overflow-hidden font-sans relative">

    <div class="flex justify-between items-center p-6 bg-black bg-opacity-40 shadow-md z-20 relative">
        <div class="flex items-center gap-4">
            <img src="images/logo.png" alt="Logo" class="h-16 drop-shadow-md">
            <div>
                <span class="text-xs font-black text-gray-400 uppercase tracking-widest">CODE PIN DU SALON</span>
                <h1 class="text-5xl font-black text-yellow-400 tracking-widest drop-shadow-lg"><?= htmlspecialchars($pin) ?></h1>
            </div>
        </div>
        <button id="btn-next" class="hidden bg-indigo-500 hover:bg-indigo-400 px-8 py-4 rounded-2xl font-black text-xl uppercase transition shadow-lg transform hover:scale-105">
            Suivant
        </button>
    </div>

    <div id="main-area" class="flex-grow flex flex-col items-center justify-center p-6 relative z-10 w-full h-full">
        
        <div id="x2-badge" class="hidden text-red-500 font-black text-3xl md:text-5xl uppercase animate-bounce mb-6 bg-white px-6 py-2 rounded-2xl shadow-xl">
            🚨 COMPTE DOUBLE ! 🚨
        </div>

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
            <div id="players-list" class="w-full h-full flex items-end justify-center mt-10 relative"></div>
        </div>

    </div>

    <script>
        const sndTick = new Audio('https://assets.mixkit.co/active_storage/sfx/2568/2568-preview.mp3');
        const sndTing = new Audio('https://assets.mixkit.co/active_storage/sfx/2018/2018-preview.mp3');
        const sndApplause = new Audio('https://assets.mixkit.co/active_storage/sfx/2003/2003-preview.mp3');

        let currentStatus = '';
        let timerInterval;
        let transitionTimeout;
        let timeLeft = 0;
        let correctAns = 1;
        let podiumAnimated = false; 

        // Fonction Helper pour générer les avatars avec Auras CSS
        function getAvatarHtml(p, sizeClasses, badgeSize) {
            let auraHtml = '';
            let floatClass = p.aura == 7 ? 'aura-float' : '';
            
            if (p.aura > 0 && p.aura <= 5) {
                let zAura = (p.aura == 1 || p.aura == 5) ? 30 : 5;
                auraHtml = `<img src="personnage/aura/aura${p.aura}.png" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] object-contain animate-pulse" style="z-index: ${zAura};">`;
            } else if (p.aura == 6) {
                auraHtml = `<div class="aura-rainbow"></div>`;
            }
            
            let badge = p.is_member ? `<div class="absolute -bottom-1 -right-1 bg-yellow-400 text-black font-black flex items-center justify-center rounded-full border-2 border-white z-40 shadow-lg ${badgeSize}">★</div>` : '';
            
            return `
            <div class="relative ${sizeClasses} bg-white bg-opacity-20 rounded-full border-2 border-indigo-300 flex items-end justify-center ${floatClass}">
                ${auraHtml}
                <div class="relative w-full h-full overflow-hidden rounded-full flex items-end justify-center">
                    <img src="personnage/tenue/tenue${p.outfit}.png" class="absolute w-[90%] h-[90%] object-contain bottom-0" style="z-index: 10;">
                    <img src="personnage/cheveux/cheveux${p.hair}.png" class="absolute w-[90%] h-[90%] object-contain bottom-0" style="z-index: 20;">
                </div>
                ${badge}
            </div>`;
        }

        function sync() {
            fetch(`api_live.php?action=get_state&pin=<?= htmlspecialchars($pin) ?>`)
            .then(r => r.json())
            .then(data => {
                if(currentStatus !== data.status) {
                    currentStatus = data.status;
                    if(data.status !== 'finished') podiumAnimated = false;
                    updateUI(data);
                } else if (data.status === 'leaderboard') {
                    if (data.current_q_index !== data.questions_list.length - 1) {
                        renderLeaderboardTable(data);
                    }
                } else if (data.status === 'finished') {
                    if(!podiumAnimated) {
                        podiumAnimated = true;
                        renderPodium(data);
                    }
                }
            });
        }

        function cleanUpAnswers() {
            document.querySelectorAll('.vote-count').forEach(e => e.remove());
            for(let i=1; i<=4; i++) {
                let ansDiv = document.getElementById(`ans${i}`);
                ansDiv.classList.remove('opacity-30', 'scale-95', 'scale-105', 'border-8', 'border-white', 'z-10', 'relative');
            }
        }

        function updateUI(data) {
            const elements = ['q-title', 'q-img', 'q-answers', 'timer-circle', 'leaderboard', 'x2-badge'];
            elements.forEach(id => document.getElementById(id).classList.add('hidden'));
            document.getElementById('timer-circle').classList.remove('text-red-500', 'border-red-500');
            
            const btnNext = document.getElementById('btn-next');
            clearTimeout(transitionTimeout);
            clearInterval(timerInterval);

            const isLastQuestion = data.current_q_index === data.questions_list.length - 1;

            if (data.status === 'reveal') {
                cleanUpAnswers();
                btnNext.classList.add('hidden');
                document.getElementById('q-title').innerText = data.question.question_text;
                document.getElementById('q-title').classList.remove('hidden');
                
                if (isLastQuestion) document.getElementById('x2-badge').classList.remove('hidden');

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
                cleanUpAnswers();
                btnNext.classList.add('hidden');
                document.getElementById('q-title').innerText = data.question.question_text;
                document.getElementById('q-title').classList.remove('hidden');
                
                if (isLastQuestion) document.getElementById('x2-badge').classList.remove('hidden');

                if (data.question.image_url && data.question.image_url.trim() !== '') {
                    document.getElementById('q-img').classList.remove('hidden');
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
                    
                    if(timeLeft <= 5 && timeLeft > 0) {
                        timerEl.classList.add('text-red-500', 'border-red-500');
                        sndTick.currentTime = 0;
                        sndTick.play().catch(e => {});
                    }
                    
                    if(timeLeft <= 0) {
                        clearInterval(timerInterval);
                        fetch(`api_live.php?action=show_answer&pin=<?= htmlspecialchars($pin) ?>`).then(sync);
                    }
                }, 1000);
            } 
            else if (data.status === 'show_answer') {
                sndTing.play().catch(e => {});

                document.getElementById('q-title').innerText = data.question.question_text;
                document.getElementById('q-title').classList.remove('hidden');
                if (data.question.image_url && data.question.image_url.trim() !== '') {
                    document.getElementById('q-img').classList.remove('hidden');
                }
                
                document.getElementById('q-answers').classList.remove('hidden');
                correctAns = data.question.correct_answer;
                
                for(let i=1; i<=4; i++) {
                    let ansDiv = document.getElementById(`ans${i}`);
                    ansDiv.querySelector('.text').innerText = data.question[`opt${i}`];
                    
                    let votes = data.answer_counts ? (data.answer_counts[i] || 0) : 0;
                    
                    let voteBadge = ansDiv.querySelector('.vote-count');
                    if(!voteBadge) {
                        voteBadge = document.createElement('div');
                        voteBadge.className = 'vote-count absolute -top-4 -right-4 bg-white text-black font-black w-14 h-14 rounded-full flex items-center justify-center text-2xl shadow-xl border-4 border-gray-200 z-20';
                        ansDiv.appendChild(voteBadge);
                        ansDiv.classList.add('relative');
                    }
                    voteBadge.innerText = votes;

                    if (i != correctAns) {
                        ansDiv.classList.add('opacity-30', 'scale-95');
                    } else {
                        ansDiv.classList.add('scale-105', 'border-8', 'border-white', 'z-10');
                    }
                }

                btnNext.classList.remove('hidden');
                if(isLastQuestion) {
                    btnNext.innerText = "🏆 VOIR LE PODIUM";
                    btnNext.onclick = () => fetch(`api_live.php?action=show_leaderboard&pin=<?= htmlspecialchars($pin) ?>`).then(sync);
                } else {
                    btnNext.innerText = "Voir le classement";
                    btnNext.onclick = () => fetch(`api_live.php?action=show_leaderboard&pin=<?= htmlspecialchars($pin) ?>`).then(sync);
                }
            }
            else if (data.status === 'leaderboard') {
                btnNext.classList.remove('hidden');
                document.getElementById('leaderboard').classList.remove('hidden');

                if (isLastQuestion) {
                    btnNext.innerText = "🏆 VOIR LE PODIUM";
                    btnNext.onclick = () => fetch(`api_live.php?action=next_step&pin=<?= htmlspecialchars($pin) ?>`).then(sync);
                    document.getElementById('leaderboard-title').innerText = "FIN DES QUESTIONS !";
                    document.getElementById('players-list').innerHTML = "<div class='text-center mt-12 flex flex-col items-center'><span class='text-8xl mb-6'>⏳</span><p class='text-3xl font-black text-indigo-300 animate-pulse uppercase tracking-widest'>Préparation des résultats...</p></div>";
                } else {
                    btnNext.innerText = "Question suivante";
                    btnNext.onclick = () => fetch(`api_live.php?action=next_step&pin=<?= htmlspecialchars($pin) ?>`).then(sync);
                    document.getElementById('leaderboard-title').innerText = "📊 CLASSEMENT PROVISOIRE 📊";
                    renderLeaderboardTable(data);
                }
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

        function renderLeaderboardTable(data) {
            let sortedPlayers = [...data.players].sort((a, b) => (data.scores[b.nickname] || 0) - (data.scores[a.nickname] || 0));
            
            let html = `
            <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-3xl p-2 border-2 border-indigo-400 shadow-2xl overflow-hidden w-full max-w-4xl">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-indigo-900 bg-opacity-50 text-indigo-200">
                        <tr>
                            <th class="p-4 font-black uppercase tracking-widest text-center w-24">Rang</th>
                            <th class="p-4 font-black uppercase tracking-widest text-center w-24">Avatar</th>
                            <th class="p-4 font-black uppercase tracking-widest">Joueur</th>
                            <th class="p-4 font-black uppercase tracking-widest text-right">Score</th>
                        </tr>
                    </thead>
                    <tbody>`;
            
            sortedPlayers.slice(0, 5).forEach((p, index) => {
                let avatar = getAvatarHtml(p, 'w-16 h-16', 'text-[10px] w-5 h-5');
                let neonClass = p.is_member ? 'neon-vip' : '';
                let streakIcon = (data.streaks && data.streaks[p.nickname] >= 3) ? '<span class="animate-bounce inline-block ml-3 text-2xl" title="Série de bonnes réponses (🔥)">🔥</span>' : '';
                
                let scoreDisplay = (data.mode === 'survie') 
                    ? ('❤️'.repeat(data.hearts[p.nickname] || 0) + '🖤'.repeat(3 - (data.hearts[p.nickname] || 0))) 
                    : (data.scores[p.nickname] || 0);

                html += `
                    <tr class="border-b border-indigo-400/20 hover:bg-white/5 transition-colors">
                        <td class="p-4 text-center font-black text-3xl text-yellow-400">#${index + 1}</td>
                        <td class="p-2 flex justify-center">${avatar}</td>
                        <td class="p-4 font-black text-2xl tracking-wider uppercase ${neonClass}">${p.nickname} ${streakIcon}</td>
                        <td class="p-4 text-right font-bold text-3xl text-white tracking-widest">${scoreDisplay}</td>
                    </tr>`;
            });
            html += `</tbody></table></div>`;
            document.getElementById('players-list').innerHTML = html;
        }

        function renderPodium(data) {
            let sortedPlayers = [...data.players].sort((a, b) => (data.scores[b.nickname] || 0) - (data.scores[a.nickname] || 0));
            let html = `<div class="relative w-full h-full flex flex-col items-center justify-end">`;
            
            // Joueurs éliminés en arrière-plan
            html += `<div class="absolute top-10 md:top-24 w-full flex justify-center gap-6 md:gap-12 flex-wrap px-4 md:px-10 z-0 opacity-50 scale-75 blur-[1px]">`;
            for(let i = 3; i < sortedPlayers.length; i++) {
                let p = sortedPlayers[i];
                let delay = Math.random() * 0.5; 
                let avatar = getAvatarHtml(p, 'w-20 h-20', 'text-[10px] w-5 h-5');
                let neonClass = p.is_member ? 'neon-vip' : '';
                html += `
                <div class="opacity-0 animate-pop-in flex flex-col items-center" style="animation-delay: ${delay}s">
                    ${avatar}
                    <span class="text-[14px] font-bold mt-2 bg-black bg-opacity-50 px-3 py-1 rounded-lg ${neonClass}">${p.nickname}</span>
                </div>`;
            }
            html += `</div>`;

            // Podium Principal
            html += `<div class="flex items-end justify-center gap-4 md:gap-8 z-10 w-full max-w-4xl mx-auto h-[400px]">`;

            const getWinnerHtml = (p, id, place, medal, heightClass, bgClass, colorClass, borderClass) => {
                if(!p) return `<div class="w-32 md:w-48"></div>`;
                let avatar = getAvatarHtml(p, 'w-32 h-32 md:w-48 md:h-48', 'text-[14px] w-8 h-8');
                let silhouetteClass = place === 1 ? 'silhouette' : ''; 
                let neonClass = p.is_member ? 'neon-vip' : '';
                
                let scoreDisplay = (data.mode === 'survie') 
                    ? ('❤️'.repeat(data.hearts[p.nickname] || 0)) 
                    : (data.scores[p.nickname] || 0) + ' pts';

                return `
                <div id="${id}" class="opacity-0 flex flex-col items-center">
                    <span class="text-4xl md:text-6xl mb-4 ${place === 1 ? 'animate-bounce' : ''}">${medal}</span>
                    <div id="${place === 1 ? 'winner-block' : ''}" class="flex flex-col items-center ${silhouetteClass} transition-all duration-[3000ms]">
                        <div class="mb-[-20px]">${avatar}</div>
                        <div class="${bgClass} w-36 md:w-56 ${heightClass} flex flex-col items-center justify-start pt-6 rounded-t-2xl border-4 ${borderClass} shadow-2xl relative z-20">
                            <span class="font-black ${colorClass} ${neonClass} text-xl md:text-2xl text-center px-2 truncate w-full">${p.nickname}</span>
                            <span class="font-bold text-black opacity-60 text-lg md:text-xl tracking-widest">${scoreDisplay}</span>
                        </div>
                    </div>
                </div>`;
            };

            html += getWinnerHtml(sortedPlayers[1], 'podium-2', 2, '🥈', 'h-40', 'bg-gray-300', 'text-gray-800', 'border-gray-400');
            html += getWinnerHtml(sortedPlayers[0], 'podium-1', 1, '🥇', 'h-56', 'bg-yellow-400', 'text-yellow-900', 'border-yellow-500');
            html += getWinnerHtml(sortedPlayers[2], 'podium-3', 3, '🥉', 'h-28', 'bg-orange-400', 'text-orange-900', 'border-orange-500');

            html += `</div>`;
            
            // Statistiques
            let statsHtml = `
            <div class="absolute right-4 md:right-10 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-60 backdrop-blur-md p-6 rounded-3xl border-2 border-indigo-400 shadow-2xl opacity-0 animate-pop-in z-50 w-72 md:w-80" style="animation-delay: 17s;">
                <h4 class="text-xl font-black text-yellow-400 mb-4 uppercase text-center border-b border-indigo-500 pb-3">Statistiques</h4>
                <ul class="space-y-4">`;

            sortedPlayers.slice(0, 5).forEach((p) => {
                let count = (data.correct_counts && data.correct_counts[p.nickname]) || 0;
                let s = count > 1 ? 's' : '';
                let neonClass = p.is_member ? 'neon-vip' : '';
                statsHtml += `
                    <li class="flex justify-between items-center text-md md:text-lg">
                        <span class="font-bold text-white truncate max-w-[120px] uppercase tracking-wider ${neonClass}">${p.nickname}</span>
                        <span class="bg-green-500 text-white px-3 py-1 rounded-xl font-black text-xs md:text-sm shadow-md">${count} juste${s}</span>
                    </li>`;
            });
            statsHtml += `</ul></div>`;
            html += statsHtml;

            // Awards
            let eclair = '', tortue = '', miracule = '';
            let minTime = 9999, maxTime = 0, maxWrong = 0;
            
            data.players.forEach(p => {
                let n = p.nickname;
                let time = data.response_times ? (data.response_times[n] || 0) : 0;
                let c = data.correct_counts ? (data.correct_counts[n] || 0) : 0;
                let w = data.wrong_counts ? (data.wrong_counts[n] || 0) : 0;
                
                if (c > 0 && time > 0 && time < minTime) { minTime = time; eclair = n; }
                if (time > maxTime) { maxTime = time; tortue = n; }
                if (w > maxWrong && (!data.eliminated || !data.eliminated.includes(n))) { maxWrong = w; miracule = n; }
            });

            let awardsHtml = `<div class="absolute top-4 md:top-10 left-1/2 transform -translate-x-1/2 flex flex-wrap justify-center gap-4 opacity-0 animate-pop-in z-50" style="animation-delay: 18s;">`;
            if (eclair) awardsHtml += `<div class="bg-blue-600 border-2 border-blue-300 text-white px-6 py-2 rounded-2xl shadow-lg text-center"><p class="text-[10px] uppercase font-bold text-blue-200">⚡ L'éclair</p><p class="font-black text-xl">${eclair}</p></div>`;
            if (tortue) awardsHtml += `<div class="bg-green-700 border-2 border-green-400 text-white px-6 py-2 rounded-2xl shadow-lg text-center"><p class="text-[10px] uppercase font-bold text-green-300">🐌 La Tortue</p><p class="font-black text-xl">${tortue}</p></div>`;
            if (miracule) awardsHtml += `<div class="bg-purple-600 border-2 border-purple-300 text-white px-6 py-2 rounded-2xl shadow-lg text-center"><p class="text-[10px] uppercase font-bold text-purple-200">🍀 Le Miraculé</p><p class="font-black text-xl">${miracule}</p></div>`;
            awardsHtml += `</div>`;
            
            html += awardsHtml + `</div>`;

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
                    sndApplause.play().catch(e => {});
                    fireConfetti();
                }
            }, 16000);
        }

        function fireConfetti() {
          const duration = 15 * 1000;
          const animationEnd = Date.now() + duration;
          const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 100 };
          function randomInRange(min, max) { return Math.random() * (max - min) + min; }

          const interval = setInterval(function() {
            const timeLeft = animationEnd - Date.now();
            if (timeLeft <= 0) return clearInterval(interval);
            const particleCount = 50 * (timeLeft / duration);
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
          }, 250);
        }

        setInterval(sync, 1500);
        sync();
    </script>
</body>
</html>