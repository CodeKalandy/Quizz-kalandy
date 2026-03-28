<?php
require_once 'db.php';

// Sécurité : Seuls les utilisateurs connectés accèdent au dashboard
if (!isset($_SESSION['user_id'])) { 
    header("Location: index.php"); 
    exit; 
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];
$isAdmin = ($userRole === 'admin' || $userRole === 'fondateur');

// Récupération des quiz accessibles (Publics + les miens + tous si Admin)
$query = "SELECT q.*, u.username as owner_name FROM quizzes q 
          JOIN users u ON q.user_id = u.id
          WHERE q.is_private = 0 OR q.user_id = ? OR ? IN ('admin', 'fondateur')
          ORDER BY q.id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId, $userRole]);
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Dashboard - Bernard Quizz</title>
</head>
<body class="bg-gray-100 font-sans">
    <nav class="bg-indigo-700 text-white p-4 shadow-lg">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-black italic tracking-tighter">BERNARD QUIZZ</h1>
            <div class="flex gap-4 items-center">
                <a href="profil.php" class="bg-indigo-600 px-4 py-2 rounded-lg font-bold hover:bg-indigo-500 transition">Mon Profil</a>
                <a href="logout.php" class="text-sm opacity-70 hover:opacity-100">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto py-8 px-6">
        <h2 class="text-3xl font-bold mb-8 text-gray-800 tracking-tighter">Bonjour, <?= htmlspecialchars($_SESSION['username']) ?> !</h2>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="space-y-6">
                <div class="bg-white p-8 rounded-3xl shadow-xl border-t-8 border-yellow-400">
                    <h3 class="font-black text-xl mb-4 uppercase tracking-tight">Rejoindre une partie</h3>
                    <form action="lobby.php" method="GET" class="flex flex-col gap-3">
                        <input type="text" name="pin" placeholder="CODE PIN" class="w-full p-4 border-2 rounded-2xl font-black text-2xl tracking-widest outline-none focus:border-indigo-500 text-center">
                        <button type="submit" class="bg-indigo-600 text-white py-4 rounded-2xl font-black hover:bg-indigo-700 transition shadow-lg">REJOINDRE</button>
                    </form>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <?php if(hasRole('createur')): ?>
                    <a href="manage_quizzes.php" class="bg-white p-6 rounded-3xl shadow-sm border-b-4 border-indigo-500 hover:scale-105 transition flex items-center gap-4">
                        <span class="text-3xl">📚</span>
                        <div>
                            <h3 class="font-black text-sm uppercase">Ma Bibliothèque</h3>
                            <p class="text-gray-400 text-[10px]">Gérez vos créations</p>
                        </div>
                    </a>
                    <?php endif; ?>

                    <?php if(hasRole('admin')): ?>
                    <a href="admin_users.php" class="bg-white p-6 rounded-3xl shadow-sm border-b-4 border-red-500 hover:scale-105 transition flex items-center gap-4">
                        <span class="text-3xl">🛠️</span>
                        <div>
                            <h3 class="font-black text-sm uppercase">Panel Admin</h3>
                            <p class="text-gray-400 text-[10px]">Gestion utilisateurs</p>
                        </div>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="flex justify-between items-end mb-6">
                    <h3 class="font-black text-2xl text-indigo-900 uppercase italic tracking-tighter">Quiz de la communauté</h3>
                    <span class="text-xs font-bold text-gray-400"><?= count($quizzes) ?> quiz disponibles</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php if(empty($quizzes)): ?>
                        <div class="col-span-2 bg-white p-10 rounded-3xl border-2 border-dashed border-gray-200 text-center">
                            <p class="text-gray-400 font-bold italic">Aucun quiz public pour le moment...</p>
                        </div>
                    <?php endif; ?>

                    <?php foreach($quizzes as $q): ?>
                    <div class="bg-white rounded-3xl shadow-md border border-gray-100 overflow-hidden flex flex-col group">
                        <div class="h-32 bg-gray-200 relative">
                            <?php if($q['image_url']): ?> 
                                <img src="<?= htmlspecialchars($q['image_url']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500"> 
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-300 font-black">IMAGE</div>
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <span class="absolute bottom-2 left-3 text-white text-[10px] font-bold">Par <?= htmlspecialchars($q['owner_name']) ?></span>
                        </div>
                        <div class="p-4 flex-grow">
                            <h4 class="font-black text-gray-800 mb-2 truncate"><?= htmlspecialchars($q['title']) ?></h4>
                            <a href="host_game.php?quiz_id=<?= $q['id'] ?>" class="block w-full bg-green-500 text-white text-center py-2 rounded-xl text-xs font-black hover:bg-green-600 transition shadow-sm">
                                🚀 LANCER UNE PARTIE
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</body>
</html>