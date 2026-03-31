<?php
require_once 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index"); exit; }

$userId   = $_SESSION['user_id'];
$userRole = $_SESSION['role'];
$isAdmin  = ($userRole === 'admin' || $userRole === 'fondateur');

if (isset($_GET['duplicate'])) {
    $id_to_copy = (int)$_GET['duplicate'];
    $pdo->prepare("INSERT INTO quizzes (user_id, title, description, image_url, is_private) SELECT ?, CONCAT(title, ' (Copie)'), description, image_url, 1 FROM quizzes WHERE id = ?")->execute([$userId, $id_to_copy]);
    $new_id = $pdo->lastInsertId();
    $pdo->prepare("INSERT INTO questions (quiz_id, question_text, image_url, timer, opt1, opt2, opt3, opt4, correct_answer) SELECT ?, question_text, image_url, timer, opt1, opt2, opt3, opt4, correct_answer FROM questions WHERE quiz_id = ?")->execute([$new_id, $id_to_copy]);
    header("Location: manage_quizzes?duplicated=1"); exit;
}

if (isset($_GET['toggle_private'])) {
    $id    = (int)$_GET['toggle_private'];
    $check = $pdo->prepare("SELECT id, is_private FROM quizzes WHERE id = ?" . ($isAdmin ? "" : " AND user_id = ?"));
    $isAdmin ? $check->execute([$id]) : $check->execute([$id, $userId]);
    $q = $check->fetch();
    if ($q) $pdo->prepare("UPDATE quizzes SET is_private = ? WHERE id = ?")->execute([$q['is_private'] ? 0 : 1, $id]);
    header("Location: manage_quizzes"); exit;
}

if (isset($_GET['delete'])) {
    $id    = (int)$_GET['delete'];
    $check = $pdo->prepare("SELECT id FROM quizzes WHERE id = ?" . ($isAdmin ? "" : " AND user_id = ?"));
    $isAdmin ? $check->execute([$id]) : $check->execute([$id, $userId]);
    if ($check->fetch()) {
        try {
            $pdo->beginTransaction();
            $pdo->prepare("DELETE FROM questions WHERE quiz_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM quizzes WHERE id = ?")->execute([$id]);
            $pdo->commit();
        } catch (Exception $e) { $pdo->rollBack(); }
    }
    header("Location: manage_quizzes?deleted=1"); exit;
}

