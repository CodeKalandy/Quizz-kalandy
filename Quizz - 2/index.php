<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap" rel="stylesheet">
    <title>Bernard Quizz - Rejoins une partie !</title>
    <style>
        /* === THEME GLOBAL (GAME UI) === */
        
        /* Verrouillage absolu du scroll */
        html, body {
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background-color: #0f172a; 
            background-image: 
                radial-gradient(at 0% 0%, #1e1b4b 0px, transparent 50%),
                radial-gradient(at 100% 100%, #312e81 0px, transparent 50%);
            color: white;
        }
        
        .game-card {
            background-color: #1e1b4b; 
            border: 4px solid #312e81;
            border-radius: 2rem;
            box-shadow: 0 12px 0 0 #0b0f19;
        }

        .title-text {
            font-family: 'Caveat', cursive;
            text-shadow: 3px 3px 0px #312e81;
            letter-spacing: 2px;
        }

        /* Bouton Géant type "Jeux Vidéo" */
        .play-btn {
            background-color: #10b981; color: white; border: 4px solid #047857;
            box-shadow: 0 8px 0 0 #064e3b; border-radius: 1.5rem;
            font-weight: 900; font-size: 1.8rem; text-transform: uppercase;
            padding: 1.2rem 2rem; width: 100%; transition: all 0.1s; letter-spacing: 2px;
            text-shadow: 2px 2px 0px #065f46;
            cursor: pointer;
            display: block;
            text-align: center;
        }
        .play-btn:hover { background-color: #34d399; }
        .play-btn:active { transform: translateY(8px); box-shadow: 0 0px 0 0 #064e3b; }

        .secondary-btn {
            background-color: #3b82f6; color: white; border: 3px solid #1d4ed8;
            box-shadow: 0 5px 0 0 #1e3a8a; border-radius: 1rem;
            font-weight: 800; font-size: 1rem; text-transform: uppercase;
            padding: 0.8rem 1.5rem; transition: all 0.1s; letter-spacing: 1px;
            text-align: center; display: inline-block; cursor: pointer;
        }
        .secondary-btn:hover { background-color: #60a5fa; }
        .secondary-btn:active { transform: translateY(5px); box-shadow: 0 0px 0 0 #1e3a8a; }

        /* Animations Fun */
        .floating { animation: float 4s ease-in-out infinite; }
        .floating-delayed-1 { animation: float 4.5s ease-in-out infinite 1s; }
        .floating-delayed-2 { animation: float 5s ease-in-out infinite 0.5s; }
        
        @keyframes float { 
            0%, 100% { transform: translateY(0px) rotate(var(--rot, 0deg)); } 
            50% { transform: translateY(-15px) rotate(var(--rot, 0deg)); } 
        }

        /* Particules de fond */
        .particle {
            position: absolute; background: rgba(255,255,255,0.02); border-radius: 50%;
            animation: drift infinite linear; pointer-events: none; z-index: 0;
        }
        @keyframes drift { from { transform: translateY(100vh) rotate(0deg); } to { transform: translateY(-100vh) rotate(360deg); } }

        /* Style des bulles et avatars flottants */
        .avatar-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .float-bubble {
            position: relative;
            backdrop-filter: blur(8px);
            border: 3px solid;
            border-radius: 1.5rem;
            padding: 0.5rem 1rem;
            font-weight: 900;
            box-shadow: 0 6px 0 0 #0b0f19, 0 10px 20px rgba(0,0,0,0.5);
            white-space: nowrap;
            margin-bottom: 12px; 
        }
        
        /* Queue de la bulle pointant vers l'avatar */
        .float-bubble::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-top: 10px solid var(--bubble-bg); 
        }

        .float-avatar {
            width: 70px; height: 70px;
            border-radius: 1.5rem;
            border: 4px solid #facc15;
            background-color: #e0e7ff;
            box-shadow: 0 6px 0 0 #ca8a04, 0 10px 20px rgba(0,0,0,0.5);
            object-fit: cover;
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center p-4 font-sans relative">

    <div class="particle" style="width: 150px; height: 150px; left: 15%; animation-duration: 20s;"></div>
    <div class="particle" style="width: 250px; height: 250px; left: 75%; animation-duration: 30s;"></div>
    <div class="particle" style="width: 60px; height: 60px; left: 45%; animation-duration: 15s;"></div>

    <div class="absolute top-8 left-4 md:left-16 floating z-0 avatar-container" style="--rot: -8deg;">
        <div class="float-bubble border-yellow-500 text-yellow-400" style="--bubble-bg: #551919; background-color: var(--bubble-bg);">
            C'est quoi cette question ?!
        </div>
        <img src="images/avatar1.png" class="float-avatar" alt="Avatar">
    </div>

    <div class="absolute top-1/4 right-4 md:right-16 floating-delayed-1 z-0 avatar-container hidden sm:flex" style="--rot: 10deg;">
        <div class="float-bubble border-pink-500 text-white" style="--bubble-bg: #535353; background-color: var(--bubble-bg);">
            🗿🗿🗿🗿
        </div>
        <img src="images/avatar2.png" class="float-avatar !w-20 !h-20" alt="Avatar">
    </div>

    <div class="absolute bottom-28 left-8 md:left-24 floating-delayed-2 z-0 avatar-container hidden md:flex" style="--rot: -5deg;">
        <div class="float-bubble border-indigo-400 text-indigo-200" style="--bubble-bg: #714077; background-color: var(--bubble-bg);">
            Aïe, presque !
        </div>
        <img src="images/avatar3.png" class="float-avatar" alt="Avatar">
    </div>

    <div class="relative z-10 w-full max-w-md flex flex-col items-center">
        
        <div class="game-card p-6 md:p-8 w-full flex flex-col items-center text-center">
            
            <div class="w-56 md:w-64 mb-6 flex items-center justify-center" style="aspect-ratio: 819/654;">
                <img src="images/logo.png" alt="Logo Bernard Quizz" class="w-full h-full object-contain drop-shadow-[0_10px_20px_rgba(0,0,0,0.5)]">
            </div>
            
            <h2 class="title-text text-3xl md:text-4xl text-white mb-6 uppercase">Rejoins une partie !</h2>
            
            <form action="lobby.php" method="GET" class="w-full space-y-6">
                <div>
                    <input type="text" name="pin" id="pin" maxlength="6" placeholder="CODE PIN" required autocomplete="off"
                           class="w-full p-4 bg-[#0f172a] border-4 border-[#312e81] rounded-2xl font-black text-center text-white text-3xl focus:border-yellow-400 focus:ring-0 outline-none transition-all shadow-[inset_0_4px_10px_rgba(0,0,0,0.5)] placeholder-gray-500 tracking-[0.5em] uppercase">
                </div>

                <button type="submit" class="play-btn">
                    C'EST PARTI !
                </button>
            </form>

            <div class="w-full flex items-center gap-4 my-6">
                <div class="h-1 bg-[#312e81] flex-1 rounded-full"></div>
                <span class="text-indigo-300 font-black uppercase text-sm">Ou</span>
                <div class="h-1 bg-[#312e81] flex-1 rounded-full"></div>
            </div>

            <div class="w-full">
                <?php if ($is_logged_in): ?>
                    <a href="dashboard.php" class="secondary-btn w-full !bg-purple-600 !border-purple-800 !shadow-[0_5px_0_0_#581c87] hover:!bg-purple-500 py-4 text-lg">
                        Mon Espace Animateur
                    </a>
                <?php else: ?>
                    <div class="flex gap-3 w-full">
                        <a href="login.php" class="secondary-btn flex-1 !text-xs md:!text-sm !py-3">
                            Connexion
                        </a>
                        <a href="register.php" class="secondary-btn flex-1 !text-xs md:!text-sm !py-3 !bg-pink-600 !border-pink-800 !shadow-[0_5px_0_0_#831843] hover:!bg-pink-500">
                            S'inscrire
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="absolute bottom-2 w-full text-center text-[10px] md:text-xs font-bold tracking-widest uppercase text-indigo-300/50 z-20 pointer-events-auto px-4">
        <div class="flex justify-center gap-4 md:gap-8 mb-1">
            <a href="documentation.php" class="hover:text-yellow-400 transition-colors">Documentation</a>
            <a href="mentions_legales.php" class="hover:text-yellow-400 transition-colors">Mentions Légales & CGU</a>
        </div>
        <p class="leading-tight">
            Création des personnages basée sur le projet open-source pinknose.me<br>
            © 2026 Bernard Quizz. Tous droits réservés.
        </p>
    </div>

    <script>
        // Force le texte en majuscule dans le champ PIN
        const pinInput = document.getElementById('pin');
        pinInput.addEventListener('input', function(e) {
            this.value = this.value.toUpperCase();
        });
    </script>
</body>
</html>