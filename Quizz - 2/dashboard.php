<?php
require_once 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index");
    exit;
}

$role = $_SESSION['role'] ?? 'joueur';

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
</head>
<body class="bg-indigo-100 font-sans text-gray-800 min-h-screen p-4 md:p-8">
    <div class="max-w-5xl mx-auto">
        
        <div class="flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-3xl shadow-xl mb-8">
            <div class="flex items-center gap-4 mb-4 md:mb-0">
                <img src="images/logo.png" alt="Logo" class="h-12" onerror="this.style.display='none'">
                <div>
                    <h1 class="text-2xl font-black text-indigo-900 uppercase tracking-widest">Bonjour, <?= htmlspecialchars($_SESSION['username']) ?> !</h1>
                    <p class="text-sm font-bold text-gray-400 uppercase">Rôle : <?= $role ?></p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="profil" class="bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-bold px-5 py-2 rounded-xl transition">👤 Ma Vitrine</a>
                <a href="logout" class="bg-red-100 hover:bg-red-200 text-red-600 font-bold px-5 py-2 rounded-xl transition">Déconnexion</a>
            </div>
        </div>

        <?php if ($role === 'createur' || $role === 'admin'): ?>
            <div class="bg-white p-6 rounded-3xl shadow-xl mb-8 border-l-4 border-yellow-400">
                <h2 class="text-xl font-black text-indigo-900 uppercase tracking-widest mb-4">🛠️ Espace Créateur</h2>
                <div class="flex gap-4">
                    <a href="manage_quizzes" class="bg-yellow-400 hover:bg-yellow-500 text-yellow-900 font-black px-6 py-3 rounded-2xl shadow-md transition">Gérer mes Quiz</a>
                    <?php if ($role === 'admin'): ?>
                        <a href="admin_users" class="bg-purple-600 hover:bg-purple-500 text-white font-black px-6 py-3 rounded-2xl shadow-md transition">Gérer les Utilisateurs</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <h2 class="text-3xl font-black text-indigo-900 uppercase tracking-widest mb-6">🎮 Lancer une partie</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($quizzes as $q): ?>
                <div class="bg-white rounded-3xl shadow-lg p-6 flex flex-col h-full transform transition hover:-translate-y-1 hover:shadow-2xl">
                    <h3 class="text-xl font-black text-indigo-800 mb-2"><?= htmlspecialchars($q['title']) ?></h3>
                    <p class="text-gray-500 text-sm mb-6 flex-grow"><?= htmlspecialchars($q['description']) ?></p>
                    <a href="host_game?quiz_id=<?= $q['id'] ?>" class="text-center bg-indigo-600 text-white font-black py-3 rounded-xl hover:bg-indigo-500 transition shadow-md w-full">
                        Héberger ce Quiz
                    </a>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($quizzes)): ?>
                <p class="text-gray-500 italic col-span-full">Aucun quiz disponible pour le moment.</p>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>