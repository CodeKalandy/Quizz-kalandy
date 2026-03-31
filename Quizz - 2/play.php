<?php
session_start();
if (!isset($_SESSION['current_pin']) || !isset($_SESSION['current_nick'])) {
    header("Location: dashboard"); // Redirige vers l'accueil si le joueur n'est pas passé par le lobby
    exit;
}
$pin = $_SESSION['current_pin'];
$nick = $_SESSION['current_nick'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Bernard Quizz - Play</title>
    <style>
        @keyframes pulseAura { 0%{opacity:0.4} 100%{opacity:1} }
        @keyframes rainbowBorder {
            0%  {border-color:#ef4444;box-shadow:0 0 15px #ef4444}
            25% {border-color:#f59e0b;box-shadow:0 0 15px #f59e0b}
            50% {border-color:#10b981;box-shadow:0 0 15px #10b981}
            75% {border-color:#3b82f6;box-shadow:0 0 15px #3b82f6}
            100%{border-color:#ef4444;box-shadow:0 0 15px #ef4444}
        }
        @keyframes levitate { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        .neon-vip { text-shadow: 0 0 5px #fff, 0 0 10px #facc15, 0 0 20px #facc15; color: #facc15 !important; }
    </style>
</head>
<body class="bg-indigo-900 text-white font-sans flex flex-col h-screen overflow-hidden">
    
    <div class="bg-black bg-opacity-30 p-4 flex justify-between items-center shadow-md z-20 relative">
        <div class="flex items-center gap-3">
            <div id="my-avatar" class="relative w-12 h-12 bg-white bg-opacity-10 rounded-full border-2 border-indigo-400 flex items-end justify-center"></div>
            <div>
                <p class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest leading-none mb-1">Ton Bernard</p>
                <p id="player-nick" class="font-black text-lg truncate max-w-[120px] leading-none"></p>
            </div>
        </div>
        <div class="flex items-center gap-2 md:gap-4">
            <button id="btn-joker" onclick="useJoker()" class="hidden bg-gray-600 hover:bg-gray-500 text-white font-black px-3 py-2 rounded-xl shadow-lg border-2 border-gray-400 text-xs uppercase transition-transform active:scale-95">
                🃏 50/50
            </button>
            <div class="text-right ml-2">
                <p id="score-label" class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest leading-none mb-1">Score</p>
                <p id="my-score" class="font-black text-2xl text-yellow-400 leading-none transition-all duration-500 tracking-widest">0</p>
            </div>
        </div>
    </div>

    <button onclick="toggleChat()" class="fixed right-0 top-1/3 transform -translate-y-1/2 bg-indigo-600/80 hover:bg-indigo-500 p-3 rounded-l-2xl z-50 transition-all backdrop-blur-md shadow-xl border-l border-y border-indigo-300">
        💬
    </button>

    <div id="chat-panel" class="fixed right-0 top-0 h-full w-72 md:w-80 bg-black/90 backdrop-blur-xl border-l border-indigo-500/50 z-50 transform translate-x-full transition-transform duration-300 flex flex-col">
        <div class="p-4 border-b border-indigo-500/30 flex justify-between items-center bg-indigo-900/50">
            <h3 class="text-lg font-black text-white uppercase tracking-widest">Tchat Public</h3>
            <button onclick="toggleChat()" class="text-white/50 hover:text-white text-3xl font-black">&times;</button>
        </div>
        
        <div id="chat-messages" class="flex-grow p-4 overflow-y-auto space-y-4 flex flex-col"></div>
        
        <div class="bg-indigo-950/80 border-t border-indigo-500/30 flex flex-col">
            <div class="flex gap-2 px-3 pt-3 justify-center">
                <button onclick="addEmoji('👏')" class="bg-white/10 hover:bg-white/20 p-2 rounded-lg text-xl transition" title="Applaudissements">👏</button>
                <button onclick="addEmoji('😱')" class="bg-white/10 hover:bg-white/20 p-2 rounded-lg text-xl transition" title="Choc">😱</button>
                <button onclick="addEmoji('🤡')" class="bg-white/10 hover:bg-white/20 p-2 rounded-lg text-xl transition" title="Boing">🤡</button>
            </div>
            <div class="p-3 flex gap-2">
                <input type="text" id="chat-input" maxlength="100" class="flex-grow bg-black/50 text-white rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-400 text-sm" placeholder="Votre message...">
                <button onclick="sendChat()" class="bg-indigo-600 hover:bg-indigo-500 p-2 rounded-xl transition-colors font-black">➤</button>
            </div>
        </div>
    </div>

    <div id="q-container" class="hidden px-4 mt-8 mb-6 z-10 w-full max-w-4xl mx-auto text-center relative">
        <h2 id="q-text" class="text-2xl md:text-4xl font-black text-white drop-shadow-md leading-tight transition-all duration-300"></h2>
    </div>

    <div id="msg" class="flex-grow flex flex-col items-center justify-center text-3xl font-black text-center italic uppercase p-6 animate-pulse z-10 relative">
        Chargement...
    </div>
    
    <div id="grid-wrapper" class="relative hidden h-[70%] pb-6 px-4 w-full max-w-4xl mx-auto z-10">
        <div id="grid" class="grid grid-cols-1 md:grid-cols-2 gap-4 h-full overflow-y-auto transition-all duration-300">
            <button id="opt1-btn" onclick="submitAns(1)" class="bg-purple-600 rounded-3xl p-6 md:p-8 text-white font-bold shadow-[0_8px_0_0_#7e22ce] active:shadow-none active:translate-y-2 transition-all flex items-center text-left">
                <span class="text-5xl md:text-6xl drop-shadow-md mr-6">✦</span>
                <span id="opt1-text" class="text-xl md:text-2xl leading-tight flex-grow drop-shadow-sm"></span>
            </button>
            <button id="opt2-btn" onclick="submitAns(2)" class="bg-pink-500 rounded-3xl p-6 md:p-8 text-white font-bold shadow-[0_8px_0_0_#be185d] active:shadow-none active:translate-y-2 transition-all flex items-center text-left">
                <span class="text-5xl md:text-6xl drop-shadow-md mr-6">⬢</span>
                <span id="opt2-text" class="text-xl md:text-2xl leading-tight flex-grow drop-shadow-sm"></span>
            </button>
            <button id="opt3-btn" onclick="submitAns(3)" class="bg-cyan-500 rounded-3xl p-6 md:p-8 text-white font-bold shadow-[0_8px_0_0_#0e7490] active:shadow-none active:translate-y-2 transition-all flex items-center text-left">
                <span class="text-5xl md:text-6xl drop-shadow-md mr-6">⬤</span>
                <span id="opt3-text" class="text-xl md:text-2xl leading-tight flex-grow drop-shadow-sm"></span>
            </button>
            <button id="opt4-btn" onclick="submitAns(4)" class="bg-orange-500 rounded-3xl p-6 md:p-8 text-white font-bold shadow-[0_8px_0_0_#c2410c] active:shadow-none active:translate-y-2 transition-all flex items-center text-left">
                <span class="text-5xl md:text-6xl drop-shadow-md mr-6">■</span>
                <span id="opt4-text" class="text-xl md:text-2xl leading-tight flex-grow drop-shadow-sm"></span>
            </button>
        </div>
    </div>

    <script>
        // Utilisation des variables PHP sécurisées au lieu de lire l'URL
        const pin = "<?= htmlspecialchars($pin) ?>";
        const nick = "<?= htmlspecialchars($nick) ?>";
        
        let answered = false;
        let lastQIndex = -1; 
        let startTime = 0;
        let correctAnsId = 1;
        let isMemberCache = false;

        const funnyPhrases = ["Enregistré ! Croise les doigts...", "T'es un rapide toi !", "C'est noté, champion !", "Réponse verrouillée.", "Plus qu'à attendre..."];

        // Gestion du Tchat
        let isChatOpen = false;
        let lastChatLen = 0;

        function toggleChat() {
            isChatOpen = !isChatOpen;
            document.getElementById('chat-panel').classList.toggle('translate-x-full');
        }

        function addEmoji(emoji) {
            const inp = document.getElementById('chat-input');
            inp.value += emoji;
            inp.focus();
        }

        // === SYSTÈME D'AVATAR PAR COUCHES (nouveau) ===
        const BLANK        = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=";
        const basePath     = "personnage/images/sections/";
        const skinColors   = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15];
        const commonColors = [1,8,11,15];
        const clothesColors= [1,19,31,40];
        const hairStyles   = ['very_short','short','medium','long','shaved'];
        const specialThemes= [
            {key:"neutral",   path:"Jacket/Men/Neutral/Men"},
            {key:"job",       path:"Jacket/Men/Job/Men"},
            {key:"antiquity", path:"Jacket/Men/Antiquity/Men"},
            {key:"medieval",  path:"Jacket/Men/Medieval/Men"},
            {key:"pirate",    path:"Jacket/Men/Pirate/Men"},
            {key:"halloween", path:"Jacket/Men/Halloween/Men"},
            {key:"christmas", path:"Jacket/Men/Christmas/Men"},
        ];

        function getAvatarHtml(p, sizeClasses, badgeSize) {
            function lyr(src, z) {
                return `<img src="${src}" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:contain;z-index:${z};" onerror="this.src='${BLANK}'">`;
            }
            const skinC    = skinColors[p.skinColor ?? 0] ?? 1;
            const hairC    = commonColors[p.hairColor ?? 0] ?? 1;
            const hairSt   = hairStyles[p.hairStyle ?? 1] ?? 'short';
            const hairT    = p.hair ?? 1;
            const beardC   = commonColors[p.beardColor ?? 0] ?? 1;
            const mustC    = commonColors[p.mustacheColor ?? 0] ?? 1;
            const topC     = clothesColors[p.topColor ?? 0] ?? 1;
            const jacketC  = clothesColors[p.jacketColor ?? 0] ?? 1;
            const eyebrowC = commonColors[p.eyebrowColor ?? 0] ?? 1;

            // Aura
            let auraHtml = '';
            if (p.aura > 0 && p.aura <= 5) {
                const zA = (p.aura==1||p.aura==5) ? 60 : 0;
                auraHtml = `<img src="personnage/aura/aura${p.aura}.png" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:contain;z-index:${zA};animation:pulseAura 2.5s ease-in-out infinite alternate;" onerror="this.src='${BLANK}'">`;
            }

            // Costume spécial vs veste
            let jacketSrc  = (p.jacket??0)>0 ? `${basePath}Jacket/Men/${p.jacket}/${jacketC}.png` : BLANK;
            let specialSrc = BLANK;
            for (const t of specialThemes) {
                if ((p[t.key]??0) > 0) { specialSrc = `${basePath}${t.path}/${p[t.key]}.png`; jacketSrc = BLANK; break; }
            }

            // Effets CSS
            let wrapExtra = '';
            if (p.effect==1) wrapExtra = 'animation:rainbowBorder 3s linear infinite;';
            if (p.effect==2) wrapExtra = 'animation:levitate 3s ease-in-out infinite;';

            const badge = (badgeSize && p.is_member)
                ? `<div style="position:absolute;bottom:-4px;right:-4px;background:#facc15;color:#000;font-weight:900;display:flex;align-items:center;justify-content:center;border-radius:50%;border:2px solid white;z-index:50;" class="${badgeSize}">★</div>`
                : '';

            return `
            <div class="${sizeClasses} shrink-0" style="position:relative;border-radius:50%;background:#312e81;border:3px solid #4338ca;overflow:visible;${wrapExtra}">
                ${auraHtml}
                <div style="position:absolute;inset:0;border-radius:50%;overflow:hidden;">
                    ${lyr(`${basePath}Skin/1/${skinC}.png`, 10)}
                    ${lyr((p.top??1)>0 ? `${basePath}Top/Men/${p.top??1}/${topC}.png` : BLANK, 20)}
                    ${lyr(jacketSrc, 30)}
                    ${lyr(specialSrc, 31)}
                    ${lyr((p.beard??0)>0    ? `${basePath}Beards/${p.beard}/${beardC}.png` : BLANK, 40)}
                    ${lyr((p.mustache??0)>0 ? `${basePath}Mustaches/${p.mustache}/${mustC}.png` : BLANK, 41)}
                    ${lyr(`${basePath}Mouth/${p.mouth??1}.png`, 50)}
                    ${lyr(`${basePath}Eyes/${p.eyes??1}.png`, 51)}
                    ${lyr(`${basePath}Eyebrow/${p.eyebrow??1}/${eyebrowC}.png`, 52)}
                    ${lyr(`${basePath}Nose/${p.nose??1}.png`, 53)}
                    ${lyr(hairT>0 ? `${basePath}Hair/Front/${hairSt}/${hairT}/${hairC}.png` : BLANK, 54)}
                    ${lyr((p.effect??0)>=3 ? `personnage/effets/effet${p.effect}.png` : BLANK, 70)}
                </div>
                ${lyr(hairT>0 ? `${basePath}Hair/Back/${hairSt}/${hairT}/${hairC}.png` : BLANK, 5)}
                ${badge}
            </div>`;
        }

        function renderChat(chatList, players) {
            if(!chatList || chatList.length === 0) return;
            if(chatList.length === lastChatLen) return;
            
            const cont = document.getElementById('chat-messages');
            const wasAtBottom = cont.scrollHeight - cont.scrollTop <= cont.clientHeight + 50;

            cont.innerHTML = chatList.map(c => {
                let p = players.find(pl => pl.nickname === c.nick);
                let avatar = p ? getAvatarHtml(p, 'w-10 h-10', 'hidden') : '';
                return `
                <div class="flex gap-3 items-end">
                    ${avatar}
                    <div class="bg-white/10 p-3 rounded-xl rounded-bl-none flex-grow border border-white/5">
                        <span class="font-black text-indigo-300 text-[10px] uppercase tracking-widest">${c.nick}</span>
                        <p class="text-white text-sm break-words">${c.msg}</p>
                    </div>
                </div>`;
            }).join('');

            if(wasAtBottom) cont.scrollTop = cont.scrollHeight;
            lastChatLen = chatList.length;
        }

        function sendChat() {
            const inp = document.getElementById('chat-input');
            const msg = inp.value.trim();
            if(!msg) return;
            inp.value = '';
            
            fetch(`api_live?action=send_chat&pin=${pin}`, {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nickname: nick, message: msg })
            }).then(sync);
        }

        document.getElementById('chat-input').addEventListener('keypress', function(e) {
            if(e.key === 'Enter') sendChat();
        });

        function sync() {
            fetch(`api_live?action=get_state&pin=${pin}`)
            .then(r => r.json())
            .then(data => {
                if(data.chat && data.players) renderChat(data.chat, data.players);

                if(data.players) {
                    const me = data.players.find(p => p.nickname === nick);
                    if(me) {
                        isMemberCache = me.is_member;
                        const avatarEl = document.getElementById('my-avatar');
                        if(!avatarEl.dataset.loaded) {
                            avatarEl.dataset.loaded = '1';
                            // On vide le conteneur et on y place les couches directement
                            avatarEl.style.position = 'relative';
                            avatarEl.style.overflow = 'visible';
                            avatarEl.innerHTML = (() => {
                                // Générer les couches sans le div wrapper externe
                                const tmp = document.createElement('div');
                                tmp.innerHTML = getAvatarHtml(me, 'w-12 h-12', '');
                                const inner = tmp.firstElementChild;
                                return inner ? inner.innerHTML : '';
                            })();
                        }

                        let neonClass = me.is_member ? 'neon-vip' : '';
                        let streakIcon = (data.streaks && data.streaks[nick] >= 3) ? ' <span class="animate-bounce inline-block" title="Série de bonnes réponses !">🔥</span>' : '';
                        document.getElementById('player-nick').innerHTML = `<span class="${neonClass}">${nick}</span>` + streakIcon;
                    }
                }

                if (data.mode === 'survie') {
                    document.getElementById('score-label').innerText = 'Vies';
                    let h = data.hearts ? (data.hearts[nick] || 0) : 0;
                    let heartsStr = '❤️'.repeat(h) + '🖤'.repeat(3 - h);
                    if (document.getElementById('my-score').innerText !== heartsStr) {
                        document.getElementById('my-score').innerText = heartsStr;
                    }
                } else {
                    document.getElementById('score-label').innerText = 'Score';
                    if(data.scores && data.scores[nick] !== undefined) {
                        if (data.status === 'show_answer' || data.status === 'leaderboard' || data.status === 'finished') {
                            let scoreEl = document.getElementById('my-score');
                            if (scoreEl.innerText !== data.scores[nick].toString()) {
                                scoreEl.innerText = data.scores[nick];
                                scoreEl.classList.add('scale-125', 'text-white');
                                setTimeout(() => scoreEl.classList.remove('scale-125', 'text-white'), 300);
                            }
                        }
                    }
                }

                if (data.eliminated && data.eliminated.includes(nick)) {
                    hidePlayingUI();
                    document.getElementById('msg').classList.remove('hidden');
                    document.getElementById('msg').innerHTML = "<span class='text-red-500 text-5xl'>ÉLIMINÉ 💀</span><br><span class='text-sm mt-4 block text-gray-400 normal-case'>Regarde la fin sur l'écran principal</span>";
                    return;
                }

                const isLastQuestion = data.current_q_index === data.questions_list?.length - 1;

                if (data.status === 'reveal') {
                    if(lastQIndex !== data.current_q_index) {
                        lastQIndex = data.current_q_index;
                        answered = false;
                        startTime = 0;
                        correctAnsId = data.question.correct_answer;
                        
                        for(let i=1; i<=4; i++) {
                            let btn = document.getElementById(`opt${i}-btn`);
                            btn.style.opacity = '1';
                            btn.disabled = false;
                        }
                    }
                    hidePlayingUI();
                    document.getElementById('msg').classList.remove('hidden');
                    
                    if (isLastQuestion) {
                        document.getElementById('msg').innerHTML = "🚨 PRÉPAREZ-VOUS !<br><span class='text-yellow-400 text-xl mt-2 block'>CETTE QUESTION COMPTE DOUBLE !</span>";
                    } else {
                        document.getElementById('msg').innerText = "PRÉPAREZ-VOUS !";
                    }
                } 
                else if (data.status === 'playing') {
                    if (!answered) {
                        document.getElementById('q-text').innerText = data.question.question_text;
                        document.getElementById('opt1-text').innerText = data.question.opt1;
                        document.getElementById('opt2-text').innerText = data.question.opt2;
                        document.getElementById('opt3-text').innerText = data.question.opt3;
                        document.getElementById('opt4-text').innerText = data.question.opt4;
                        
                        document.getElementById('q-container').classList.remove('hidden');
                        document.getElementById('msg').classList.add('hidden');
                        document.getElementById('grid-wrapper').classList.remove('hidden');
                        
                        if (isMemberCache && !localStorage.getItem('joker_used_' + pin)) {
                            document.getElementById('btn-joker').classList.remove('hidden');
                        }

                        if (startTime === 0) startTime = Date.now();
                    }
                } 
                else if (data.status === 'show_answer') {
                    document.getElementById('btn-joker').classList.add('hidden');
                    hidePlayingUI();
                    document.getElementById('msg').classList.remove('hidden');
                    document.getElementById('msg').innerHTML = "FIN DU TEMPS !<br><span class='text-lg font-normal text-yellow-400 mt-2 block normal-case'>Regardez l'écran pour la réponse !</span>";
                }
                else if (data.status === 'leaderboard') {
                    hidePlayingUI();
                    document.getElementById('msg').classList.remove('hidden');
                    
                    if (isLastQuestion) {
                        document.getElementById('msg').innerHTML = "FIN DES QUESTIONS !<br><span class='text-lg font-normal text-yellow-400 mt-2 block normal-case'>Le podium va apparaître...</span>";
                    } else {
                        document.getElementById('msg').innerHTML = "CLASSEMENT PROVISOIRE<br><span class='text-lg font-normal text-yellow-400 mt-2 block normal-case'>Regardez l'écran principal !</span>";
                    }
                }
                else if (data.status === 'finished') {
                    hidePlayingUI();
                    document.getElementById('msg').classList.remove('hidden');
                    document.getElementById('msg').innerHTML = "PARTIE TERMINÉE !<br><span class='text-sm font-normal normal-case'>Redirection en cours...</span>";
                    
                    localStorage.removeItem('joker_used_' + pin);
                    setTimeout(() => { window.location.href = "dashboard"; }, 5000);
                }
            })
            .catch(err => console.error(err));
        }

        function hidePlayingUI() {
            document.getElementById('q-container').classList.add('hidden');
            document.getElementById('grid-wrapper').classList.add('hidden');
        }

        function useJoker() {
            if (localStorage.getItem('joker_used_' + pin) || answered) return;
            localStorage.setItem('joker_used_' + pin, 'true');
            document.getElementById('btn-joker').classList.add('hidden');
            
            let wrongOptions = [1, 2, 3, 4].filter(n => n != correctAnsId);
            wrongOptions.sort(() => 0.5 - Math.random());
            
            document.getElementById(`opt${wrongOptions[0]}-btn`).style.opacity = '0.2';
            document.getElementById(`opt${wrongOptions[0]}-btn`).disabled = true;
            document.getElementById(`opt${wrongOptions[1]}-btn`).style.opacity = '0.2';
            document.getElementById(`opt${wrongOptions[1]}-btn`).disabled = true;
        }

        function submitAns(num) {
            if (answered) return;
            answered = true;

            document.getElementById('btn-joker').classList.add('hidden');

            const responseTime = (Date.now() - startTime) / 1000;
            const isCorrect = (num == correctAnsId);

            fetch(`api_live?action=submit_answer&pin=${pin}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    nickname: nick, 
                    is_correct: isCorrect, 
                    response_time: responseTime, 
                    answer_index: num 
                })
            }).then(() => {
                hidePlayingUI();
                document.getElementById('msg').classList.remove('hidden');
                document.getElementById('msg').innerText = funnyPhrases[Math.floor(Math.random() * funnyPhrases.length)];
            });
        }

        setInterval(sync, 1500);
        sync();
    </script>
</body>
</html>