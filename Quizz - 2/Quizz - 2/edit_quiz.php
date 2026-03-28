<?php
require_once 'db.php';
if (!hasRole('createur')) { header("Location: dashboard.php"); exit; }

$quiz_id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();

if (!$quiz) { header("Location: manage_quizzes.php"); exit; }

// --- ACTION : MODIFIER LES INFOS GLOBALES DU QUIZ ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quiz_info'])) {
    $stmt = $pdo->prepare("UPDATE quizzes SET title = ?, image_url = ?, description = ? WHERE id = ?");
    $stmt->execute([$_POST['q_title'], $_POST['q_img'], $_POST['q_desc'], $quiz_id]);
    header("Location: edit_quiz.php?id=$quiz_id&updated=1");
    exit;
}

// --- ACTION : SUPPRIMER UNE QUESTION ---
if (isset($_GET['delete_q'])) {
    $pdo->prepare("DELETE FROM questions WHERE id = ? AND quiz_id = ?")->execute([$_GET['delete_q'], $quiz_id]);
    header("Location: edit_quiz.php?id=$quiz_id");
    exit;
}

// --- ACTION : AJOUTER OU MODIFIER UNE QUESTION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_q'])) {
    if (!empty($_POST['q_id'])) {
        // Modification
        $stmt = $pdo->prepare("UPDATE questions SET question_text=?, image_url=?, timer=?, opt1=?, opt2=?, opt3=?, opt4=?, correct_answer=? WHERE id=? AND quiz_id=?");
        $stmt->execute([$_POST['txt'], $_POST['img'], $_POST['time'], $_POST['o1'], $_POST['o2'], $_POST['o3'], $_POST['o4'], $_POST['correct'], $_POST['q_id'], $quiz_id]);
    } else {
        // Ajout
        $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, image_url, timer, opt1, opt2, opt3, opt4, correct_answer) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$quiz_id, $_POST['txt'], $_POST['img'], $_POST['time'], $_POST['o1'], $_POST['o2'], $_POST['o3'], $_POST['o4'], $_POST['correct']]);
    }
    header("Location: edit_quiz.php?id=$quiz_id");
    exit;
}

