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
<body class="bg-indigo-900 flex flex-col items-center justify-center min-h-screen text-white font-sans relative">

    <div class="max-w-md w-full p-6 bg-white rounded-3xl shadow-2xl text-gray-900 relative z-10 mt-10">
        
        <img src="images/logo.png" alt="Logo Bernard Quizz" class="w-32 h-32 mx-auto mb-2 object-contain drop-shadow-xl hover:scale-110 transition-transform">
        
        <div class="mb-6">
            <input type="text" id="pin" placeholder="Code PIN à 6 chiffres" 
                   class="w-full p-4 border-4 border-gray-100 rounded-2xl text-center text-2xl font-black tracking-widest focus:border-indigo-500 outline-none transition-colors">
            <button onclick="joinGame()" class="w-full mt-4 bg-indigo-600 text-white p-4 rounded-2xl font-black text-xl hover:bg-indigo-700 transition shadow-lg active:scale-95">
                REJOINDRE LE SALON
            </button>
        </div>

        <div class="flex flex-col gap-3 pt-6 border-t-2 border-gray-100">
            <a href="login.php" class="text-center p-3 bg-gray-100 text-gray-700 rounded-xl font-black hover:bg-gray-200 transition">
                Se connecter
            </a>
            <a href="register.php" class="text-center text-indigo-500 font-bold text-sm hover:underline">
                Créer un compte 
            </a>
        </div>
        
        <div class="mt-8 p-6 bg-yellow-300 text-yellow-900 rounded-2xl transform rotate-2 shadow-xl border-4 border-yellow-400" style="font-family: 'Caveat', cursive; font-size: 1.4rem; line-height: 1.2;">
            ✨ Psst... Crée un compte (c'est gratuit) pour devenir VIP !<br>
            Tu auras accès à :<br>
            - Plus de tenues & coiffures 👕<br>
            - Auras débloquées 🌟<br>
            - Un Joker 50/50 en partie 🃏<br>
            - Toutes tes statistiques sauvegardées 📈 !
        </div>
    </div>

    <div class="absolute bottom-4 text-center w-full flex justify-center gap-6 text-sm font-bold text-indigo-300 opacity-80 z-0">
        <a href="documentation.php" class="hover:text-white transition">📖 Documentation</a>
        <a href="mentions_legales.php" class="hover:text-white transition">⚖️ Mentions Légales</a>
    </div>

    <script>
        function joinGame() {
            const pin = document.getElementById('pin').value;
            if(pin.length === 6) {
                window.location.href = "lobby.php?pin=" + pin;
            } else {
                alert("Veuillez entrer un code à 6 chiffres.");
            }
        }
    </script>
</body>
</html>