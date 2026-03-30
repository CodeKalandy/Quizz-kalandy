<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Bernard Quizz - Accueil</title>
</head>
<body class="bg-indigo-900 flex flex-col items-center justify-center min-h-screen text-white font-sans relative overflow-hidden">

    <div class="absolute top-10 left-4 md:left-20 opacity-80 animate-bounce" style="animation-duration: 3.5s;">
        <div class="bg-white text-indigo-900 px-3 py-2 rounded-2xl rounded-bl-none font-black text-xs shadow-lg mb-2">C'est quoi la capitale ?</div>
        <div class="relative w-16 h-16">
            <img src="personnage/tenue/tenue2.png" class="absolute bottom-0 w-full h-full object-contain z-10">
            <img src="personnage/cheveux/cheveux4.png" class="absolute bottom-0 w-full h-full object-contain z-20">
        </div>
    </div>

    <div class="absolute bottom-24 right-4 md:right-32 opacity-80 animate-bounce" style="animation-duration: 4.2s; animation-delay: 1s;">
        <div class="bg-white text-indigo-900 px-3 py-2 rounded-2xl rounded-br-none font-black text-xs shadow-lg mb-2 text-right">C'est la réponse B !</div>
        <div class="relative w-16 h-16 ml-auto">
            <img src="personnage/tenue/tenue5.png" class="absolute bottom-0 w-full h-full object-contain z-10">
            <img src="personnage/cheveux/cheveux7.png" class="absolute bottom-0 w-full h-full object-contain z-20">
        </div>
    </div>

    <div class="max-w-md w-full p-6 bg-white rounded-3xl shadow-2xl text-gray-900 relative z-10 mt-10 mx-4">
        
        <img src="images/logo.png" alt="Logo Bernard Quizz" class="w-32 h-32 mx-auto mb-2 object-contain drop-shadow-xl hover:scale-110 transition-transform">
        <h1 class="text-4xl font-black text-center text-indigo-700 mb-8 italic tracking-tighter">BERNARD QUIZZ</h1>
        
        <div class="mb-6">
            <input type="text" id="pin" placeholder="Code PIN à 6 chiffres" 
                   class="w-full p-4 border-4 border-gray-100 rounded-2xl text-center text-2xl font-black tracking-widest focus:border-indigo-500 outline-none transition-colors">
            <button onclick="joinGame()" class="w-full mt-4 bg-indigo-600 text-white p-4 rounded-2xl font-black text-xl hover:bg-indigo-700 transition shadow-lg active:scale-95">
                REJOINDRE LE SALON
            </button>
        </div>

        <div class="flex flex-col gap-3 pt-6 border-t-2 border-gray-100">
            <a href="login" class="text-center p-3 bg-gray-100 text-gray-700 rounded-xl font-black hover:bg-gray-200 transition">
                Se connecter
            </a>
            <a href="register" class="text-center text-indigo-500 font-bold text-sm hover:underline">
                Créer un compte 
            </a>
        </div>
        
        <div class="mt-8 p-6 bg-yellow-300 text-yellow-900 rounded-2xl transform rotate-2 shadow-xl border-4 border-yellow-400" style="font-family: 'Caveat', cursive; font-size: 1.4rem; line-height: 1.2;">
            ✨ Psst... Crée un compte (c'est gratuit) pour devenir VIP !<br>
            Tu auras accès à :<br>
            - La sauvegarde de ton Bernard favori 👕<br>
            - Les Effets Lévitation & Arc-en-ciel 🌈<br>
            - Un Joker 50/50 exclusif 🃏<br>
            - Toutes tes statistiques classées 📈 !
        </div>
    </div>

    <div class="absolute bottom-4 flex flex-col items-center gap-2 text-xs font-bold text-indigo-300 opacity-80 z-0">
        <div class="flex gap-6">
            <a href="documentation" class="hover:text-white transition">📖 Documentation</a>
            <a href="mentions_legales" class="hover:text-white transition">⚖️ Mentions Légales</a>
        </div>
        <p class="mt-2 text-center opacity-60">
            Les visuels des Bernard sont propulsés avec amour par le code Open-Source de <a href="https://pinknose.me" target="_blank" class="text-white hover:underline">pinknose.me</a>.
        </p>
    </div>

    <script>
        function joinGame() {
            const pin = document.getElementById('pin').value;
            if(pin.length === 6) {
                window.location.href = "lobby?pin=" + pin;
            } else {
                alert("Veuillez entrer un code à 6 chiffres.");
            }
        }
    </script>
</body>
</html>