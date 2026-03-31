<?php
require_once 'db.php';
if (!hasRole('createur')) { header("Location: dashboard"); exit; }

$quiz_id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();
if (!$quiz) { header("Location: manage_quizzes"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quiz_info'])) {
    $pdo->prepare("UPDATE quizzes SET title = ?, image_url = ?, description = ? WHERE id = ?")->execute([$_POST['q_title'], $_POST['q_img'], $_POST['q_desc'], $quiz_id]);
    header("Location: edit_quiz?id=$quiz_id&updated=1"); exit;
}
if (isset($_GET['delete_q'])) {
    $pdo->prepare("DELETE FROM questions WHERE id = ? AND quiz_id = ?")->execute([(int)$_GET['delete_q'], $quiz_id]);
    header("Location: edit_quiz?id=$quiz_id"); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_q'])) {
    if (!empty($_POST['q_id'])) {
        $pdo->prepare("UPDATE questions SET question_text=?,image_url=?,timer=?,opt1=?,opt2=?,opt3=?,opt4=?,correct_answer=? WHERE id=? AND quiz_id=?")->execute([$_POST['txt'],$_POST['img'],$_POST['time'],$_POST['o1'],$_POST['o2'],$_POST['o3'],$_POST['o4'],$_POST['correct'],$_POST['q_id'],$quiz_id]);
    } else {
        $pdo->prepare("INSERT INTO questions (quiz_id,question_text,image_url,timer,opt1,opt2,opt3,opt4,correct_answer) VALUES (?,?,?,?,?,?,?,?,?)")->execute([$quiz_id,$_POST['txt'],$_POST['img'],$_POST['time'],$_POST['o1'],$_POST['o2'],$_POST['o3'],$_POST['o4'],$_POST['correct']]);
    }
    header("Location: edit_quiz?id=$quiz_id"); exit;
}

$editQ = null;
if (isset($_GET['edit_q'])) {
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ? AND quiz_id = ?");
    $stmt->execute([(int)$_GET['edit_q'], $quiz_id]);
    $editQ = $stmt->fetch();
}
$qCount = (int)$pdo->query("SELECT COUNT(*) FROM questions WHERE quiz_id = $quiz_id")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/logo.png">
    <title>Édition – <?= htmlspecialchars($quiz['title']) ?></title>
    <style>
        body { background-color:#0f172a; background-image:radial-gradient(at 0% 0%,#1e1b4b 0px,transparent 50%),radial-gradient(at 100% 100%,#312e81 0px,transparent 50%); background-attachment:fixed; color:white; font-family:sans-serif; min-height:100vh; }
        .title-text { font-family:'Caveat',cursive; text-shadow:3px 3px 0px #312e81; letter-spacing:2px; }
        .game-card  { background-color:#1e1b4b; border:4px solid #312e81; border-radius:1.5rem; box-shadow:0 8px 0 0 #0b0f19; }
        .particle   { position:fixed; background:rgba(255,255,255,0.02); border-radius:50%; animation:drift infinite linear; pointer-events:none; z-index:0; }
        @keyframes drift { from{transform:translateY(100vh) rotate(0deg)} to{transform:translateY(-100vh) rotate(360deg)} }

        .form-input { background:#0f172a; border:3px solid #4338ca; border-radius:.75rem; color:white; padding:.65rem .9rem; width:100%; outline:none; font-size:.9rem; transition:border-color .2s; }
        .form-input::placeholder { color:#4b5563; }
        .form-input:focus { border-color:#facc15; }

        /* Options de réponse colorées */
        .opt-1 { border-left:6px solid #7c3aed; background:#1e1b4b; }
        .opt-2 { border-left:6px solid #db2777; background:#1e1b4b; }
        .opt-3 { border-left:6px solid #0891b2; background:#1e1b4b; }
        .opt-4 { border-left:6px solid #d97706; background:#1e1b4b; }
        .opt-1, .opt-2, .opt-3, .opt-4 { border-radius:.75rem; padding:.65rem .9rem; color:white; border-top:2px solid #312e81; border-right:2px solid #312e81; border-bottom:2px solid #312e81; outline:none; width:100%; font-size:.9rem; }
        .opt-1::placeholder, .opt-2::placeholder, .opt-3::placeholder, .opt-4::placeholder { color:#4b5563; }
        .opt-1:focus { border-color:#7c3aed; }
        .opt-2:focus { border-color:#db2777; }
        .opt-3:focus { border-color:#0891b2; }
        .opt-4:focus { border-color:#d97706; }

        /* Boutons */
        .btn-save   { background:#10b981; color:white; border:4px solid #047857; box-shadow:0 5px 0 0 #064e3b; border-radius:1rem; font-weight:900; text-transform:uppercase; letter-spacing:1px; padding:.75rem 1.5rem; transition:all .1s; cursor:pointer; flex-grow:1; }
        .btn-save:hover  { background:#34d399; }
        .btn-save:active { transform:translateY(5px); box-shadow:0 0 0 0 #064e3b; }
        .btn-cancel { background:#2e2a72; color:#a5b4fc; border:3px solid #4338ca; border-radius:1rem; font-weight:900; padding:.75rem 1.5rem; transition:all .1s; cursor:pointer; }
        .btn-cancel:hover { background:#3730a3; }
        .btn-update { background:#4f46e5; color:white; border:3px solid #3730a3; box-shadow:0 4px 0 0 #1e1b6e; border-radius:.75rem; font-weight:900; padding:.6rem 1.5rem; transition:all .1s; cursor:pointer; }
        .btn-update:hover  { background:#6366f1; }
        .btn-update:active { transform:translateY(4px); box-shadow:none; }

        /* Liste questions */
        .q-item { background:#2e2a72; border:2px solid #3730a3; border-radius:1rem; padding:1rem 1.25rem; display:flex; justify-content:space-between; align-items:center; transition:background .15s; }
        .q-item:hover { background:#3730a3; }

        /* Radio correct */
        .radio-label { display:flex; align-items:center; gap:.4rem; cursor:pointer; font-size:.85rem; color:#a5b4fc; font-weight:700; }
        .radio-label input[type=radio] { accent-color:#facc15; width:1rem; height:1rem; }

        /* Timer select */
        .timer-select { background:#0f172a; border:2px solid #4338ca; border-radius:.5rem; color:white; padding:.4rem .6rem; outline:none; }
        .timer-select:focus { border-color:#facc15; }
    </style>
</head>
<body class="p-4 md:p-8 relative">
<div class="particle" style="width:150px;height:150px;left:5%;animation-duration:25s;"></div>
<div class="particle" style="width:220px;height:220px;left:80%;animation-duration:35s;"></div>
<div class="relative z-10 max-w-4xl mx-auto">

    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center gap-4">
            <img src="images/logo.png" alt="Logo" class="h-12 drop-shadow-md" onerror="this.style.display='none'">
            <div>
                <h1 class="title-text text-2xl text-yellow-400">Édition du Quiz</h1>
                <p class="text-indigo-400 text-xs font-bold uppercase tracking-widest truncate max-w-[200px]"><?= htmlspecialchars($quiz['title']) ?></p>
            </div>
        </div>
        <a href="manage_quizzes" class="bg-indigo-600 text-white px-5 py-2 rounded-xl font-black uppercase tracking-wider shadow-[0_4px_0_0_#1e1b4b] hover:bg-indigo-500 transition-all active:translate-y-1 text-sm border-2 border-indigo-400">✓ Terminer</a>
    </div>

    <?php if (isset($_GET['updated'])): ?>
    <div class="bg-emerald-900/50 border-2 border-emerald-600 text-emerald-300 font-black px-5 py-3 rounded-xl mb-6 text-sm">✓ Informations mises à jour !</div>
    <?php endif; ?>

    <!-- Paramètres généraux -->
    <div class="game-card p-6 mb-6">
        <h2 class="title-text text-xl text-indigo-300 mb-5">⚙️ Paramètres généraux</h2>
        <form method="POST" class="flex flex-col gap-4">
            <div>
                <label class="text-xs font-black uppercase tracking-widest text-indigo-400 block mb-2">Titre</label>
                <input type="text" name="q_title" value="<?= htmlspecialchars($quiz['title']) ?>" required class="form-input">
            </div>
            <div>
                <label class="text-xs font-black uppercase tracking-widest text-indigo-400 block mb-2">URL de l'image de couverture</label>
                <input type="text" name="q_img" value="<?= htmlspecialchars($quiz['image_url']) ?>" placeholder="https://..." class="form-input">
            </div>
            <div>
                <label class="text-xs font-black uppercase tracking-widest text-indigo-400 block mb-2">Description</label>
                <textarea name="q_desc" rows="2" class="form-input resize-none"><?= htmlspecialchars($quiz['description']) ?></textarea>
            </div>
            <div class="flex items-center gap-4">
                <button type="submit" name="update_quiz_info" class="btn-update">💾 Enregistrer</button>
            </div>
        </form>
    </div>

    <!-- Formulaire question -->
    <div class="game-card p-6 mb-6">
        <h2 class="title-text text-xl <?= $editQ ? 'text-yellow-400' : 'text-green-400' ?> mb-5">
            <?= $editQ ? '✏️ Modifier la question' : '➕ Nouvelle question' ?>
        </h2>
        <form method="POST" class="flex flex-col gap-4">
            <input type="hidden" name="q_id" value="<?= $editQ['id'] ?? '' ?>">
            <input type="text" name="txt" placeholder="La question..." value="<?= htmlspecialchars($editQ['question_text'] ?? '') ?>" required class="form-input">
            <input type="text" name="img" placeholder="URL image pour la question (optionnel)" value="<?= htmlspecialchars($editQ['image_url'] ?? '') ?>" class="form-input">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <input type="text" name="o1" placeholder="✦  Option 1" value="<?= htmlspecialchars($editQ['opt1'] ?? '') ?>" required class="opt-1">
                <input type="text" name="o2" placeholder="⬢  Option 2" value="<?= htmlspecialchars($editQ['opt2'] ?? '') ?>" required class="opt-2">
                <input type="text" name="o3" placeholder="⬤  Option 3" value="<?= htmlspecialchars($editQ['opt3'] ?? '') ?>" required class="opt-3">
                <input type="text" name="o4" placeholder="■  Option 4" value="<?= htmlspecialchars($editQ['opt4'] ?? '') ?>" required class="opt-4">
            </div>

            <div class="bg-[#0f172a] border-2 border-[#4338ca] rounded-xl p-4 flex flex-wrap gap-4 items-center">
                <span class="text-xs font-black uppercase tracking-widest text-indigo-400">Bonne réponse :</span>
                <?php for ($i=1; $i<=4; $i++): ?>
                <label class="radio-label">
                    <input type="radio" name="correct" value="<?= $i ?>" <?= ($editQ['correct_answer'] ?? 1) == $i ? 'checked' : '' ?>>
                    Option <?= $i ?>
                </label>
                <?php endfor; ?>
                <div class="ml-auto flex items-center gap-2">
                    <span class="text-xs font-black text-indigo-400 uppercase">Temps :</span>
                    <select name="time" class="timer-select">
                        <option value="10" <?= ($editQ['timer'] ?? 20)==10?'selected':'' ?>>10s</option>
                        <option value="20" <?= ($editQ['timer'] ?? 20)==20?'selected':'' ?>>20s</option>
                        <option value="30" <?= ($editQ['timer'] ?? 20)==30?'selected':'' ?>>30s</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" name="save_q" class="btn-save">💾 Enregistrer la question</button>
                <?php if ($editQ): ?>
                <a href="edit_quiz?id=<?= $quiz_id ?>" class="btn-cancel">Annuler</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Liste des questions -->
    <div class="game-card p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="title-text text-xl text-white">📋 Questions <span class="text-indigo-400 text-base">(<?= $qCount ?>)</span></h2>
        </div>
        <div class="flex flex-col gap-3">
            <?php
            $qs = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id ASC");
            $qs->execute([$quiz_id]);
            $count = 1;
            while ($q = $qs->fetch()):
            ?>
            <div class="q-item group">
                <div class="flex items-center gap-4 flex-grow min-w-0">
                    <span class="bg-indigo-900 border-2 border-indigo-600 text-indigo-300 w-8 h-8 rounded-full flex items-center justify-center font-black text-sm flex-shrink-0"><?= $count++ ?></span>
                    <span class="font-bold text-white truncate"><?= htmlspecialchars($q['question_text']) ?></span>
                    <span class="text-xs text-indigo-500 flex-shrink-0"><?= $q['timer'] ?>s</span>
                </div>
                <div class="flex gap-3 ml-4 flex-shrink-0 opacity-0 group-hover:opacity-100 transition">
                    <a href="?id=<?= $quiz_id ?>&edit_q=<?= $q['id'] ?>" class="text-blue-400 text-sm font-black hover:text-blue-300">✏️ Modifier</a>
                    <a href="?id=<?= $quiz_id ?>&delete_q=<?= $q['id'] ?>" onclick="return confirm('Supprimer cette question ?')" class="text-red-400 text-sm font-black hover:text-red-300">🗑 Suppr.</a>
                </div>
            </div>
            <?php endwhile; ?>
            <?php if ($qCount === 0): ?>
            <div class="text-center text-indigo-500 font-bold py-8 text-sm uppercase tracking-widest">Aucune question pour l'instant — ajoutez-en une ci-dessus !</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>