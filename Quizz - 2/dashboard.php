<?php
require_once 'db.php';

// Sécurisation de la page
if (!isset($_SESSION['user_id'])) {
    header('Location: login');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = htmlspecialchars($_SESSION['username']);

// Récupérer le rôle de l'utilisateur pour gérer les permissions d'affichage
$stmtRole = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmtRole->execute([$user_id]);
$user_role = $stmtRole->fetchColumn();

// Stocker le role en session si absent (compatibilité)
if (!isset($_SESSION['role'])) { $_SESSION['role'] = $user_role; }

// Autorisations
$can_create_quiz = in_array($user_role, ['createur', 'admin', 'fondateur']);
$can_admin       = in_array($user_role, ['admin', 'fondateur']);

// Récupérer le nombre de quizz de ce user
$stmt = $pdo->prepare("SELECT COUNT(*) FROM quizzes WHERE user_id = ?");
$stmt->execute([$user_id]);
$quiz_count = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap" rel="stylesheet">
    <title>Bernard Quizz - Mon Espace</title>
    <style>
        /* === THEME GLOBAL (GAME UI) === */
        body {
            margin: 0; padding: 0; width: 100vw; min-height: 100vh; overflow-x: hidden;
            background-color: #0f172a; 
            background-image: 
                radial-gradient(at 0% 0%, #1e1b4b 0px, transparent 50%),
                radial-gradient(at 100% 100%, #312e81 0px, transparent 50%);
            background-attachment: fixed; color: white; font-family: sans-serif;
            display: flex; flex-direction: column; align-items: center;
        }
        
        .game-card {
            background-color: #1e1b4b; border: 4px solid #312e81; border-radius: 2rem;
            box-shadow: 0 8px 0 0 #0b0f19; transition: transform 0.2s;
        }

        .title-text { font-family: 'Caveat', cursive; text-shadow: 3px 3px 0px #312e81; letter-spacing: 2px; }

        /* Boutons de Menu Dashboard */
        .dash-btn {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            border-radius: 1.5rem; font-weight: 900; text-transform: uppercase;
            padding: 2rem 1rem; width: 100%; transition: all 0.1s; letter-spacing: 1px;
            cursor: pointer; text-shadow: 2px 2px 0px rgba(0,0,0,0.3); text-align: center;
            height: 100%;
        }
        .dash-btn:active { transform: translateY(8px); box-shadow: 0 0px 0 0 rgba(0,0,0,0.5) !important; }

        .btn-green { background-color: #10b981; border: 4px solid #047857; box-shadow: 0 8px 0 0 #064e3b; color: white; }
        .btn-green:hover { background-color: #34d399; }

        .btn-blue { background-color: #3b82f6; border: 4px solid #1d4ed8; box-shadow: 0 8px 0 0 #1e3a8a; color: white; }
        .btn-blue:hover { background-color: #60a5fa; }

        .btn-yellow { background-color: #facc15; border: 4px solid #ca8a04; box-shadow: 0 8px 0 0 #854d0e; color: #422006; text-shadow: none; }
        .btn-yellow:hover { background-color: #fde047; }

        .btn-purple { background-color: #9333ea; border: 4px solid #6b21a8; box-shadow: 0 8px 0 0 #581c87; color: white; }
        .btn-purple:hover { background-color: #a855f7; }

        .btn-red { background-color: #dc2626; border: 4px solid #991b1b; box-shadow: 0 8px 0 0 #7f1d1d; color: white; }
        .btn-red:hover { background-color: #ef4444; }

        .particle { position: fixed; background: rgba(255,255,255,0.02); border-radius: 50%; animation: drift infinite linear; pointer-events: none; z-index: 0; }
        @keyframes drift { from { transform: translateY(100vh) rotate(0deg); } to { transform: translateY(-100vh) rotate(360deg); } }
    </style>
</head>
<body class="p-4 md:p-8 relative">

    <div class="particle" style="width: 150px; height: 150px; left: 10%; animation-duration: 25s;"></div>
    <div class="particle" style="width: 250px; height: 250px; left: 80%; animation-duration: 35s;"></div>
    <div class="particle" style="width: 80px; height: 80px; left: 50%; animation-duration: 20s;"></div>

    <div class="relative z-10 w-full max-w-5xl flex flex-col items-center mt-4">
        
        <div class="w-full flex flex-col md:flex-row items-center justify-between game-card p-6 md:p-8 mb-8">
            <div class="flex items-center gap-6 mb-4 md:mb-0">
                <div class="w-24 md:w-32 flex items-center justify-center" style="aspect-ratio: 819/654;">
                    <img src="images/logo.png" alt="Bernard Quizz" class="w-full h-full object-contain drop-shadow-[0_10px_20px_rgba(0,0,0,0.5)]">
                </div>
                <div>
                    <p class="text-indigo-400 font-black text-sm uppercase tracking-widest">Bienvenue,</p>
                    <h1 class="title-text text-4xl text-white"><?= $username ?></h1>
                </div>
            </div>
            <div>
                <a href="logout" class="bg-red-500 border-4 border-red-700 text-white px-6 py-3 rounded-xl font-black uppercase tracking-wider shadow-[0_6px_0_0_#7f1d1d] hover:bg-red-400 transition-all active:translate-y-2 active:shadow-none inline-block">
                    Déconnexion 🚪
                </a>
            </div>
        </div>

        <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 max-w-4xl mx-auto">

            <!-- Widget Rejoindre -->
            <div class="game-card p-6 flex flex-col items-center justify-center" style="min-height:180px;">
                <span class="text-4xl mb-3">🎮</span>
                <p class="title-text text-2xl text-green-400 mb-1">Rejoindre</p>
                <p class="text-indigo-300 text-xs font-bold uppercase tracking-widest mb-4">Entre le code PIN de la partie</p>
                <input type="text" id="pin-input"
                    class="w-full bg-[#0f172a] border-4 border-[#312e81] rounded-2xl font-black text-center text-white text-3xl tracking-[8px] p-3 outline-none focus:border-yellow-400 transition-all placeholder-indigo-700 mb-3"
                    placeholder="• • • • • •" maxlength="6" inputmode="numeric">
                <button onclick="joinGame()"
                    class="w-full bg-green-500 border-4 border-green-700 text-white font-black text-lg uppercase tracking-widest py-3 rounded-2xl shadow-[0_6px_0_0_#064e3b] hover:bg-green-400 transition-all active:translate-y-2 active:shadow-none"
                    style="text-shadow:2px 2px 0 #065f46">
                    Rejoindre !
                </button>
            </div>

            <!-- Profil -->
            <a href="profil" class="dash-btn btn-purple" style="min-height:180px;">
                <span class="text-4xl mb-2">👤</span>
                <span class="text-xl">Mon<br>Profil</span>
            </a>

        </div>

        <!-- Boutons créateurs / admin (si applicable) -->
        <?php if ($can_create_quiz): ?>
        <div class="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-<?= $can_admin ? '4' : '3' ?> gap-6 mb-12 max-w-4xl mx-auto">


            <a href="manage_quizzes" class="dash-btn btn-blue" style="min-height:140px;">
                <span class="text-3xl mb-2">📂</span>
                <span class="text-base">Gérer<br>Les Quizz</span>
                <span class="mt-2 text-xs bg-blue-900/50 px-3 py-1 rounded-full border border-blue-400"><?= $quiz_count ?> créés</span>
            </a>

            <a href="edit_quiz" class="dash-btn btn-yellow" style="min-height:140px;">
                <span class="text-3xl mb-2">✏️</span>
                <span class="text-base">Créer<br>un Quizz</span>
            </a>

            <?php if ($can_admin): ?>
            <a href="admin_users" class="dash-btn btn-red" style="min-height:140px;">
                <span class="text-3xl mb-2">🛡️</span>
                <span class="text-base">Panel<br>Admin</span>
            </a>
            <?php endif; ?>

        </div>
        <?php else: ?>
        <div class="w-full max-w-4xl mx-auto mb-12">
            <a href="manage_quizzes" class="dash-btn btn-blue w-full" style="min-height:100px;">
                <span class="text-3xl mb-2">📂</span>
                <span class="text-base">Les Quizz</span>
                <span class="mt-2 text-xs bg-blue-900/50 px-3 py-1 rounded-full border border-blue-400"><?= $quiz_count ?> créés</span>
            </a>
        </div>
        <?php endif; ?>

    </div>

    <div class="mt-auto pt-12 pb-4 w-full text-center text-[10px] md:text-xs font-bold tracking-widest uppercase text-indigo-300/50 z-20 pointer-events-auto px-4 relative">
        <div class="flex justify-center gap-4 md:gap-8 mb-2">
            <a href="documentation" class="hover:text-yellow-400 transition-colors">Documentation</a>
            <a href="mentions_legales" class="hover:text-yellow-400 transition-colors">Mentions Légales & CGU</a>
        </div>
        <p class="leading-tight">
            Création des personnages basée sur le projet open-source pinknose.me<br>
            © 2026 Bernard Quizz. Tous droits réservés.
        </p>
    </div>

    <script>
        const pinInput = document.getElementById('pin-input');
        pinInput.addEventListener('input', () => {
            pinInput.value = pinInput.value.replace(/\D/g,'').slice(0,6);
        });
        pinInput.addEventListener('keydown', (e) => { if(e.key==='Enter') joinGame(); });
        function joinGame() {
            const pin = pinInput.value.trim();
            if (pin.length < 4) return pinInput.focus();
            window.location.href = `lobby?pin=${pin}`;
        }
    </script>
</body>
</html>