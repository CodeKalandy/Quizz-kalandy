<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Bernard Quizz - Play</title>
</head>
<body class="bg-indigo-900 text-white font-sans flex flex-col h-screen overflow-hidden p-4">
    
    <div id="status-bar" class="p-2 bg-black bg-opacity-30 text-center text-xs font-bold italic mb-4 rounded-full shadow-inner">
        Chargement...
    </div>

    <div id="msg" class="flex-grow flex items-center justify-center text-3xl font-black text-center italic uppercase p-6 animate-pulse">
        Préparez-vous !
    </div>
    
    <div id="grid" class="hidden grid grid-cols-1 gap-4 h-3/4 pb-4">
        <button onclick="submitAns(1)" class="bg-red-500 rounded-3xl p-6 text-2xl font-bold shadow-[0_10px_0_0_#991b1b] active:shadow-none active:translate-y-2 transition-all flex items-center justify-center">
            <span class="text-6xl">▲</span>
        </button>
        <button onclick="submitAns(2)" class="bg-blue-500 rounded-3xl p-6 text-2xl font-bold shadow-[0_10px_0_0_#1e40af] active:shadow-none active:translate-y-2 transition-all flex items-center justify-center">
            <span class="text-6xl">◆</span>
        </button>
        <button onclick="submitAns(3)" class="bg-yellow-500 rounded-3xl p-6 text-2xl font-bold shadow-[0_10px_0_0_#854d0e] active:shadow-none active:translate-y-2 transition-all flex items-center justify-center">
            <span class="text-6xl">●</span>
        </button>
        <button onclick="submitAns(4)" class="bg-green-500 rounded-3xl p-6 text-2xl font-bold shadow-[0_10px_0_0_#166534] active:shadow-none active:translate-y-2 transition-all flex items-center justify-center">
            <span class="text-6xl">■</span>
        </button>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const pin = urlParams.get('pin');
        const nick = urlParams.get('nick') || localStorage.getItem('quiz_nickname') || 'Joueur';
        
        document.getElementById('status-bar').innerText = `${nick} | PIN: ${pin}`;

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
                
                // Si éliminé au Battle Royale
                if (data.eliminated && data.eliminated.includes(nick)) {
                    document.getElementById('grid').classList.add('hidden');
                    document.getElementById('msg').classList.remove('hidden');
                    document.getElementById('msg').innerHTML = "<span class='text-red-500 text-5xl'>ÉLIMINÉ 💀</span><br><span class='text-sm mt-4 block text-gray-400'>Regarde la fin sur l'écran principal</span>";
                    return; // Arrête la logique ici pour ce joueur
                }

                if (data.status === 'reveal') {
                    if(lastQIndex !== data.current_q_index) {
                        lastQIndex = data.current_q_index;
                        answered = false;
                        startTime = 0;
                        correctAnsId = data.question.correct_answer;
                    }
                    document.getElementById('grid').classList.add('hidden');
                    document.getElementById('msg').classList.remove('hidden');
                    document.getElementById('msg').innerText = "PRÉPAREZ-VOUS !";
                } 
                else if (data.status === 'playing') {
                    if (!answered) {
                        document.getElementById('msg').classList.add('hidden');
                        document.getElementById('grid').classList.remove('hidden');
                        if (startTime === 0) startTime = Date.now();
                    }
                } 
                else if (data.status === 'leaderboard') {
                    document.getElementById('grid').classList.add('hidden');
                    document.getElementById('msg').classList.remove('hidden');
                    document.getElementById('msg').innerHTML = "TEMPS ÉCOULÉ !<br><span class='text-lg font-normal text-yellow-400 mt-2 block'>Regardez le classement sur l'écran !</span>";
                }
                else if (data.status === 'finished') {
                    document.getElementById('grid').classList.add('hidden');
                    document.getElementById('msg').classList.remove('hidden');
                    document.getElementById('msg').innerHTML = "PARTIE TERMINÉE !<br><span class='text-sm font-normal'>Redirection en cours...</span>";
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
                document.getElementById('msg').classList.remove('hidden');
                document.getElementById('msg').innerText = funnyPhrases[Math.floor(Math.random() * funnyPhrases.length)];
            });
        }

        setInterval(sync, 1500);
        sync();
    </script>
</body>
</html>