<?php
require_once 'db.php';
$pin = $_GET['pin'] ?? '';
if (!$pin) { header("Location: dashboard"); exit; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <title>Écran Hôte - Bernard Quizz</title>
    <style>
        /* === THEME GLOBAL === */
        body {
            background-color: #0f172a;
            background-image:
                radial-gradient(at 0% 0%, #1e1b4b 0px, transparent 50%),
                radial-gradient(at 100% 100%, #312e81 0px, transparent 50%);
            background-attachment: fixed;
            color: white;
            font-family: sans-serif;
        }

        /* Avatar - conteneur circulaire */
        .avatar-circle {
            position: relative;
            background-color: #312e81;
            border-radius: 50%;
            border: 3px solid #4338ca;
            overflow: visible;
            flex-shrink: 0;
        }
        .avatar-circle .avatar-inner {
            position: absolute; inset: 0;
            border-radius: 50%;
            overflow: hidden;
        }
        .avatar-layer {
            position: absolute; top: 0; left: 0;
            width: 100%; height: 100%;
            object-fit: contain;
        }

        /* Effets d'avatar */
        .aura-wrap { animation: pulseAura 2.5s ease-in-out infinite alternate; }
        @keyframes pulseAura { 0%{opacity:0.4} 100%{opacity:1} }

        .effect-rainbow-border { animation: rainbowBorder 3s linear infinite; }
        @keyframes rainbowBorder {
            0%   { border-color: #ef4444; box-shadow: 0 0 20px #ef4444; }
            25%  { border-color: #f59e0b; box-shadow: 0 0 20px #f59e0b; }
            50%  { border-color: #10b981; box-shadow: 0 0 20px #10b981; }
            75%  { border-color: #3b82f6; box-shadow: 0 0 20px #3b82f6; }
            100% { border-color: #ef4444; box-shadow: 0 0 20px #ef4444; }
        }
        .effect-levitate { animation: levitate 3s ease-in-out infinite; }
        @keyframes levitate { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-15px)} }

        .neon-vip { text-shadow: 0 0 5px #fff, 0 0 10px #facc15, 0 0 20px #facc15; color: #facc15 !important; }

        /* Animations générales */
        @keyframes popIn {
            0% { transform: scale(0.5); opacity: 0; }
            80% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-pop-in { animation: popIn 0.8s ease-out forwards; }

        .silhouette { filter: brightness(0); transition: filter 3s ease-in-out; }
        .revealed { filter: brightness(1); }

        /* Chat flottant */
        @keyframes floatUp {
            0%  { opacity: 0; transform: translateY(20px) scale(0.9); }
            10% { opacity: 1; transform: translateY(0) scale(1); }
            90% { opacity: 1; transform: translateY(-30px); }
            100%{ opacity: 0; transform: translateY(-50px); }
        }
        .chat-bubble { animation: floatUp 5s ease-out forwards; }

        /* Barre du haut */
        #top-bar-info {
            background: rgba(0,0,0,0.5);
            border-bottom: 2px solid rgba(99,102,241,0.3);
            backdrop-filter: blur(8px);
        }

        /* Bouton Suivant */
        #btn-next {
            background-color: #4f46e5; color: white;
            border: 3px solid #3730a3;
            box-shadow: 0 6px 0 0 #1e1b6e;
            border-radius: 1rem; font-weight: 900;
            font-size: 1.1rem; text-transform: uppercase;
            letter-spacing: 1px; padding: 0.8rem 2rem;
            transition: all 0.1s; cursor: pointer;
        }
        #btn-next:hover { background-color: #6366f1; }
        #btn-next:active { transform: translateY(6px); box-shadow: 0 0px 0 0 #1e1b6e; }

        /* Réponses */
        #ans1 { background-color: #7c3aed; }
        #ans2 { background-color: #db2777; }
        #ans3 { background-color: #0891b2; }
        #ans4 { background-color: #d97706; }

        /* Timer */
        #timer-circle {
            background: #1e1b4b;
            border: 8px solid #4338ca;
            box-shadow: 0 0 40px rgba(99,102,241,0.4);
        }

        /* Leaderboard table */
        .lb-table { background: rgba(30,27,75,0.8); border: 2px solid #4338ca; border-radius: 1.5rem; overflow: hidden; }
        .lb-table thead { background: rgba(0,0,0,0.4); }
        .lb-table tr { border-bottom: 1px solid rgba(99,102,241,0.15); transition: background 0.2s; }
        .lb-table tr:hover { background: rgba(255,255,255,0.04); }

        /* Podium */
        .podium-block { border-top-left-radius: 1rem; border-top-right-radius: 1rem; }
    </style>
</head>
<body class="flex flex-col h-screen overflow-hidden relative">

    <!-- Barre du haut -->
    <div id="top-bar-info" class="flex justify-between items-center px-6 py-4 z-20 relative transition-all duration-500">
        <div class="flex items-center gap-4">
            <img src="images/logo.png" alt="Logo" class="h-14 drop-shadow-md" onerror="this.style.display='none'">
            <div>
                <span class="text-xs font-black text-indigo-400 uppercase tracking-widest">Code PIN</span>
                <h1 class="text-5xl font-black text-yellow-400 tracking-widest drop-shadow-lg" style="font-family:'Caveat',cursive;"><?= htmlspecialchars($pin) ?></h1>
            </div>
        </div>
        <button id="btn-next" class="hidden">Suivant</button>
    </div>

    <!-- Chat flottant -->
    <div id="floating-chat-container" class="absolute bottom-10 left-10 w-80 flex flex-col-reverse gap-3 z-50 pointer-events-none"></div>

    <!-- Bouton toggle chat -->
    <button onclick="toggleChat()" class="fixed right-0 top-1/2 transform -translate-y-1/2 bg-white/10 hover:bg-white/25 p-4 rounded-l-2xl z-50 transition-all text-2xl shadow-xl border-l border-y border-white/20 pointer-events-auto">
        💬
    </button>

    <!-- Panneau chat -->
    <div id="chat-panel" class="fixed right-0 top-0 h-full w-96 bg-black/80 backdrop-blur-xl border-l border-white/10 z-50 transform translate-x-full transition-transform duration-300 flex flex-col">
        <div class="p-4 border-b border-white/10 flex justify-between items-center" style="background:rgba(30,27,75,0.6);">
            <h3 class="text-xl font-black uppercase tracking-widest">Tchat</h3>
            <button onclick="toggleChat()" class="text-white/40 hover:text-white text-3xl font-black leading-none">&times;</button>
        </div>
        <div id="chat-messages" class="flex-grow p-4 overflow-y-auto space-y-4 flex flex-col"></div>
    </div>

    <!-- Zone principale -->
    <div id="main-area" class="flex-grow flex flex-col items-center justify-center p-6 relative z-10 w-full">

        <div id="x2-badge" class="hidden font-black text-3xl md:text-5xl uppercase animate-bounce mb-6 bg-yellow-400 text-indigo-900 px-8 py-3 rounded-2xl shadow-2xl border-4 border-yellow-500">
            🚨 COMPTE DOUBLE ! 🚨
        </div>

        <h2 id="q-title" class="text-4xl md:text-6xl font-black text-center mb-8 drop-shadow-2xl hidden px-4 leading-tight"></h2>
        <img id="q-img" src="" class="hidden max-h-60 rounded-3xl shadow-2xl mb-8 object-cover border-4 border-indigo-500">

        <div id="q-answers" class="hidden w-full max-w-5xl grid grid-cols-2 gap-5 mb-8">
            <div id="ans1" class="p-7 rounded-3xl text-2xl font-bold flex items-center shadow-xl transition-all duration-300 relative"><span class="text-4xl mr-5 drop-shadow-md">✦</span><span class="text drop-shadow-md"></span></div>
            <div id="ans2" class="p-7 rounded-3xl text-2xl font-bold flex items-center shadow-xl transition-all duration-300 relative"><span class="text-4xl mr-5 drop-shadow-md">⬢</span><span class="text drop-shadow-md"></span></div>
            <div id="ans3" class="p-7 rounded-3xl text-2xl font-bold flex items-center shadow-xl transition-all duration-300 relative"><span class="text-4xl mr-5 drop-shadow-md">⬤</span><span class="text drop-shadow-md"></span></div>
            <div id="ans4" class="p-7 rounded-3xl text-2xl font-bold flex items-center shadow-xl transition-all duration-300 relative"><span class="text-4xl mr-5 drop-shadow-md">■</span><span class="text drop-shadow-md"></span></div>
        </div>

        <div id="timer-circle" class="hidden text-7xl font-black w-36 h-36 rounded-full flex items-center justify-center absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50 transition-colors duration-300">
            20
        </div>

        <div id="leaderboard" class="w-full h-full flex flex-col items-center justify-start pt-6 pb-4 hidden">
            <h3 id="leaderboard-title" class="text-5xl font-black text-center mb-8 uppercase text-yellow-400 tracking-widest relative z-30" style="font-family:'Caveat',cursive;"></h3>
            <div id="players-list" class="w-full h-full flex items-end justify-center relative"></div>
        </div>
    </div>

    <script>
        // ====== CONFIGURATION AVATAR ======
        const BLANK       = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=";
        const basePath    = "personnage/images/sections/";
        const skinColors  = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15];
        const commonColors  = [1,8,11,15];
        const clothesColors = [1,19,31,40];
        const hairStyles  = ['very_short','short','medium','long','shaved'];
        const specialThemes = [
            {key:"neutral",   path:"Jacket/Men/Neutral/Men"},
            {key:"job",       path:"Jacket/Men/Job/Men"},
            {key:"antiquity", path:"Jacket/Men/Antiquity/Men"},
            {key:"medieval",  path:"Jacket/Men/Medieval/Men"},
            {key:"pirate",    path:"Jacket/Men/Pirate/Men"},
            {key:"halloween", path:"Jacket/Men/Halloween/Men"},
            {key:"christmas", path:"Jacket/Men/Christmas/Men"},
        ];

        /**
         * Génère le HTML complet d'un avatar avec le nouveau système de couches.
         * @param {object} p          - objet joueur depuis api_live
         * @param {string} sizeClass  - classes Tailwind ex: "w-16 h-16"
         * @param {string} badgeSize  - classes Tailwind badge VIP ex: "w-5 h-5 text-[10px]"
         */
        function getAvatarHtml(p, sizeClass, badgeSize) {
            function img(src, z) {
                return `<img src="${src}" class="avatar-layer" style="z-index:${z};" onerror="this.src='${BLANK}'">`;
            }

            // Résolution couleurs
            const skinColor  = skinColors[p.skinColor ?? 0] ?? 1;
            const hairColor  = commonColors[p.hairColor ?? 0] ?? 1;
            const hairStyle  = hairStyles[p.hairStyle ?? 1] ?? 'short';
            const hairType   = p.hair ?? 1;
            const beardC     = commonColors[p.beardColor ?? 0] ?? 1;
            const mustacheC  = commonColors[p.mustacheColor ?? 0] ?? 1;
            const topC       = clothesColors[p.topColor ?? 0] ?? 1;
            const jacketC    = clothesColors[p.jacketColor ?? 0] ?? 1;
            const eyebrowC   = commonColors[p.eyebrowColor ?? 0] ?? 1;

            // Aura
            let auraHtml = '';
            if (p.aura > 0 && p.aura <= 5) {
                const zAura = (p.aura == 1 || p.aura == 5) ? 60 : 0;
                auraHtml = `<img src="personnage/aura/aura${p.aura}.png" class="avatar-layer aura-wrap" style="z-index:${zAura};" onerror="this.src='${BLANK}'">`;
            }

            // Cheveux arrière
            const hairBackSrc  = hairType > 0 ? `${basePath}Hair/Back/${hairStyle}/${hairType}/${hairColor}.png` : BLANK;
            // Peau
            const skinSrc      = `${basePath}Skin/1/${skinColor}.png`;
            // T-shirt
            const topSrc       = (p.top ?? 1) > 0 ? `${basePath}Top/Men/${p.top ?? 1}/${topC}.png` : BLANK;

            // Veste / Costume spécial (s'excluent mutuellement)
            let jacketSrc  = (p.jacket ?? 0) > 0 ? `${basePath}Jacket/Men/${p.jacket}/${jacketC}.png` : BLANK;
            let specialSrc = BLANK;
            for (const t of specialThemes) {
                if ((p[t.key] ?? 0) > 0) {
                    specialSrc = `${basePath}${t.path}/${p[t.key]}.png`;
                    jacketSrc  = BLANK; // le costume spécial prend la main
                    break;
                }
            }

            // Barbe / Moustache
            const beardSrc    = (p.beard ?? 0) > 0    ? `${basePath}Beards/${p.beard}/${beardC}.png`       : BLANK;
            const mustacheSrc = (p.mustache ?? 0) > 0 ? `${basePath}Mustaches/${p.mustache}/${mustacheC}.png` : BLANK;

            // Visage
            const mouthSrc   = `${basePath}Mouth/${p.mouth ?? 1}.png`;
            const eyesSrc    = `${basePath}Eyes/${p.eyes ?? 1}.png`;
            const eyebrowSrc = `${basePath}Eyebrow/${p.eyebrow ?? 1}/${eyebrowC}.png`;
            const noseSrc    = `${basePath}Nose/${p.nose ?? 1}.png`;

            // Cheveux avant
            const hairFrontSrc = hairType > 0 ? `${basePath}Hair/Front/${hairStyle}/${hairType}/${hairColor}.png` : BLANK;

            // Effet image (type >= 3)
            const effectSrc = (p.effect ?? 0) >= 3 ? `personnage/effets/effet${p.effect}.png` : BLANK;

            // Classes CSS d'effet
            let effectClass = '';
            if (p.effect == 1) effectClass += ' effect-rainbow-border';
            if (p.effect == 2) effectClass += ' effect-levitate';

            // Badge VIP
            const badge = (badgeSize && p.is_member)
                ? `<div class="absolute -bottom-1 -right-1 bg-yellow-400 text-black font-black flex items-center justify-center rounded-full border-2 border-white z-40 shadow-lg ${badgeSize}">★</div>`
                : '';

            return `
            <div class="avatar-circle ${sizeClass} ${effectClass}">
                ${auraHtml}
                <div class="avatar-inner">
                    ${img(skinSrc, 10)}
                    ${img(topSrc, 20)}
                    ${img(jacketSrc, 30)}
                    ${img(specialSrc, 31)}
                    ${img(beardSrc, 40)}
                    ${img(mustacheSrc, 41)}
                    ${img(mouthSrc, 50)}
                    ${img(eyesSrc, 51)}
                    ${img(eyebrowSrc, 52)}
                    ${img(noseSrc, 53)}
                    ${img(hairFrontSrc, 54)}
                    ${img(effectSrc, 70)}
                </div>
                ${img(hairBackSrc, 5)}
                ${badge}
            </div>`;
        }

        // ====== SONS ======
        const sndTick     = new Audio('https://assets.mixkit.co/active_storage/sfx/2568/2568-preview.mp3'); sndTick.volume = 0.3;
        const sndTing     = new Audio('https://assets.mixkit.co/active_storage/sfx/2018/2018-preview.mp3'); sndTing.volume = 0.3;
        const sndApplause = new Audio('https://assets.mixkit.co/active_storage/sfx/2003/2003-preview.mp3'); sndApplause.volume = 0.4;
        const sndGasp     = new Audio('https://assets.mixkit.co/active_storage/sfx/270/270-preview.mp3');   sndGasp.volume = 0.4;
        const sndBoing    = new Audio('https://assets.mixkit.co/active_storage/sfx/3005/3005-preview.mp3'); sndBoing.volume = 0.4;

        let currentStatus  = '';
        let timerInterval;
        let transitionTimeout;
        let timeLeft       = 0;
        let correctAns     = 1;
        let podiumAnimated = false;
        let isChatOpen     = false;
        let lastChatLen    = 0;

        // ====== CHAT ======
        function toggleChat() {
            isChatOpen = !isChatOpen;
            document.getElementById('chat-panel').classList.toggle('translate-x-full');
        }

        function showFloatingMessage(avatarHtml, nick, text) {
            const cont   = document.getElementById('floating-chat-container');
            const bubble = document.createElement('div');
            bubble.className = 'chat-bubble bg-white/10 backdrop-blur-md p-3 rounded-2xl rounded-bl-none shadow-xl border border-white/20 text-white flex items-center gap-3';
            bubble.innerHTML = `${avatarHtml}<div><span class="font-black text-yellow-400 text-[10px] uppercase tracking-widest block mb-1">${nick}</span><p class="text-sm font-bold break-words">${text}</p></div>`;
            cont.prepend(bubble);
            setTimeout(() => bubble.remove(), 5000);
        }

        function renderChat(chatList, players) {
            if (!chatList || chatList.length === 0) return;

            if (chatList.length > lastChatLen) {
                for (let i = lastChatLen; i < chatList.length; i++) {
                    const msg = chatList[i];
                    const p   = players.find(pl => pl.nickname === msg.nick);
                    const av  = p ? getAvatarHtml(p, 'w-10 h-10', '') : '';

                    if (msg.msg.includes('👏')) { sndApplause.currentTime = 0; sndApplause.play().catch(()=>{}); }
                    if (msg.msg.includes('😱')) { sndGasp.currentTime = 0;     sndGasp.play().catch(()=>{}); }
                    if (msg.msg.includes('🤡')) { sndBoing.currentTime = 0;    sndBoing.play().catch(()=>{}); }

                    showFloatingMessage(av, msg.nick, msg.msg);
                }
            }

            const cont = document.getElementById('chat-messages');
            const wasAtBottom = cont.scrollHeight - cont.scrollTop <= cont.clientHeight + 50;

            cont.innerHTML = chatList.map(c => {
                const p  = players.find(pl => pl.nickname === c.nick);
                const av = p ? getAvatarHtml(p, 'w-10 h-10', '') : '';
                return `
                <div class="flex gap-3 items-end">
                    ${av}
                    <div class="bg-white/5 p-3 rounded-xl rounded-bl-none flex-grow border border-white/5">
                        <span class="font-black text-indigo-300 text-[10px] uppercase tracking-widest">${c.nick}</span>
                        <p class="text-white text-sm break-words">${c.msg}</p>
                    </div>
                </div>`;
            }).join('');

            if (wasAtBottom) cont.scrollTop = cont.scrollHeight;
            lastChatLen = chatList.length;
        }

        // ====== SYNC PRINCIPAL ======
        function sync() {
            fetch(`api_live?action=get_state&pin=<?= htmlspecialchars($pin) ?>`)
            .then(r => r.json())
            .then(data => {
                if (data.chat && data.players) renderChat(data.chat, data.players);

                if (currentStatus !== data.status) {
                    currentStatus = data.status;
                    if (data.status !== 'finished') podiumAnimated = false;
                    updateUI(data);
                } else if (data.status === 'leaderboard') {
                    if (data.current_q_index !== (data.questions_list || []).length - 1) {
                        renderLeaderboardTable(data);
                    }
                } else if (data.status === 'finished') {
                    if (!podiumAnimated) { podiumAnimated = true; renderPodium(data); }
                }
            });
        }

        function cleanUpAnswers() {
            document.querySelectorAll('.vote-count').forEach(e => e.remove());
            for (let i = 1; i <= 4; i++) {
                const d = document.getElementById(`ans${i}`);
                d.classList.remove('opacity-30','scale-95','scale-105','border-8','border-white','z-10','relative');
            }
        }

        function updateUI(data) {
            ['q-title','q-img','q-answers','timer-circle','leaderboard','x2-badge'].forEach(id =>
                document.getElementById(id).classList.add('hidden')
            );
            document.getElementById('timer-circle').classList.remove('text-red-400','border-red-500');

            const btnNext = document.getElementById('btn-next');
            const topBar  = document.getElementById('top-bar-info');
            clearTimeout(transitionTimeout);
            clearInterval(timerInterval);

            const qList        = data.questions_list || [];
            const isLastQ      = data.current_q_index === qList.length - 1;

            if (data.status === 'reveal') {
                cleanUpAnswers();
                topBar.classList.remove('opacity-30','scale-75','origin-top-left');
                btnNext.classList.add('hidden');
                document.getElementById('q-title').innerText = data.question.question_text;
                document.getElementById('q-title').classList.remove('hidden');
                if (isLastQ) document.getElementById('x2-badge').classList.remove('hidden');
                if (data.question.image_url && data.question.image_url.trim()) {
                    document.getElementById('q-img').src = data.question.image_url;
                    document.getElementById('q-img').classList.remove('hidden');
                }
                transitionTimeout = setTimeout(() =>
                    fetch(`api_live?action=activate_playing&pin=<?= htmlspecialchars($pin) ?>`).then(sync)
                , 2000);
            }
            else if (data.status === 'playing') {
                cleanUpAnswers();
                topBar.classList.add('opacity-30','scale-75','origin-top-left');
                btnNext.classList.add('hidden');
                document.getElementById('q-title').innerText = data.question.question_text;
                document.getElementById('q-title').classList.remove('hidden');
                if (isLastQ) document.getElementById('x2-badge').classList.remove('hidden');
                if (data.question.image_url && data.question.image_url.trim())
                    document.getElementById('q-img').classList.remove('hidden');
                for (let i = 1; i <= 4; i++) document.querySelector(`#ans${i} .text`).innerText = data.question[`opt${i}`];
                document.getElementById('q-answers').classList.remove('hidden');

                correctAns = data.question.correct_answer;
                timeLeft   = data.question.timer || 20;
                const timerEl = document.getElementById('timer-circle');
                timerEl.innerText = timeLeft;
                timerEl.classList.remove('hidden');

                timerInterval = setInterval(() => {
                    timeLeft--;
                    timerEl.innerText = timeLeft;
                    if (timeLeft <= 5 && timeLeft > 0) {
                        timerEl.classList.add('text-red-400','border-red-500');
                        sndTick.currentTime = 0; sndTick.play().catch(()=>{});
                    }
                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        fetch(`api_live?action=show_answer&pin=<?= htmlspecialchars($pin) ?>`).then(sync);
                    }
                }, 1000);
            }
            else if (data.status === 'show_answer') {
                sndTing.play().catch(()=>{});
                topBar.classList.remove('opacity-30','scale-75','origin-top-left');
                document.getElementById('q-title').innerText = data.question.question_text;
                document.getElementById('q-title').classList.remove('hidden');
                if (data.question.image_url && data.question.image_url.trim())
                    document.getElementById('q-img').classList.remove('hidden');
                document.getElementById('q-answers').classList.remove('hidden');
                correctAns = data.question.correct_answer;

                for (let i = 1; i <= 4; i++) {
                    const d = document.getElementById(`ans${i}`);
                    d.querySelector('.text').innerText = data.question[`opt${i}`];
                    const votes = data.answer_counts ? (data.answer_counts[i] || 0) : 0;
                    let badge = d.querySelector('.vote-count');
                    if (!badge) {
                        badge = document.createElement('div');
                        badge.className = 'vote-count absolute -top-4 -right-4 bg-white text-black font-black w-12 h-12 rounded-full flex items-center justify-center text-xl shadow-xl border-4 border-gray-200 z-20';
                        d.appendChild(badge);
                        d.classList.add('relative');
                    }
                    badge.innerText = votes;
                    if (i != correctAns) d.classList.add('opacity-30','scale-95');
                    else d.classList.add('scale-105','border-8','border-white','z-10');
                }
                btnNext.classList.remove('hidden');
                btnNext.innerText = isLastQ ? '🏆 Voir le podium' : 'Voir le classement';
                btnNext.onclick   = () => fetch(`api_live?action=show_leaderboard&pin=<?= htmlspecialchars($pin) ?>`).then(sync);
            }
            else if (data.status === 'leaderboard') {
                topBar.classList.remove('opacity-30','scale-75','origin-top-left');
                btnNext.classList.remove('hidden');
                document.getElementById('leaderboard').classList.remove('hidden');
                if (isLastQ) {
                    btnNext.innerText = '🏆 Voir le podium';
                    btnNext.onclick   = () => fetch(`api_live?action=next_step&pin=<?= htmlspecialchars($pin) ?>`).then(sync);
                    document.getElementById('leaderboard-title').innerText = 'Fin des questions !';
                    document.getElementById('players-list').innerHTML = `<div class='text-center mt-12 flex flex-col items-center'><span class='text-7xl mb-6'>⏳</span><p class='text-3xl font-black text-indigo-300 animate-pulse uppercase tracking-widest'>Préparation des résultats...</p></div>`;
                } else {
                    btnNext.innerText = 'Question suivante';
                    btnNext.onclick   = () => fetch(`api_live?action=next_step&pin=<?= htmlspecialchars($pin) ?>`).then(sync);
                    document.getElementById('leaderboard-title').innerText = '📊 Classement provisoire 📊';
                    renderLeaderboardTable(data);
                }
            }
            else if (data.status === 'finished') {
                topBar.classList.remove('opacity-30','scale-75','origin-top-left');
                btnNext.classList.remove('hidden');
                btnNext.innerText = 'Retour au menu';
                btnNext.style.background = '#dc2626';
                btnNext.onclick = () => window.location.href = 'dashboard';
                document.getElementById('leaderboard').classList.remove('hidden');
                document.getElementById('leaderboard-title').innerText = '🏆 PODIUM FINAL 🏆';
                renderPodium(data);
            }
        }

        // ====== CLASSEMENT PROVISOIRE ======
        function renderLeaderboardTable(data) {
            const sorted = [...data.players].sort((a,b) => (data.scores[b.nickname]||0)-(data.scores[a.nickname]||0));
            let html = `
            <div class="lb-table w-full max-w-4xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="p-4 font-black uppercase tracking-widest text-center text-indigo-300 w-20">Rang</th>
                            <th class="p-4 font-black uppercase tracking-widest text-center text-indigo-300 w-20">Bernard</th>
                            <th class="p-4 font-black uppercase tracking-widest text-indigo-300">Joueur</th>
                            <th class="p-4 font-black uppercase tracking-widest text-right text-indigo-300">Score</th>
                        </tr>
                    </thead><tbody>`;

            sorted.slice(0, 5).forEach((p, i) => {
                const avatar   = getAvatarHtml(p, 'w-14 h-14', 'w-5 h-5 text-[10px]');
                const neon     = p.is_member ? 'neon-vip' : '';
                const streak   = (data.streaks && data.streaks[p.nickname] >= 3) ? '<span class="animate-bounce inline-block ml-2 text-xl">🔥</span>' : '';
                const rankIcon = ['🥇','🥈','🥉','4.','5.'][i] || `${i+1}.`;
                const score    = data.mode === 'survie'
                    ? ('❤️'.repeat(data.hearts[p.nickname]||0) + '🖤'.repeat(3-(data.hearts[p.nickname]||0)))
                    : (data.scores[p.nickname]||0);

                html += `
                <tr>
                    <td class="p-4 text-center font-black text-3xl text-yellow-400">${rankIcon}</td>
                    <td class="p-3 flex justify-center items-center">${avatar}</td>
                    <td class="p-4 font-black text-xl uppercase tracking-wider ${neon}">${p.nickname} ${streak}</td>
                    <td class="p-4 text-right font-bold text-2xl text-white">${score}</td>
                </tr>`;
            });
            html += `</tbody></table></div>`;
            document.getElementById('players-list').innerHTML = html;
        }

        // ====== PODIUM FINAL ======
        function renderPodium(data) {
            const sorted = [...data.players].sort((a,b) => (data.scores[b.nickname]||0)-(data.scores[a.nickname]||0));
            let html = `<div class="relative w-full h-full flex flex-col items-center justify-end">`;

            // Joueurs 4+ (fond, flous)
            html += `<div class="absolute top-8 w-full flex justify-center gap-6 flex-wrap px-6 z-0 opacity-40 scale-75 blur-[1px]">`;
            for (let i = 3; i < sorted.length; i++) {
                const p   = sorted[i];
                const av  = getAvatarHtml(p, 'w-16 h-16', '');
                const neon= p.is_member ? 'neon-vip' : '';
                const del = Math.random() * 0.5;
                html += `
                <div class="opacity-0 animate-pop-in flex flex-col items-center" style="animation-delay:${del}s">
                    ${av}
                    <span class="text-xs font-bold mt-2 bg-black/50 px-2 py-1 rounded-lg ${neon}">${p.nickname}</span>
                </div>`;
            }
            html += `</div>`;

            // Podium 1-2-3
            html += `<div class="flex items-end justify-center gap-4 md:gap-8 z-10 w-full max-w-4xl mx-auto" style="height:380px;">`;

            const winner = (p, id, place, medal, h, bg, col, border) => {
                if (!p) return `<div class="w-32 md:w-48"></div>`;
                const av    = getAvatarHtml(p, 'w-28 h-28 md:w-40 md:h-40', 'w-8 h-8 text-[12px]');
                const sil   = place === 1 ? 'silhouette' : '';
                const neon  = p.is_member ? 'neon-vip' : '';
                const score = data.mode === 'survie' ? '❤️'.repeat(data.hearts[p.nickname]||0) : `${data.scores[p.nickname]||0} pts`;
                const bounce= place === 1 ? 'animate-bounce' : '';
                return `
                <div id="${id}" class="opacity-0 flex flex-col items-center">
                    <span class="text-4xl md:text-5xl mb-3 ${bounce}">${medal}</span>
                    <div id="${place===1?'winner-block':''}" class="flex flex-col items-center ${sil} transition-all duration-[3000ms]">
                        <div class="mb-[-16px] z-10">${av}</div>
                        <div class="${bg} w-32 md:w-48 ${h} flex flex-col items-center justify-start pt-5 rounded-t-2xl border-4 ${border} shadow-2xl relative z-0">
                            <span class="font-black ${col} ${neon} text-base md:text-lg text-center px-2 truncate w-full">${p.nickname}</span>
                            <span class="font-bold text-black/50 text-sm md:text-base">${score}</span>
                        </div>
                    </div>
                </div>`;
            };

            html += winner(sorted[1], 'podium-2', 2, '🥈', 'h-36', 'bg-gray-300',   'text-gray-800',   'border-gray-400');
            html += winner(sorted[0], 'podium-1', 1, '🥇', 'h-52', 'bg-yellow-400', 'text-yellow-900', 'border-yellow-500');
            html += winner(sorted[2], 'podium-3', 3, '🥉', 'h-24', 'bg-orange-400', 'text-orange-900', 'border-orange-500');
            html += `</div>`;

            // Stats
            let statsHtml = `<div class="absolute right-4 md:right-8 top-1/2 -translate-y-1/2 bg-black/60 backdrop-blur-md p-5 rounded-3xl border-2 border-indigo-500 shadow-2xl opacity-0 animate-pop-in z-50 w-64" style="animation-delay:17s;">
                <h4 class="text-lg font-black text-yellow-400 mb-3 uppercase text-center border-b border-indigo-600 pb-2">Statistiques</h4><ul class="space-y-3">`;
            sorted.slice(0, 5).forEach(p => {
                const c    = (data.correct_counts && data.correct_counts[p.nickname]) || 0;
                const neon = p.is_member ? 'neon-vip' : '';
                statsHtml += `<li class="flex justify-between items-center">
                    <span class="font-bold text-white truncate max-w-[100px] uppercase text-sm ${neon}">${p.nickname}</span>
                    <span class="bg-emerald-600 text-white px-2 py-0.5 rounded-lg font-black text-xs shadow">${c} juste${c>1?'s':''}</span>
                </li>`;
            });
            html += statsHtml + `</ul></div>`;

            // Awards
            let eclair='', tortue='', miracule='';
            let minTime=9999, maxTime=0, maxWrong=0;
            data.players.forEach(p => {
                const n = p.nickname;
                const t = (data.response_times && data.response_times[n]) || 0;
                const c = (data.correct_counts && data.correct_counts[n]) || 0;
                const w = (data.wrong_counts   && data.wrong_counts[n])   || 0;
                if (c > 0 && t > 0 && t < minTime) { minTime = t; eclair = n; }
                if (t > maxTime) { maxTime = t; tortue = n; }
                if (w > maxWrong && (!data.eliminated || !data.eliminated.includes(n))) { maxWrong = w; miracule = n; }
            });
            let awardsHtml = `<div class="absolute top-4 left-1/2 -translate-x-1/2 flex flex-wrap justify-center gap-3 opacity-0 animate-pop-in z-50" style="animation-delay:18s;">`;
            if (eclair)   awardsHtml += `<div class="bg-blue-700 border-2 border-blue-400 text-white px-5 py-2 rounded-2xl shadow-lg text-center"><p class="text-[10px] uppercase font-bold text-blue-200">⚡ L'Éclair</p><p class="font-black text-lg">${eclair}</p></div>`;
            if (tortue)   awardsHtml += `<div class="bg-green-800 border-2 border-green-500 text-white px-5 py-2 rounded-2xl shadow-lg text-center"><p class="text-[10px] uppercase font-bold text-green-300">🐌 La Tortue</p><p class="font-black text-lg">${tortue}</p></div>`;
            if (miracule) awardsHtml += `<div class="bg-purple-700 border-2 border-purple-400 text-white px-5 py-2 rounded-2xl shadow-lg text-center"><p class="text-[10px] uppercase font-bold text-purple-200">🍀 Le Miraculé</p><p class="font-black text-lg">${miracule}</p></div>`;
            html += awardsHtml + `</div></div>`;

            document.getElementById('players-list').innerHTML = html;

            setTimeout(() => { const e = document.getElementById('podium-3'); if(e){e.classList.remove('opacity-0');e.classList.add('animate-pop-in');} }, 4000);
            setTimeout(() => { const e = document.getElementById('podium-2'); if(e){e.classList.remove('opacity-0');e.classList.add('animate-pop-in');} }, 8000);
            setTimeout(() => { const e = document.getElementById('podium-1'); if(e){e.classList.remove('opacity-0');e.classList.add('animate-pop-in');} }, 12000);
            setTimeout(() => {
                const w = document.getElementById('winner-block');
                if (w) { w.classList.remove('silhouette'); w.classList.add('revealed'); sndApplause.play().catch(()=>{}); fireConfetti(); }
            }, 16000);
        }

        function fireConfetti() {
            const dur = 15000, end = Date.now() + dur, defaults = { startVelocity:30, spread:360, ticks:60, zIndex:100 };
            const rnd = (a,b) => Math.random()*(b-a)+a;
            const iv  = setInterval(() => {
                const tl = end - Date.now();
                if (tl <= 0) return clearInterval(iv);
                const n = 50*(tl/dur);
                confetti({...defaults, particleCount:n, origin:{x:rnd(0.1,0.3),y:Math.random()-0.2}});
                confetti({...defaults, particleCount:n, origin:{x:rnd(0.7,0.9),y:Math.random()-0.2}});
            }, 250);
        }

        setInterval(sync, 1500);
        sync();
    </script>
</body>
</html>
