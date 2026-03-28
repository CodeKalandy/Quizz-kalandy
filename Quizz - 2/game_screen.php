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

    <div id="q-container" class="hidden px-4 mt-8 mb-6 z-0 w-full max-w-4xl mx-auto text-center">
        <h2 id="q-text" class="text-2xl md:text-4xl font-black text-white drop-shadow-md leading-tight"></h2>
    </div>

    <div id="msg" class="flex-grow flex flex-col items-center justify-center text-3xl font-black text-center italic uppercase p-6 animate-pulse z-0">
        Chargement...
    </div>
    
    <div id="grid" class="hidden grid-cols-1 md:grid-cols-2 gap-4 h-[70%] pb-6 px-4 w-full max-w-4xl mx-auto z-0 overflow-y-auto">
        <button onclick="submitAns(1)" class="bg-red-500 rounded-3xl p-6 md:p-8 text-white font-bold shadow-[0_8px_0_0_#991b1b] active:shadow-none active:translate-y-2 transition-all flex items-center text-left">
            <span class="text-5xl md:text-6xl drop-shadow-md mr-6">▲</span>
            <span id="opt1-text" class="text-xl md:text-2xl leading-tight flex-grow drop-shadow-sm"></span>
        </button>
        <button onclick="submitAns(2)" class="bg-blue-500 rounded-3xl p-6 md:p-8 text-white font-bold shadow-[0_8px_0_0_#1e40af] active:shadow-none active:translate-y-2 transition-all flex items-center text-left">
            <span class="text-5xl md:text-6xl drop-shadow-md mr-6">◆</span>
            <span id="opt2-text" class="text-xl md:text-2xl leading-tight flex-grow drop-shadow-sm"></span>
        </button>
        <button onclick="submitAns(3)" class="bg-yellow-500 rounded-3xl p-6 md:p-8 text-white font-bold shadow-[0_8px_0_0_#854d0e] active:shadow-none active:translate-y-2 transition-all flex items-center text-left">
            <span class="text-5xl md:text-6xl drop-shadow-md mr-6">●</span>
            <span id="opt3-text" class="text-xl md:text-2xl leading-tight flex-grow drop-shadow-sm"></span>
        </button>
        <button onclick="submitAns(4)" class="bg-green-500 rounded-3xl p-6 md:p-8 text-white font-bold shadow-[0_8px_0_0_#166534] active:shadow-none active:translate-y-2 transition-all flex items-center text-left">
            <span class="text-5xl md:text-6xl drop-shadow-md mr-6">■</span>
            <span id="opt4-text" class="text-xl md:text-2xl leading-tight flex-grow drop-shadow-sm"></span>
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

        const funnyPhrases = ["Enregistré ! Croise les doigts...", "T'es un rapide toi !", "C'est noté, champion !", "Réponse verrouillée.", "Plus qu'à attendre..."];

        function sync() {
            fetch(`api_live.php?action=get_state&pin=${pin}`)
            .then(r => r.json())
            .then(data => {
                
                if(!document.getElementById('my-avatar').innerHTML.includes('img') && data.players) {
                    const me = data.players.find(p => p.nickname === nick);
                    if(me) {
                        let zAura = (me.aura == 1 || me.aura == 5) ? 30 : 5;
                        let auraHtml = me.aura > 0 ? `<img src="personnage/aura/aura${me.aura}.png" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] object-contain animate-pulse" style="z-index: ${zAura};">` : '';
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

                if(data.scores && data.scores[nick] !== undefined) {
                    document.getElementById('my-score').innerText = data.scores[nick];
                }

                if (data.eliminated && data.eliminated.includes(nick)) {
                    document.getElementById('q-container').classList.add('hidden');
                    document.getElementById('grid').classList.add('hidden');
                    document.getElementById('grid').classList.remove('grid', 'md:grid-cols-2');
                    document.getElementById('msg').classList.remove('hidden');
                    document.getElementById('msg').innerHTML = "<span class='text-red-500 text-5xl'>ÉLIMINÉ 💀</span><br><span class='text-sm mt-4 block text-gray-400 normal-case'>Regarde la fin sur l'écran principal</span>";
                    return;
                }

                if (data.status === 'reveal') {
                    if(lastQIndex !== data.current_q_index) {
                        lastQIndex = data.current_q_index;
                        answered = false;
                        startTime = 0;
                        correctAnsId = data.question.correct_answer;
                    }
                    document.getElementById('q-container').classList.add('hidden');
                    document.getElementById('grid').classList.add('hidden');
                    document.getElementById('grid').classList.remove('grid', 'md:grid-cols-2');
                    document.getElementById('msg').classList.remove('hidden');
                    document.getElementById('msg').innerText = "PRÉPAREZ-VOUS !";
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
                        document.getElementById('grid').classList.remove('hidden');
                        document.getElementById('grid').classList.add('grid', 'md:grid-cols-2');
                        if (startTime === 0) startTime = Date.now();
                    }
                }
                // GESTION DU NOUVEL ÉTAT SHOW_ANSWER SUR LE MOBILE 
                else if (data.status === 'show_answer') {
                    document.getElementById('q-container').classList.add('hidden');
                    document.getElementById('grid').classList.add('hidden');
                    document.getElementById('grid').classList.remove('grid', 'md:grid-cols-2');
                    document.getElementById('msg').classList.remove('hidden');
                    document.getElementById('msg').innerHTML = "FIN DU TEMPS !<br><span class='text-lg font-normal text-yellow-400 mt-2 block normal-case'>Regardez l'écran pour la réponse !</span>";
                }
                else if (data.status === 'leaderboard') {
                    const isLast = data.current_q_index === data.questions_list.length - 1;
                    document.getElementById('q-container').classList.add('hidden');
                    document.getElementById('grid').classList.add('hidden');
                    document.getElementById('grid').classList.remove('grid', 'md:grid-cols-2');
                    document.getElementById('msg').classList.remove('hidden');
                    
                    if (isLast) {
                        document.getElementById('msg').innerHTML = "FIN DES QUESTIONS !<br><span class='text-lg font-normal text-yellow-400 mt-2 block normal-case'>Le podium va apparaître...</span>";
                    } else {
                        document.getElementById('msg').innerHTML = "CLASSEMENT PROVISOIRE<br><span class='text-lg font-normal text-yellow-400 mt-2 block normal-case'>Regardez l'écran principal !</span>";
                    }
                }
                else if (data.status === 'finished') {
                    document.getElementById('q-container').classList.add('hidden');
                    document.getElementById('grid').classList.add('hidden');
                    document.getElementById('grid').classList.remove('grid', 'md:grid-cols-2');
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
                document.getElementById('q-container').classList.add('hidden');
                document.getElementById('grid').classList.add('hidden');
                document.getElementById('grid').classList.remove('grid', 'md:grid-cols-2');
                document.getElementById('msg').classList.remove('hidden');
                document.getElementById('msg').innerText = funnyPhrases[Math.floor(Math.random() * funnyPhrases.length)];
            });
        }

        setInterval(sync, 1500);
        sync();
    </script>
</body>
</html>