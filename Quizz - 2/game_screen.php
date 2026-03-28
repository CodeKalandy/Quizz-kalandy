<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Bernard Quizz - Play</title>
</head>
<body class="bg-indigo-900 text-white font-sans flex flex-col h-screen overflow-hidden">
    
    <div class="bg-black bg-opacity-30 p-4 flex justify-between items-center shadow-md z-10">
        <div class="flex items-center gap-3">
            <div id="my-avatar" class="relative w-12 h-12 bg-white bg-opacity-10 rounded-full border-2 border-indigo-400 flex items-end justify-center">
                </div>
            <div>
                <p class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest leading-none mb-1">Joueur</p>
                <p id="player-nick" class="font-black text-lg truncate max-w-[120px] leading-none"></p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest leading-none mb-1">Score</p>
            <p id="my-score" class="font-black text-2xl text-yellow-400 leading-none">0</p>
        </div>
    </div>

    <div id="msg" class="flex-grow flex flex-col items-center justify-center text-3xl font-black text-center italic uppercase p-6 animate-pulse z-0">
        Chargement...
    </div>
    
    <div id="grid" class="hidden grid-cols-1 gap-4 h-3/4 pb-6 px-4 w-full max-w-sm mx-auto z-0">
        <button onclick="submitAns(1)" class="bg-red-500 rounded-3xl p-6 text-2xl font-bold shadow-[0_10px_0_0_#991b1b] active:shadow-none active:translate-y-2 transition-all flex items-center justify-center">
            <span class="text-6xl drop-shadow-md">▲</span>
        </button>
        <button onclick="submitAns(2)" class="bg-blue-500 rounded-3xl p-6 text-2xl font-bold shadow-[0_10px_0_0_#1e40af] active:shadow-none active:translate-y-2 transition-all flex items-center justify-center">
            <span class="text-6xl drop-shadow-md">◆</span>
        </button>
        <button onclick="submitAns(3)" class="bg-yellow-500 rounded-3xl p-6 text-2xl font-bold shadow-[0_10px_0_0_#854d0e] active:shadow-none active:translate-y-2 transition-all flex items-center justify-center">
            <span class="text-6xl drop-shadow-md">●</span>
        </button>
        <button onclick="submitAns(4)" class="bg-green-500 rounded-3xl p-6 text-2xl font-bold shadow-[0_10px_0_0_#166534] active:shadow-none active:translate-y-2 transition-all flex items-center justify-center">
            <span class="text-6xl drop-shadow-md">■</span>
        </button>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const pin = urlParams.get('pin');
        const nick = urlParams.get('nick') || localStorage.getItem('quiz_nickname') || 'Joueur';
        
        document.getElementById('player-nick').innerText = nick;

        let answered = false;
        let lastQIndex = -1; 
        let startTime = 0;
        let correctAnsId = 1;

        const funnyPhrases = [
            "Enregistré ! Croise les doigts...",
            "T'es un rapide toi !",
            "C'est noté, champion !",
            "Réponse verrouillée.",
            "Plus qu'à attendre..."
        ];

        function sync() {
            fetch(`api_live.php?action=get_state&pin=${pin}`)
            .then(r => r.json())
            .then(data => {
                
                // 1. MISE À JOUR DE L'AVATAR ET DU BADGE VIP
                if(!document.getElementById('my-avatar').innerHTML.includes('img') && data.players) {
                    const me = data.players.find(p => p.nickname === nick);
                    if(me) {
                        let zAura = (me.aura == 1 || me.aura == 5) ? 30 : 5;
                        let auraHtml = me.aura > 0 ? `<img src="personnage/aura/aura${me.aura}.png" class="absolute w-[180%] h-[180%] object-contain animate-pulse" style="z-index: ${zAura}; top:-40%; left:-40%;">` : '';
                        let badge = me.is_member ? `<div class="absolute -bottom-1 -right-1 bg-yellow-400 text-black text-[10px] font-black w-5 h-5 flex items-center justify-center rounded-full border-2 border-white z-40 shadow-sm" title="VIP">★</div>` : '';
                        
                        document.getElementById('my-avatar').innerHTML = `
                            ${auraHtml}
                            <div class="relative w-full h-full overflow-hidden rounded-full flex items-end justify-center">
                                <img src="personnage/tenue/tenue${me.outfit}.png" class="absolute w-[90%] h-[90%] object-contain bottom-0" style="z-index: 10;">
                                <img src="personnage/cheveux/cheveux${me.hair}.png" class="absolute w-[90%] h-[90%] object-contain bottom-0" style="z-index: 20;">
                            </div>
                            ${badge}
                        `;
                    }
                }

                // 2. MISE À JOUR DU SCORE
                if(data.scores && data.scores[nick] !== undefined) {
                    document.getElementById('my-score').innerText = data.scores[nick];
                }

                // 3. LOGIQUE D'ÉLIMINATION (BATTLE ROYALE)
                if (data.eliminated && data.eliminated.includes(nick)) {
                    document.getElementById('grid').classList.add('hidden');
                    document.getElementById('grid').classList.remove('grid');
                    document.getElementById('msg').classList.remove('hidden');
                    document.getElementById('msg').innerHTML = "<span class='text-red-500 text-5xl'>ÉLIMINÉ 💀</span><br><span class='text-sm mt-4 block text-gray-400 normal-case'>Regarde la fin sur l'écran principal</span>";
                    return; // Arrête la boucle pour ce joueur
                }

                // 4. GESTION DES PHASES DE JEU
                if (data.status === 'reveal') {
                    if(lastQIndex !== data.current_q_index) {
                        lastQIndex = data.current_q_index;
                        answered = false;
                        startTime = 0;
                        correctAnsId = data.question.correct_answer;
                    }
                    document.getElementById('grid').classList.add('hidden');
                    document.getElementById('grid').classList.remove('grid');
                    document.getElementById('msg').classList.remove('hidden');
                    document.getElementById('msg').innerText = "PRÉPAREZ-VOUS !";
                } 
                else if (data.status === 'playing') {
                    if (!answered) {
                        document.getElementById('msg').classList.add('hidden');
                        document.getElementById('grid').classList.remove('hidden');
                        document.getElementById('grid').classList.add('grid');
                        if (startTime === 0) startTime = Date.now();
                    }
                } 
                else if (data.status === 'leaderboard') {
                    document.getElementById('grid').classList.add('hidden');
                    document.getElementById('grid').classList.remove('grid');
                    document.getElementById('msg').classList.remove('hidden');
                    document.getElementById('msg').innerHTML = "TEMPS ÉCOULÉ !<br><span class='text-lg font-normal text-yellow-400 mt-2 block normal-case'>Regardez le classement sur l'écran !</span>";
                }
                else if (data.status === 'finished') {
                    document.getElementById('grid').classList.add('hidden');
                    document.getElementById('grid').classList.remove('grid');
                    document.getElementById('msg').classList.remove('hidden');
                    document.getElementById('msg').innerHTML = "PARTIE TERMINÉE !<br><span class='text-sm font-normal normal-case'>Redirection en cours...</span>";
                    setTimeout(() => { window.location.href = "dashboard.php"; }, 5000);
                }
            })
            .catch(err => console.error(err));
        }

        function submitAns(num) {
            if (answered) return;
            answered = true;

            const responseTime = (Date.now() - startTime) / 1000;
            const isCorrect = (num == correctAnsId);

            fetch(`api_live.php?action=submit_answer&pin=${pin}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    nickname: nick, 
                    is_correct: isCorrect, 
                    response_time: responseTime, 
                    answer_index: num 
                })
            }).then(() => {
                document.getElementById('grid').classList.add('hidden');
                document.getElementById('grid').classList.remove('grid');
                document.getElementById('msg').classList.remove('hidden');
                document.getElementById('msg').innerText = funnyPhrases[Math.floor(Math.random() * funnyPhrases.length)];
            });
        }

        setInterval(sync, 1500);
        sync();
    </script>
</body>
</html>