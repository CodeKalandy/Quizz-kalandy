<?php
require_once 'db.php';

// Tout le monde peut voir la bibliothèque s'il est connecté
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];
$isAdmin = ($userRole === 'admin' || $userRole === 'fondateur');

// --- ACTION : DUPLICATION ---
if (isset($_GET['duplicate'])) {
    $id_to_copy = $_GET['duplicate'];
    
    // 1. Copier le quiz (on le met en privé par défaut pour la copie)
    $stmt = $pdo->prepare("INSERT INTO quizzes (user_id, title, description, image_url, is_private) 
                           SELECT ?, CONCAT(title, ' (Copie)'), description, image_url, 1 
                           FROM quizzes WHERE id = ?");
    $stmt->execute([$userId, $id_to_copy]);
    $new_id = $pdo->lastInsertId();

    // 2. Copier toutes les questions liées
    $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, image_url, timer, opt1, opt2, opt3, opt4, correct_answer) 
                           SELECT ?, question_text, image_url, timer, opt1, opt2, opt3, opt4, correct_answer 
                           FROM questions WHERE quiz_id = ?");
    $stmt->execute([$new_id, $id_to_copy]);
    
    header("Location: manage_quizzes.php?duplicated=1");
    exit;
}

// --- ACTION : CHANGER VISIBILITÉ (PRIVÉ/PUBLIC) ---
if (isset($_GET['toggle_private'])) {
    $id = $_GET['toggle_private'];
    // On vérifie qu'on est propriétaire ou admin
    $check = $pdo->prepare("SELECT id, is_private FROM quizzes WHERE id = ? " . ($isAdmin ? "" : "AND user_id = ?"));
    $isAdmin ? $check->execute([$id]) : $check->execute([$id, $userId]);
    $q = $check->fetch();
    
    if ($q) {
        $new_status = $q['is_private'] ? 0 : 1;
        $pdo->prepare("UPDATE quizzes SET is_private = ? WHERE id = ?")->execute([$new_status, $id]);
    }
    header("Location: manage_quizzes.php");
    exit;
}

// --- ACTION : SUPPRESSION SÉCURISÉE ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $check = $pdo->prepare("SELECT id FROM quizzes WHERE id = ? " . ($isAdmin ? "" : "AND user_id = ?"));
    $isAdmin ? $check->execute([$id]) : $check->execute([$id, $userId]);

    if ($check->fetch()) {
        try {
            $pdo->beginTransaction();
            $pdo->prepare("DELETE FROM questions WHERE quiz_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM game_sessions WHERE quiz_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM quizzes WHERE id = ?")->execute([$id]);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Erreur lors de la suppression : " . $e->getMessage());
        }
    }
    header("Location: manage_quizzes.php?deleted=1");
    exit;
}

