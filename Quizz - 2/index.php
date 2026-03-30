<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Bernard Quizz - Accueil</title>
    <style>
        @keyframes float { 
            0%, 100% { transform: translateY(0) rotate(0deg); } 
            50% { transform: translateY(-20px) rotate(5deg); } 
        }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-float-delayed { animation: float 7s ease-in-out infinite; animation-delay: 2s; }
        .animate-float-fast { animation: float 4s ease-in-out infinite; animation-delay: 1s; }
    </style>
</head>
<body class="bg-indigo-900 flex flex-col items-center justify-center min-h-screen text-white font-sans relative overflow-hidden">

    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute top-10 left-10 text-8xl text-white/5 font-black animate-pulse">?</div>
        <div class="absolute bottom-20 right-20 text-9xl text-white/5 font-black animate-float">!</div>
        <div class="absolute top-1/4 right-1/4 text-7xl text-purple-500/20 font-black animate-float-fast">✦</div>
        <div class="absolute bottom-1/3 left-1/4 text-8xl text-pink-500/10 font-black animate-float-delayed">⬢</div>
        <div class="absolute top-20 right-10 text-6xl text-cyan-500/20 font-black animate-pulse">⬤</div>
        <div class="absolute bottom-10 left-1/3 text-7xl text-orange-500/20 font-black animate-float">■</div>

        <div class="absolute top-16 left-4 md:left-24 opacity-90 animate-bounce" style="animation-duration: 4s;">
            <div class="bg-white text-indigo-900 px-4 py-2 rounded-2xl rounded-bl-none font-black text-sm shadow-xl mb-2 max-w-[160px] border-2 border-indigo-200">
                Quelle est la capitale de l'Australie ? 🤔
            </div>
            <div class="relative w-20 h-20">
                <img src="personnage/tenue/tenue2.png" class="absolute bottom-0 w-full h-full object-contain z-10" onerror="this.style.display='none'">
                <img src="personnage/cheveux/cheveux4.png" class="absolute bottom-0 w-full h-full object-contain z-20" onerror="this.style.display='none'">
            </div>
        </div>

        <div class="absolute bottom-32 right-4 md:right-24 opacity-90 animate-bounce" style="animation-duration: 5s; animation-delay: 1s;">
            <div class="bg-yellow-400 text-yellow-900 px-4 py-2 rounded-2xl rounded-br-none font-black text-sm shadow-xl mb-2 max-w-[160px] ml-auto text-right border-2 border-yellow-500">
                C'est Canberra ! Évidemment ! 💡
            </div>
            <div class="relative w-24 h-24 ml-auto">
                <img src="personnage/tenue/tenue5.png" class="absolute bottom-0 w-full h-full object-contain z-10" onerror="this.style.display='none'">
                <img src="personnage/cheveux/cheveux7.png" class="absolute bottom-0 w-full h-full object-contain z-20" onerror="this.style.display='none'">
            </div>
        </div>

        <div class="absolute top-1/2 left-2 md:left-12 opacity-80 animate-float-fast">
            <div class="bg-purple-600 text-white px-3 py-2 rounded-2xl rounded-bl-none font-bold text-xs shadow-lg mb-1 max-w-[120px] border border-purple-400">
                Il me faut le Joker 50/50... 🃏
            </div>
            <div class="relative w-16 h-16">
                <img src="personnage/tenue/tenue8.png" class="absolute bottom-0 w-full h-full object-contain z-10" onerror="this.style.display='none'">
                <img src="personnage/cheveux/cheveux10.png" class="absolute bottom-0 w-full h-full object-contain z-20" onerror="this.style.display='none'">
            </div>
        </div>
    </div>

    <div class="max-w-md w-full p-6 bg-white rounded-3xl shadow-2xl text-gray-900 relative z-10 mt-10 mx-4">
        
        <img src="images/logo.png" alt="Logo Bernard Quizz" class="w-32 h-32 mx-auto mb-2 object-contain drop-shadow-xl hover:scale-110 transition-transform">
        <h1 class="text-4xl font-black text-center text-indigo-700 mb-8 italic tracking-tighter">BERNARD QUIZZ</h1>
        
        <div class="mb-6">
            <input type="text" id="pin" placeholder="Code PIN à 6 chiffres" 
                   class="w-full p-4 border-4 border-gray-100 rounded-2xl text-center text-2xl font-black tracking-widest focus:border-indigo-500 outline-none transition-colors">
            <button onclick="joinGame()" class="w-full mt-4 bg-indigo-600 text-white p-4 rounded-2xl font-black text-xl hover:bg-indigo-700 transition shadow-lg active:scale-95">
                REJOINDRE LA PARTIE
            </button>
        </div>

        <div class="flex flex-col gap-3 pt-6 border-t-2 border-gray-100">
            <a href="login" class="text-center p-3 bg-gray-100 text-gray-700 rounded-xl font-black hover:bg-gray-200 transition">
                Se connecter
            </a>
            <a href="register" class="text-center text-indigo-500 font-bold text-sm hover:underline">
                Créer un compte VIP (Gratuit)
            </a>
        </div>
        
        <div class="mt-8 p-6 bg-yellow-300 text-yellow-900 rounded-2xl transform rotate-2 shadow-xl border-4 border-yellow-400" style="font-family: 'Caveat', cursive; font-size: 1.4rem; line-height: 1.2;">
            ✨ Psst... Crée un compte pour devenir VIP !<br>
            Tu auras accès à :<br>
            - La sauvegarde de ton Bernard favori 👕<br>
            - Les Auras Lévitation & Arc-en-ciel 🌈<br>
            - Un Joker 50/50 exclusif 🃏<br>
            - Toutes tes stats & titres sauvegardés 📈 !
        </div>
    </div>

    <div class="absolute bottom-4 flex flex-col items-center gap-2 text-xs font-bold text-indigo-300 opacity-80 z-10 w-full px-4 text-center">
        <div class="flex gap-6">
            <a href="documentation" class="hover:text-white transition drop-shadow-md">📖 Documentation</a>
            <a href="mentions_legales" class="hover:text-white transition drop-shadow-md">⚖️ Mentions Légales</a>
        </div>
        <p class="mt-2 opacity-80 drop-shadow-md bg-indigo-900/50 px-4 py-1 rounded-full">
            Les modèles de création de personnages sont basés sur le superbe projet open-source de <a href="https://pinknose.me" target="_blank" class="text-yellow-400 hover:underline">pinknose.me</a>.
        </p>
    </div>

    <script>
        function joinGame() {
            const pin = document.getElementById('pin').value;
            if(pin.length === 6) {
                // Lien propre sans .php
                window.location.href = "lobby?pin=" + pin;
            } else {
                alert("Veuillez entrer un code à 6 chiffres.");
            }
        }
    </script>
</body>
</html>