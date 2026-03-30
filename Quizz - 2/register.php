<?php
require_once 'db.php'; 

$error = '';
$success = '';

// --- TA LOGIQUE D'INSCRIPTION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    if ($username && $password && $password_confirm) {
        if ($password === $password_confirm) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = "Ce pseudo est déjà pris ! 😔";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                if($stmt->execute([$username, $hash])) {
                    $success = "Compte créé avec succès ! 🎉 Connecte-toi maintenant.";
                } else {
                    $error = "Une erreur est survenue. ❌";
                }
            }
        } else {
            $error = "Les mots de passe ne correspondent pas ! 🔐";
        }
    } else {
        $error = "Il manque des informations ! 📝";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap" rel="stylesheet">
    <title>Bernard Quizz - Inscription</title>
    <style>
        /* === THEME GLOBAL (GAME UI) === */
        body {
            margin: 0; padding: 0; width: 100vw; min-height: 100vh; overflow-x: hidden;
            background-color: #0f172a; 
            background-image: 
                radial-gradient(at 0% 0%, #1e1b4b 0px, transparent 50%),
                radial-gradient(at 100% 100%, #312e81 0px, transparent 50%);
            background-attachment: fixed; color: white; font-family: sans-serif;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
        }
        
        .game-card {
            background-color: #1e1b4b; border: 4px solid #312e81; border-radius: 2rem;
            box-shadow: 0 12px 0 0 #0b0f19;
        }

        .title-text { font-family: 'Caveat', cursive; text-shadow: 3px 3px 0px #312e81; letter-spacing: 2px; }

        .input-game {
            width: 100%; padding: 1rem 1.2rem; background-color: #0f172a; border: 4px solid #312e81; 
            border-radius: 1.2rem; font-weight: 900; color: white; font-size: 1.1rem;
            outline: none; transition: all 0.2s; box-shadow: inset 0 4px 10px rgba(0,0,0,0.5);
            text-align: center;
        }
        .input-game:focus { border-color: #ec4899; }
        .input-game::placeholder { color: #475569; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }

        .play-btn {
            background-color: #ec4899; color: white; border: 4px solid #be185d;
            box-shadow: 0 8px 0 0 #831843; border-radius: 1.5rem;
            font-weight: 900; font-size: 1.5rem; text-transform: uppercase;
            padding: 1rem 2rem; width: 100%; transition: all 0.1s; letter-spacing: 2px;
            text-shadow: 2px 2px 0px #831843; cursor: pointer; display: block; text-align: center;
        }
        .play-btn:hover { background-color: #f472b6; }
        .play-btn:active { transform: translateY(8px); box-shadow: 0 0px 0 0 #831843; }

        .secondary-btn {
            background-color: #3b82f6; color: white; border: 3px solid #1d4ed8;
            box-shadow: 0 5px 0 0 #1e3a8a; border-radius: 1rem;
            font-weight: 800; font-size: 0.9rem; text-transform: uppercase;
            padding: 0.8rem 1.5rem; transition: all 0.1s; letter-spacing: 1px;
            text-align: center; display: inline-block; cursor: pointer; width: 100%;
        }
        .secondary-btn:hover { background-color: #60a5fa; }
        .secondary-btn:active { transform: translateY(5px); box-shadow: 0 0px 0 0 #1e3a8a; }

        /* Animations */
        .floating { animation: float 4s ease-in-out infinite; }
        .floating-delayed-1 { animation: float 5s ease-in-out infinite 1s; }
        @keyframes float { 0%, 100% { transform: translateY(0px) rotate(var(--rot, 0deg)); } 50% { transform: translateY(-15px) rotate(var(--rot, 0deg)); } }

        .particle { position: fixed; background: rgba(255,255,255,0.02); border-radius: 50%; animation: drift infinite linear; pointer-events: none; z-index: 0; }
        @keyframes drift { from { transform: translateY(100vh) rotate(0deg); } to { transform: translateY(-100vh) rotate(360deg); } }

        .avatar-container { display: flex; flex-direction: column; align-items: center; }
        .float-bubble { position: relative; backdrop-filter: blur(8px); border: 3px solid; border-radius: 1.5rem; padding: 0.5rem 1rem; font-weight: 900; box-shadow: 0 6px 0 0 #0b0f19, 0 10px 20px rgba(0,0,0,0.5); white-space: nowrap; margin-bottom: 12px; }
        .float-bubble::after { content: ''; position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); border-left: 10px solid transparent; border-right: 10px solid transparent; border-top: 10px solid var(--bubble-bg); }
        .float-avatar { width: 70px; height: 70px; border-radius: 1.5rem; border: 4px solid #facc15; background-color: #e0e7ff; box-shadow: 0 6px 0 0 #ca8a04, 0 10px 20px rgba(0,0,0,0.5); object-fit: cover; }
    </style>
</head>
<body class="p-4 relative">

    <div class="particle" style="width: 150px; height: 150px; left: 15%; animation-duration: 20s;"></div>
    <div class="particle" style="width: 250px; height: 250px; left: 75%; animation-duration: 30s;"></div>
    <div class="particle" style="width: 60px; height: 60px; left: 45%; animation-duration: 15s;"></div>

    <div class="absolute top-12 left-4 md:left-24 floating z-0 avatar-container hidden md:flex" style="--rot: -8deg;">
        <div class="float-bubble border-pink-500 text-pink-200" style="--bubble-bg: #831843; background-color: var(--bubble-bg);">
            Rejoins-nous ! 🎉
        </div>
        <img src="images/avatar1.png" class="float-avatar" alt="Avatar">
    </div>

    <div class="absolute bottom-32 right-4 md:right-24 floating-delayed-1 z-0 avatar-container hidden md:flex" style="--rot: 10deg;">
        <div class="float-bubble border-yellow-500 text-yellow-200" style="--bubble-bg: #422006; background-color: var(--bubble-bg);">
            C'est rapide ! ⏱️
        </div>
        <img src="images/avatar3.png" class="float-avatar" alt="Avatar">
    </div>

    <div class="absolute top-4 left-4 z-20">
        <a href="index.php" class="bg-[#1e1b4b] border-2 border-[#312e81] text-indigo-300 px-4 py-2 rounded-xl font-black text-sm uppercase tracking-wider shadow-[0_4px_0_0_#0b0f19] hover:text-white transition-all active:translate-y-1 active:shadow-none inline-block">
            ◀ Retour
        </a>
    </div>

    <div class="relative z-10 w-full max-w-sm flex flex-col items-center mt-12 md:mt-0">
        
        <div class="game-card p-6 md:p-8 w-full flex flex-col items-center mt-8">
            
            <h2 class="title-text text-4xl text-white mb-2 uppercase text-center leading-none">Inscription</h2>
            <p class="text-pink-400 font-bold text-sm uppercase tracking-widest mb-6 text-center">Deviens un membre</p>
            
            <?php if($error): ?>
                <div class="bg-red-900/80 border-2 border-red-500 text-red-200 font-black text-sm p-3 rounded-xl mb-4 w-full text-center shadow-[0_4px_0_0_#7f1d1d]">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="bg-green-900/80 border-2 border-green-500 text-green-200 font-black text-sm p-3 rounded-xl mb-4 w-full text-center shadow-[0_4px_0_0_#14532d]">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" class="w-full space-y-4">
                <div>
                    <input type="text" name="username" placeholder="TON PSEUDO" required autocomplete="username" class="input-game">
                </div>
                <div>
                    <input type="password" name="password" placeholder="MOT DE PASSE" required class="input-game">
                </div>
                <div>
                    <input type="password" name="password_confirm" placeholder="CONFIRMER" required class="input-game">
                </div>

                <div class="pt-4">
                    <button type="submit" class="play-btn">
                        M'inscrire !
                    </button>
                </div>
            </form>

            <div class="w-full flex items-center gap-4 my-6">
                <div class="h-1 bg-[#312e81] flex-1 rounded-full"></div>
                <span class="text-indigo-300 font-black uppercase text-[10px] tracking-widest">Déjà un compte ?</span>
                <div class="h-1 bg-[#312e81] flex-1 rounded-full"></div>
            </div>

            <div class="w-full">
                <a href="login.php" class="secondary-btn">
                    Se connecter
                </a>
            </div>
        </div>
    </div>

    <div class="mt-auto pt-8 pb-2 w-full text-center text-[10px] md:text-xs font-bold tracking-widest uppercase text-indigo-300/50 z-20 pointer-events-auto px-4 relative">
        <div class="flex justify-center gap-4 md:gap-8 mb-1">
            <a href="documentation.php" class="hover:text-yellow-400 transition-colors">Documentation</a>
            <a href="mentions_legales.php" class="hover:text-yellow-400 transition-colors">Mentions Légales & CGU</a>
        </div>
        <p class="leading-tight">
            Création des personnages basée sur le projet open-source pinknose.me<br>
            © 2026 Bernard Quizz. Tous droits réservés.
        </p>
    </div>

</body>
</html>