<?php
require_once 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index");
    exit;
}

$role = $_SESSION['role'] ?? 'joueur';
$username = htmlspecialchars($_SESSION['username']);

$stmt = $pdo->query("SELECT * FROM quizzes ORDER BY created_at DESC");
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Tableau de Bord - Bernard Quizz</title>
    <style>
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-5px); } }
        .animate-float { animation: float 3s ease-in-out infinite; }
    </style>
</head>
<body class="bg-indigo-900 font-sans text-white min-h-screen p-4 md:p-8 relative overflow-x-hidden">
    
    <div class="fixed top-10 left-10 text-7xl text-white/5 font-black z-0 pointer-events-none">✦</div>
    <div class="fixed bottom-20 right-20 text-9xl text-white/5 font-black z-0 pointer-events-none">⬢</div>
    
    <div class="max-w-6xl mx-auto relative z-10">
        
        <div class="flex flex-col md:flex-row justify-between items-center bg-white/10 backdrop-blur-md p-6 rounded-3xl shadow-2xl border border-white/20 mb-8">
            <div class="flex items-center gap-6 mb-4 md:mb-0">
                <img src="images/logo.png" alt="Logo" class="h-16 animate-float" onerror="this.style.display='none'">
                <div>
                    <h1 class="text-3xl font-black text-yellow-400 uppercase tracking-widest">Bonjour, <?= $username ?> !</h1>
                    <p class="text-xs font-bold text-indigo-300 uppercase tracking-widest bg-indigo-950/50 inline-block px-3 py-1 rounded-full mt-1">
                        Rôle : <?= $role ?>
                    </p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="profil" class="bg-indigo-600 hover:bg-indigo-500 text-white font-black px-6 py-3 rounded-2xl shadow-lg transition transform hover:scale-105 border border-indigo-400">👤 Ma Vitrine</a>
                <a href="logout" class="bg-red-500/20 hover:bg-red-500/40 text-red-300 hover:text-red-100 font-bold px-6 py-3 rounded-2xl transition border border-red-500/30">Déconnexion</a>
            </div>
        </div>

        <?php if ($role === 'createur' || $role === 'admin'): ?>
            <div class="bg-yellow-400 p-1 rounded-3xl shadow-xl mb-10 transform rotate-1 hover:rotate-0 transition duration-300">
                <div class="bg-indigo-900 p-6 rounded-[22px] border border-yellow-400 border-dashed">
                    <h2 class="text-2xl font-black text-yellow-400 uppercase tracking-widest mb-4 flex items-center gap-3">
                        <span class="text-3xl">🛠️</span> Espace Administration
                    </h2>
                    <div class="flex flex-wrap gap-4">
                        <a href="manage_quizzes" class="bg-yellow-400 hover:bg-yellow-300 text-indigo-900 font-black px-6 py-3 rounded-xl shadow-md transition uppercase text-sm tracking-wide">
                            Gérer mes Quiz
                        </a>
                        <?php if ($role === 'admin'): ?>
                            <a href="admin_users" class="bg-indigo-500 hover:bg-indigo-400 text-white font-black px-6 py-3 rounded-xl shadow-md transition uppercase text-sm tracking-wide border border-indigo-300">
                                Gérer les Utilisateurs
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <h2 class="text-3xl font-black text-white uppercase tracking-widest mb-8 border-b border-indigo-500/50 pb-4">🎮 Lancer une partie</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($quizzes as $q): ?>
                <div class="bg-white/5 backdrop-blur-sm rounded-3xl shadow-lg p-6 flex flex-col h-full transform transition hover:-translate-y-2 hover:bg-white/10 border border-white/10 hover:border-indigo-400">
                    <h3 class="text-2xl font-black text-yellow-300 mb-3 leading-tight"><?= htmlspecialchars($q['title']) ?></h3>
                    <p class="text-indigo-200 text-sm mb-8 flex-grow leading-relaxed"><?= htmlspecialchars($q['description']) ?></p>
                    <a href="host_game?quiz_id=<?= $q['id'] ?>" class="text-center bg-white text-indigo-900 font-black py-4 rounded-xl hover:bg-indigo-50 transition shadow-[0_4px_0_0_#a5b4fc] active:shadow-none active:translate-y-1 w-full uppercase tracking-widest text-sm">
                        Héberger ce Quiz
                    </a>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($quizzes)): ?>
                <div class="col-span-full bg-indigo-900/50 border border-indigo-500/30 rounded-3xl p-10 text-center">
                    <p class="text-indigo-300 italic text-lg font-bold">Aucun quiz n'a encore été créé. La salle d'attente est vide !</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>