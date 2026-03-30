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
            0%, 100% { transform: translateY(0) rotate(0deg) scale(1); } 
            50% { transform: translateY(-20px) rotate(5deg) scale(1.05); } 
        }
        @keyframes pulse-slow {
            0%, 100% { opacity: 0.1; transform: scale(1); }
            50% { opacity: 0.3; transform: scale(1.5); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-float-delayed { animation: float 7s ease-in-out infinite; animation-delay: 2s; }
        .animate-float-fast { animation: float 4s ease-in-out infinite; animation-delay: 1s; }
        .orb { position: absolute; border-radius: 50%; filter: blur(80px); z-index: 0; animation: pulse-slow 8s infinite alternate; }
    </style>
</head>
<body class="bg-indigo-900 flex flex-col items-center justify-between min-h-screen text-white font-sans relative overflow-x-hidden">

    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="orb bg-yellow-500 w-96 h-96 top-[-10%] left-[-10%] opacity-20"></div>
        <div class="orb bg-pink-600 w-[30rem] h-[30rem] bottom-[-10%] right-[-10%] opacity-20" style="animation-delay: 3s;"></div>
        <div class="orb bg-cyan-500 w-64 h-64 top-[40%] left-[60%] opacity-10" style="animation-delay: 1s;"></div>

        <div class="absolute top-10 left-10 text-8xl text-white/5 font-black animate-pulse">?</div>
        <div class="absolute bottom-32 right-20 text-9xl text-white/5 font-black animate-float">!</div>
        <div class="absolute top-1/4 right-1/4 text-7xl text-yellow-400/20 font-black animate-float-fast">✦</div>
        <div class="absolute bottom-1/3 left-1/4 text-8xl text-pink-500/20 font-black animate-float-delayed">⬢</div>
        
        <div class="absolute top-16 left-4 md:left-24 opacity-90 animate-float" style="animation-duration: 5s;">
            <div class="bg-white text-indigo-900 px-4 py-2 rounded-2xl rounded-bl-none font-black text-xs shadow-xl mb-2 max-w-[140px] border-2 border-indigo-200">
                Prêt pour le Quizz ? 😎
            </div>
            <div class="relative w-16 h-16">
                <img src="https://codekalandy.github.io/Quizz-kalandy/Quizz%20-%202/personnage/images/sections/Skin/1/1.png" class="absolute bottom-0 w-full h-full object-contain z-10">
                <img src="https://codekalandy.github.io/Quizz-kalandy/Quizz%20-%202/personnage/images/sections/Hair/Front/short/1/11.png" class="absolute bottom-0 w-full h-full object-contain z-20">
            </div>
        </div>

        <div class="absolute bottom-40 right-4 md:right-24 opacity-90 animate-float-fast">
            <div class="bg-yellow-400 text-yellow-900 px-4 py-2 rounded-2xl rounded-br-none font-black text-xs shadow-xl mb-2 max-w-[140px] ml-auto text-right border-2 border-yellow-500">
                J'ai pas révisé... 😱
            </div>
            <div class="relative w-20 h-20 ml-auto">
                <img src="https://codekalandy.github.io/Quizz-kalandy/Quizz%20-%202/personnage/images/sections/Skin/1/4.png" class="absolute bottom-0 w-full h-full object-contain z-10">
                <img src="https://codekalandy.github.io/Quizz-kalandy/Quizz%20-%202/personnage/images/sections/Hair/Front/short/4/19.png" class="absolute bottom-0 w-full h-full object-contain z-20">
            </div>
        </div>
    </div>

    <div class="flex-grow flex flex-col items-center justify-center w-full z-10 px-4 pt-10">
        <div class="max-w-md w-full p-8 bg-white/10 backdrop-blur-lg rounded-[2rem] shadow-2xl border border-white/20 text-center relative">
            
            <img src="images/logo.png" alt="Logo Bernard Quizz" class="w-32 h-32 mx-auto mb-2 object-contain drop-shadow-2xl hover:scale-110 transition-transform duration-300">
            <h1 class="text-5xl font-black text-yellow-400 mb-8 tracking-tighter" style="font-family: 'Caveat', cursive; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">BERNARD QUIZZ</h1>
            
            <div class="mb-8 relative group">
                <input type="text" id="pin" placeholder="CODE PIN (6 CHIFFRES)" maxlength="6" 
                       class="w-full p-5 bg-white text-indigo-900 border-none rounded-2xl text-center text-2xl font-black tracking-[0.2em] focus:ring-4 focus:ring-yellow-400 outline-none transition-all shadow-inner">
                <button onclick="joinGame()" class="w-full mt-4 bg-yellow-400 text-indigo-900 p-5 rounded-2xl font-black text-xl hover:bg-yellow-300 transition shadow-[0_6px_0_0_#ca8a04] active:shadow-none active:translate-y-1.5 uppercase tracking-widest">
                    Rejoindre
                </button>
            </div>

            <div class="flex flex-col gap-3 pt-6 border-t border-white/20">
                <a href="login" class="w-full p-4 bg-indigo-600/50 hover:bg-indigo-500/80 text-white rounded-xl font-black transition border border-indigo-400/50 uppercase tracking-wider text-sm shadow-md">
                    Se connecter
                </a>
                <a href="register" class="w-full p-4 bg-transparent border-2 border-white/30 hover:bg-white/10 text-white rounded-xl font-bold transition text-sm">
                    Créer un compte Gratuit
                </a>
            </div>
            
        </div>
    </div>

    <div class="w-full bg-indigo-950/80 border-t border-indigo-500/30 py-6 px-4 z-10 mt-10 backdrop-blur-md">
        <div class="max-w-4xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            
            <div class="flex gap-6 text-sm font-bold text-indigo-300">
                <a href="documentation" class="hover:text-yellow-400 transition flex items-center gap-2"><span>📖</span> Documentation</a>
                <a href="mentions_legales" class="hover:text-yellow-400 transition flex items-center gap-2"><span>⚖️</span> Mentions Légales</a>
            </div>

            <div class="text-xs text-indigo-400/80 font-medium text-center md:text-right">
                <p>Création des personnages basée sur le projet open-source <a href="https://pinknose.me" target="_blank" class="text-yellow-500/80 hover:text-yellow-400 hover:underline">pinknose.me</a></p>
                <p class="mt-1 opacity-50">&copy; <?= date('Y') ?> Bernard Quizz. Tous droits réservés.</p>
            </div>
            
        </div>
    </div>

    <script>
        function joinGame() {
            const pin = document.getElementById('pin').value.trim();
            if(pin.length === 6) {
                window.location.href = "lobby?pin=" + pin;
            } else {
                alert("Veuillez entrer un code PIN à 6 chiffres.");
            }
        }
    </script>
</body>
</html>