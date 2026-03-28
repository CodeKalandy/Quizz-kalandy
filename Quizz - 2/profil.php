<?php
require_once 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupération des données avec valeurs par défaut
$correct = $u['total_correct'] ?? 0;
$wrong = $u['total_wrong'] ?? 0;
$games = $u['total_games'] ?? 0;
$p1 = $u['podium_1'] ?? 0;
$p2 = $u['podium_2'] ?? 0;
$p3 = $u['podium_3'] ?? 0;

// Système de Rangs et Progrès
$ranks = [
    ["Merguez de Bronze", 0, "text-orange-600"],
    ["Apprenti Bernard", 50, "text-blue-500"],
    ["Expert des Quiz", 150, "text-purple-600"],
    ["Maître des Questions", 350, "text-red-600"],
    ["Légende de Bernard", 700, "text-yellow-500"]
];

$currentRank = $ranks[0];
$nextRank = $ranks[1];
foreach ($ranks as $index => $r) {
    if ($correct >= $r[1]) {
        $currentRank = $r;
        $nextRank = $ranks[$index + 1] ?? null;
    }
}

$precision = ($correct + $wrong > 0) ? round(($correct / ($correct + $wrong)) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><script src="https://cdn.tailwindcss.com"></script>
    <title>Profil - <?= htmlspecialchars($u['username']) ?></title>
</head>
<body class="bg-gray-50 font-sans">
    <div class="max-w-4xl mx-auto p-4 md:p-8">
        
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-black text-indigo-900 uppercase italic">Statistiques</h1>
            <a href="dashboard.php" class="bg-white px-5 py-2 rounded-xl shadow-sm font-bold text-gray-500 hover:text-indigo-600 transition">Quitter</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="bg-white p-8 rounded-3xl shadow-xl border-b-8 border-indigo-600 flex flex-col items-center text-center">
                <div class="text-6xl mb-4">🏆</div>
                <h2 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Rang Actuel</h2>
                <p class="text-2xl font-black <?= $currentRank[2] ?> mb-6"><?= $currentRank[0] ?></p>
                
                <?php if($nextRank): ?>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden mb-2">
                        <div class="bg-indigo-600 h-full" style="width: <?= min(100, ($correct / $nextRank[1]) * 100) ?>%"></div>
                    </div>
                    <p class="text-[10px] text-gray-400 font-bold italic">Plus que <?= $nextRank[1] - $correct ?> bonnes réponses pour devenir "<?= $nextRank[0] ?>"</p>
                <?php endif; ?>
            </div>

            <div class="md:col-span-2 grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div class="bg-white p-6 rounded-3xl shadow-sm border text-center">
                    <p class="text-3xl font-black text-indigo-600"><?= $games ?></p>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Parties Jouées</p>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border text-center">
                    <p class="text-3xl font-black text-green-500"><?= $correct ?></p>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Bonnes Réponses</p>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border text-center">
                    <p class="text-3xl font-black text-red-500"><?= $wrong ?></p>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Erreurs</p>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border text-center">
                    <p class="text-3xl font-black text-indigo-400"><?= $precision ?>%</p>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Précision globale</p>
                </div>
                
                <div class="bg-indigo-900 col-span-2 p-6 rounded-3xl shadow-xl text-white flex justify-around items-center">
                    <div class="text-center">
                        <p class="text-2xl font-black text-yellow-400">🥇 <?= $p1 ?></p>
                        <p class="text-[8px] font-bold uppercase opacity-60">Victoires</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-black text-gray-300">🥈 <?= $p2 ?></p>
                        <p class="text-[8px] font-bold uppercase opacity-60">2ème Places</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-black text-orange-400">🥉 <?= $p3 ?></p>
                        <p class="text-[8px] font-bold uppercase opacity-60">3ème Places</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-12 bg-white p-8 rounded-3xl shadow-sm border">
            <h3 class="font-black text-indigo-900 mb-4 uppercase text-sm tracking-widest">Détails du compte</h3>
            <div class="flex gap-8 text-sm">
                <div>
                    <p class="text-gray-400 font-bold">Pseudo</p>
                    <p class="font-black"><?= htmlspecialchars($u['username']) ?></p>
                </div>
                <div>
                    <p class="text-gray-400 font-bold">Rôle</p>
                    <p class="font-black uppercase text-indigo-600"><?= htmlspecialchars($u['role']) ?></p>
                </div>
                <div>
                    <p class="text-gray-400 font-bold">ID Joueur</p>
                    <p class="font-black text-gray-300">#<?= $u['id'] ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>