// Récupérer la question à modifier si besoin
$editQ = null;
if (isset($_GET['edit_q'])) {
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ? AND quiz_id = ?");
    $stmt->execute([$_GET['edit_q'], $quiz_id]);
    $editQ = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><script src="https://cdn.tailwindcss.com"></script>
    <title>Configuration - <?= htmlspecialchars($quiz['title']) ?></title>
</head>
<body class="bg-gray-100 p-6 font-sans">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold italic uppercase text-indigo-800">Configuration du Quiz</h1>
            <a href="manage_quizzes.php" class="bg-white px-4 py-2 rounded-lg text-sm shadow hover:bg-gray-50 transition">Terminer et quitter</a>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border mb-8 border-l-8 border-indigo-600">
            <h2 class="font-black mb-4 uppercase text-sm tracking-widest text-gray-400">Paramètres Généraux</h2>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="text-xs font-bold uppercase text-gray-500">Titre du Quiz</label>
                    <input type="text" name="q_title" value="<?= htmlspecialchars($quiz['title']) ?>" required 
                           class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="text-xs font-bold uppercase text-gray-500">URL de l'Image de couverture</label>
                    <input type="text" name="q_img" value="<?= htmlspecialchars($quiz['image_url']) ?>" 
                           class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="https://...">
                </div>
                <div>
                    <label class="text-xs font-bold uppercase text-gray-500">Description</label>
                    <textarea name="q_desc" rows="2" class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none"><?= htmlspecialchars($quiz['description']) ?></textarea>
                </div>
                <button type="submit" name="update_quiz_info" class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-indigo-700 transition">
                    Enregistrer les modifications
                </button>
                <?php if(isset($_GET['updated'])): ?>
                    <span class="ml-4 text-green-600 font-bold text-sm">✓ Mis à jour !</span>
                <?php endif; ?>
            </form>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border mb-8">
            <h2 class="font-bold mb-4 text-indigo-700"><?= $editQ ? "Modifier la question" : "Ajouter une nouvelle question" ?></h2>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="q_id" value="<?= $editQ['id'] ?? '' ?>">
                <input type="text" name="txt" placeholder="Votre question..." value="<?= $editQ['question_text'] ?? '' ?>" required class="w-full p-3 border rounded-xl">
                <input type="text" name="img" placeholder="URL Image pour la question (optionnel)" value="<?= $editQ['image_url'] ?? '' ?>" class="w-full p-3 border rounded-xl">
                
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="o1" placeholder="Option 1" value="<?= $editQ['opt1'] ?? '' ?>" required class="p-3 border-l-8 border-red-500 rounded bg-red-50">
                    <input type="text" name="o2" placeholder="Option 2" value="<?= $editQ['opt2'] ?? '' ?>" required class="p-3 border-l-8 border-blue-500 rounded bg-blue-50">
                    <input type="text" name="o3" placeholder="Option 3" value="<?= $editQ['opt3'] ?? '' ?>" required class="p-3 border-l-8 border-yellow-500 rounded bg-yellow-50">
                    <input type="text" name="o4" placeholder="Option 4" value="<?= $editQ['opt4'] ?? '' ?>" required class="p-3 border-l-8 border-green-500 rounded bg-green-50">
                </div>

                <div class="flex items-center gap-4 bg-indigo-50 p-4 rounded-xl">
                    <span class="font-bold text-sm">Réponse correcte :</span>
                    <?php for($i=1;$i<=4;$i++): ?>
                        <label class="flex items-center gap-1 cursor-pointer">
                            <input type="radio" name="correct" value="<?= $i ?>" <?= ($editQ['correct_answer'] ?? 1) == $i ? 'checked' : '' ?>> <?= $i ?>
                        </label>
                    <?php endfor; ?>
                    <select name="time" class="ml-auto p-1 border rounded">
                        <option value="10" <?= ($editQ['timer'] ?? 20) == 10 ? 'selected' : '' ?>>10s</option>
                        <option value="20" <?= ($editQ['timer'] ?? 20) == 20 ? 'selected' : '' ?>>20s</option>
                        <option value="30" <?= ($editQ['timer'] ?? 20) == 30 ? 'selected' : '' ?>>30s</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" name="save_q" class="flex-grow bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-700 transition">ENREGISTRER LA QUESTION</button>
                    <?php if($editQ): ?><a href="edit_quiz.php?id=<?= $quiz_id ?>" class="bg-gray-200 px-6 py-3 rounded-xl hover:bg-gray-300">Annuler</a><?php endif; ?>
                </div>
            </form>
        </div>

        <h3 class="font-black uppercase text-xs tracking-widest text-gray-400 mb-4">Liste des Questions (<?= $pdo->query("SELECT count(*) FROM questions WHERE quiz_id = $quiz_id")->fetchColumn() ?>)</h3>
        <div class="space-y-3">
            <?php
            $qs = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id ASC");
            $qs->execute([$quiz_id]);
            $count = 1; // Compteur pour la numérotation liée au quiz
            while($q = $qs->fetch()):
            ?>
            <div class="bg-white p-4 rounded-xl shadow-sm border flex justify-between items-center group">
                <div class="flex items-center gap-4">
                    <span class="bg-indigo-100 text-indigo-700 w-8 h-8 rounded-full flex items-center justify-center font-black text-sm">
                        <?= $count++ ?>
                    </span>
                    <span class="font-medium text-gray-700"><?= htmlspecialchars($q['question_text']) ?></span>
                </div>
                <div class="flex gap-4 opacity-0 group-hover:opacity-100 transition">
                    <a href="?id=<?= $quiz_id ?>&edit_q=<?= $q['id'] ?>" class="text-blue-500 text-sm font-bold hover:underline">Modifier</a>
                    <a href="?id=<?= $quiz_id ?>&delete_q=<?= $q['id'] ?>" onclick="return confirm('Supprimer cette question ?')" class="text-red-500 text-sm font-bold hover:underline">Supprimer</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>