if (isset($_POST['create_quiz']) && hasRole('createur')) {
    $title = trim($_POST['title']);
    if (!empty($title)) {
        $pdo->prepare("INSERT INTO quizzes (user_id, title, description, image_url, is_private) VALUES (?, ?, ?, ?, 0)")->execute([$userId, $title, $_POST['description'], $_POST['image_url']]);
        header("Location: manage_quizzes"); exit;
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
    <link rel="icon" type="image/png" href="images/logo.png">
    <title>Bibliothèque – Bernard Quizz</title>
    <style>
        body { background-color:#0f172a; background-image:radial-gradient(at 0% 0%,#1e1b4b 0px,transparent 50%),radial-gradient(at 100% 100%,#312e81 0px,transparent 50%); background-attachment:fixed; color:white; font-family:sans-serif; min-height:100vh; }
        .title-text { font-family:'Caveat',cursive; text-shadow:3px 3px 0px #312e81; letter-spacing:2px; }
        .game-card  { background-color:#1e1b4b; border:4px solid #312e81; border-radius:1.5rem; box-shadow:0 8px 0 0 #0b0f19; }
        .particle   { position:fixed; background:rgba(255,255,255,0.02); border-radius:50%; animation:drift infinite linear; pointer-events:none; z-index:0; }
        @keyframes drift { from{transform:translateY(100vh) rotate(0deg)} to{transform:translateY(-100vh) rotate(360deg)} }

        /* Formulaire nouveau quiz */
        .form-input { background:#0f172a; border:3px solid #4338ca; border-radius:.75rem; color:white; padding:.65rem .9rem; width:100%; outline:none; font-size:.9rem; transition:border-color .2s; }
        .form-input::placeholder { color:#4b5563; }
        .form-input:focus { border-color:#facc15; }
        .create-btn { background:#10b981; color:white; border:4px solid #047857; box-shadow:0 5px 0 0 #064e3b; border-radius:1rem; font-weight:900; font-size:.95rem; text-transform:uppercase; letter-spacing:1px; padding:.75rem; width:100%; transition:all .1s; cursor:pointer; text-shadow:2px 2px 0 #065f46; }
        .create-btn:hover  { background:#34d399; }
        .create-btn:active { transform:translateY(5px); box-shadow:0 0 0 0 #064e3b; }

        /* Cards quiz */
        .quiz-card { background:#1e1b4b; border:3px solid #312e81; border-radius:1.25rem; overflow:hidden; display:flex; flex-direction:column; transition:transform .2s, box-shadow .2s; }
        .quiz-card:hover { transform:translateY(-4px); box-shadow:0 12px 0 0 #0b0f19; }
        .quiz-cover { height:140px; background:#2e2a72; position:relative; overflow:hidden; }
        .quiz-cover img { width:100%; height:100%; object-fit:cover; }
        .quiz-cover-placeholder { width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-family:'Caveat',cursive; font-size:2.5rem; color:#4338ca; }

        /* Boutons action */
        .btn-action { text-align:center; padding:.4rem .6rem; border-radius:.6rem; font-size:.7rem; font-weight:900; text-transform:uppercase; letter-spacing:.5px; transition:all .1s; }
        .btn-launch  { background:#10b981; color:white; box-shadow:0 3px 0 0 #064e3b; }
        .btn-launch:hover  { background:#34d399; }
        .btn-launch:active { transform:translateY(3px); box-shadow:none; }
        .btn-copy    { background:#ca8a04; color:#fff7ed; box-shadow:0 3px 0 0 #713f12; }
        .btn-copy:hover  { background:#facc15; color:#451a03; }
        .btn-edit    { background:#2e2a72; color:#a5b4fc; border:2px solid #4338ca; }
        .btn-edit:hover  { background:#3730a3; }
        .btn-delete  { background:#450a0a; color:#fca5a5; border:2px solid #7f1d1d; }
        .btn-delete:hover { background:#7f1d1d; }
    </style>
</head>
<body class="p-4 md:p-8 relative">
<div class="particle" style="width:150px;height:150px;left:5%;animation-duration:25s;"></div>
<div class="particle" style="width:220px;height:220px;left:80%;animation-duration:35s;"></div>
<div class="relative z-10 max-w-6xl mx-auto">

    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center gap-4">
            <img src="images/logo.png" alt="Logo" class="h-12 drop-shadow-md" onerror="this.style.display='none'">
            <div>
                <h1 class="title-text text-3xl text-yellow-400">Bibliothèque</h1>
                <p class="text-indigo-400 text-xs font-bold uppercase tracking-widest">Découvrez, dupliquez et jouez !</p>
            </div>
        </div>
        <a href="dashboard" class="bg-indigo-600 text-white px-5 py-2 rounded-xl font-black uppercase tracking-wider shadow-[0_4px_0_0_#1e1b4b] hover:bg-indigo-500 transition-all active:translate-y-1 text-sm border-2 border-indigo-400">← Menu</a>
    </div>

    <?php if (isset($_GET['duplicated'])): ?>
    <div class="bg-emerald-900/50 border-2 border-emerald-600 text-emerald-300 font-black px-5 py-3 rounded-xl mb-6 text-sm uppercase tracking-widest">✓ Quiz dupliqué dans votre bibliothèque !</div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
    <div class="bg-red-900/50 border-2 border-red-700 text-red-300 font-black px-5 py-3 rounded-xl mb-6 text-sm uppercase tracking-widest">🗑 Quiz supprimé.</div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <!-- Sidebar création -->
        <div class="lg:col-span-1">
            <?php if (hasRole('createur')): ?>
            <div class="game-card p-6">
                <h2 class="title-text text-xl text-green-400 mb-4">✏️ Nouveau Quiz</h2>
                <form method="POST" class="flex flex-col gap-3">
                    <input type="text"  name="title"       placeholder="Titre du quiz *" required class="form-input">
                    <input type="text"  name="image_url"   placeholder="URL de l'image"          class="form-input">
                    <textarea          name="description"  placeholder="Description..."  rows="3"  class="form-input resize-none"></textarea>
                    <button type="submit" name="create_quiz" class="create-btn">+ Créer</button>
                </form>
            </div>
            <?php else: ?>
            <div class="game-card p-6 text-center">
                <span class="text-3xl block mb-3">🔒</span>
                <p class="text-indigo-400 text-sm font-bold">Seuls les créateurs peuvent proposer des quiz.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Grille de quiz -->
        <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            <?php
            $stmt = $pdo->prepare("SELECT q.*, u.username as owner_name FROM quizzes q JOIN users u ON q.user_id = u.id WHERE q.is_private = 0 OR q.user_id = ? OR ? IN ('admin','fondateur') ORDER BY q.id DESC");
            $stmt->execute([$userId, $userRole]);
            while ($q = $stmt->fetch()):
                $isOwner = ($q['user_id'] == $userId || $isAdmin);
            ?>
            <div class="quiz-card">
                <div class="quiz-cover">
                    <?php if ($q['image_url']): ?>
                        <img src="<?= htmlspecialchars($q['image_url']) ?>" alt="">
                    <?php else: ?>
                        <div class="quiz-cover-placeholder">QUIZZ</div>
                    <?php endif; ?>
                    <span class="absolute top-2 left-2 bg-black/60 backdrop-blur-sm text-white text-[10px] px-2 py-1 rounded-full font-bold">
                        <?= htmlspecialchars($q['owner_name']) ?>
                    </span>
                    <?php if ($isOwner): ?>
                    <a href="?toggle_private=<?= $q['id'] ?>" class="absolute top-2 right-2 <?= $q['is_private'] ? 'bg-red-600' : 'bg-emerald-600' ?> text-white text-[10px] px-2 py-1 rounded-full font-black uppercase shadow">
                        <?= $q['is_private'] ? '🔒 Privé' : '🌍 Public' ?>
                    </a>
                    <?php endif; ?>
                </div>

                <div class="p-4 flex flex-col flex-grow gap-3">
                    <div>
                        <h3 class="font-black text-white text-base truncate"><?= htmlspecialchars($q['title']) ?></h3>
                        <p class="text-indigo-400 text-xs line-clamp-2 mt-1"><?= htmlspecialchars($q['description']) ?></p>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-auto">
                        <a href="host_game?quiz_id=<?= $q['id'] ?>" class="btn-action btn-launch">🚀 Lancer</a>
                        <a href="?duplicate=<?= $q['id'] ?>" onclick="return confirm('Dupliquer ce quiz ?')" class="btn-action btn-copy">📋 Copier</a>
                        <?php if ($isOwner): ?>
                        <a href="edit_quiz?id=<?= $q['id'] ?>" class="btn-action btn-edit">✏️ Modifier</a>
                        <a href="?delete=<?= $q['id'] ?>" onclick="return confirm('Supprimer définitivement ?')" class="btn-action btn-delete">🗑 Suppr.</a>
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