// --- ACTION : CRÉATION ---
if (isset($_POST['create_quiz']) && hasRole('createur')) {
    $title = trim($_POST['title']);
    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO quizzes (user_id, title, description, image_url, is_private) VALUES (?, ?, ?, ?, 0)");
        $stmt->execute([$userId, $title, $_POST['description'], $_POST['image_url']]);
        header("Location: manage_quizzes.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><script src="https://cdn.tailwindcss.com"></script>
    <title>Bibliothèque - Bernard Quizz</title>
</head>
<body class="bg-gray-50 min-h-screen p-6 font-sans">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-black text-indigo-800 uppercase tracking-tighter">Bibliothèque de Quiz</h1>
                <p class="text-gray-500 text-sm italic">Découvrez, dupliquez et jouez !</p>
            </div>
            <a href="dashboard.php" class="bg-white px-6 py-2 rounded-full shadow-sm font-bold text-gray-600 hover:bg-gray-100 transition">← Retour</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-1">
                <?php if(hasRole('createur')): ?>
                <div class="bg-white p-6 rounded-3xl shadow-xl border-t-4 border-indigo-600">
                    <h2 class="font-black mb-4 uppercase text-xs tracking-widest text-indigo-600">Nouveau Quiz</h2>
                    <form method="POST" class="space-y-4">
                        <input type="text" name="title" placeholder="Titre du quiz" required class="w-full p-3 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <input type="text" name="image_url" placeholder="URL de l'image" class="w-full p-3 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <textarea name="description" placeholder="Petite description..." class="w-full p-3 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                        <button type="submit" name="create_quiz" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-black shadow-lg hover:bg-indigo-700 transition">CRÉER</button>
                    </form>
                </div>
                <?php else: ?>
                <div class="bg-indigo-50 p-6 rounded-3xl border-2 border-dashed border-indigo-200 text-center">
                    <p class="text-indigo-400 text-sm font-bold">Inscrivez-vous comme créateur pour proposer vos propres quiz !</p>
                </div>
                <?php endif; ?>
            </div>

            <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php
                // Logique de visibilité : Les publics de tout le monde + Mes privés + Tous si Admin
                $query = "SELECT q.*, u.username as owner_name FROM quizzes q 
                          JOIN users u ON q.user_id = u.id
                          WHERE q.is_private = 0 OR q.user_id = ? OR ? IN ('admin', 'fondateur')
                          ORDER BY q.id DESC";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$userId, $userRole]);

                while($q = $stmt->fetch()):
                    $isOwner = ($q['user_id'] == $userId || $isAdmin);
                ?>
                <div class="bg-white rounded-3xl shadow-sm border overflow-hidden flex flex-col group hover:shadow-xl transition-all duration-300">
                    <div class="h-40 bg-gray-100 relative">
                        <?php if($q['image_url']): ?> 
                            <img src="<?= htmlspecialchars($q['image_url']) ?>" class="w-full h-full object-cover"> 
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-gray-300 font-black text-4xl">QUIZZ</div>
                        <?php endif; ?>
                        
                        <span class="absolute top-3 left-3 bg-black bg-opacity-50 text-white text-[10px] px-2 py-1 rounded-full font-bold">
                            Par <?= htmlspecialchars($q['owner_name']) ?>
                        </span>

                        <?php if($isOwner): ?>
                        <a href="?toggle_private=<?= $q['id'] ?>" class="absolute top-3 right-3 <?= $q['is_private'] ? 'bg-red-500' : 'bg-green-500' ?> text-white text-[10px] px-2 py-1 rounded-full font-bold uppercase shadow-lg">
                            <?= $q['is_private'] ? '🔒 Privé' : '🌍 Public' ?>
                        </a>
                        <?php endif; ?>
                    </div>

                    <div class="p-5 flex-grow">
                        <h3 class="font-black text-lg mb-1 truncate text-gray-800"><?= htmlspecialchars($q['title']) ?></h3>
                        <p class="text-gray-400 text-xs line-clamp-2 mb-4"><?= htmlspecialchars($q['description']) ?></p>
                        
                        <div class="grid grid-cols-2 gap-2">
                            <a href="host_game.php?quiz_id=<?= $q['id'] ?>" class="bg-indigo-600 text-white text-center py-2 rounded-xl text-xs font-black hover:bg-indigo-700 transition">LANCER</a>
                            
                            <a href="?duplicate=<?= $q['id'] ?>" onclick="return confirm('Dupliquer ce quiz dans votre bibliothèque ?')" class="bg-yellow-400 text-black text-center py-2 rounded-xl text-xs font-black hover:bg-yellow-500 transition">COPIER</a>
                            
                            <?php if($isOwner): ?>
                                <a href="edit_quiz.php?id=<?= $q['id'] ?>" class="bg-gray-100 text-gray-600 text-center py-2 rounded-xl text-xs font-bold hover:bg-gray-200 transition">MODIFIER</a>
                                <a href="?delete=<?= $q['id'] ?>" onclick="return confirm('Supprimer définitivement ?')" class="bg-red-50 text-red-400 text-center py-2 rounded-xl text-xs font-bold hover:bg-red-100 transition text-opacity-60">SUPPRIMER</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>