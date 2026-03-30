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

// 🎯 Calcul des déblocages des auras mystères !
$aura6_unlocked = ($games >= 10);
$aura7_unlocked = ($p1 >= 3);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Profil - <?= htmlspecialchars($u['username']) ?></title>
    <style>
        /* Animations des Auras CSS */
        @keyframes rainbow { 100% { filter: hue-rotate(360deg); } }
        .aura-rainbow { position: absolute; top: -15%; left: -15%; width: 130%; height: 130%; border-radius: 50%; box-shadow: 0 0 20px 5px #f43f5e, inset 0 0 20px 5px #f43f5e; animation: rainbow 2.5s linear infinite; z-index: 5; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }
        .aura-float { animation: float 3s ease-in-out infinite; }
        .neon-vip { text-shadow: 0 0 5px #fff, 0 0 10px #facc15, 0 0 20px #facc15; color: #facc15; }
        .preview-container { width: 160px; height: 160px; position: relative; margin: 0 auto; background: #1e1b4b; border-radius: 20px; overflow: visible; border: 4px solid #4f46e5; }
    </style>
</head>
<body class="bg-indigo-900 font-sans text-white min-h-screen p-4 md:p-8">
    <div class="max-w-6xl mx-auto">
        
        <div class="flex justify-between items-center mb-10">
            <div class="flex items-center gap-4">
                <img src="images/logo.png" alt="Logo" class="h-12 drop-shadow-md" onerror="this.style.display='none'">
                <h1 class="text-3xl font-black text-yellow-400 uppercase tracking-widest drop-shadow-lg hidden md:block">Vitrine du Joueur</h1>
            </div>
            <a href="dashboard.php" class="bg-white px-5 py-2 rounded-xl shadow-lg font-bold text-indigo-900 hover:bg-gray-200 transition">Retour Menu</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="bg-white/10 backdrop-blur-md p-8 rounded-3xl shadow-2xl border border-white/20 flex flex-col items-center text-center">
                <h2 class="text-3xl font-black neon-vip uppercase tracking-widest mb-8"><?= htmlspecialchars($u['username']) ?></h2>
                
                <div class="preview-container shadow-2xl flex items-end justify-center mb-8" id="char-wrapper">
                    <div id="prev-aura-container"></div>
                    <div class="relative w-full h-full overflow-hidden rounded-[15px] flex items-end justify-center z-10 bg-white/20">
                        <img src="personnage/tenue/tenue1.png" class="absolute w-[90%] h-[90%] object-contain bottom-0" style="z-index: 10;">
                        <img src="personnage/cheveux/cheveux1.png" class="absolute w-[90%] h-[90%] object-contain bottom-0" style="z-index: 20;">
                    </div>
                    <div class="absolute -bottom-3 -right-3 bg-yellow-400 text-black text-[14px] font-black w-8 h-8 flex items-center justify-center rounded-full border-2 border-white z-40 shadow-lg" title="Joueur VIP">★</div>
                </div>

                <p class="text-sm font-bold text-indigo-300 uppercase tracking-widest mb-4">Cabine d'essayage (Auras)</p>
                <div class="flex flex-wrap justify-center gap-3">
                    <button onclick="testAura(0)" class="w-10 h-10 bg-white/20 rounded-lg hover:bg-white/40 transition font-bold">Ø</button>
                    <button onclick="testAura(1)" class="w-10 h-10 bg-white/20 rounded-lg hover:bg-white/40 transition overflow-hidden p-1"><img src="personnage/aura/aura1.png" class="w-full h-full object-contain"></button>
                    <button onclick="testAura(2)" class="w-10 h-10 bg-white/20 rounded-lg hover:bg-white/40 transition overflow-hidden p-1"><img src="personnage/aura/aura2.png" class="w-full h-full object-contain"></button>
                    <button onclick="testAura(3)" class="w-10 h-10 bg-white/20 rounded-lg hover:bg-white/40 transition overflow-hidden p-1"><img src="personnage/aura/aura3.png" class="w-full h-full object-contain"></button>
                    <button onclick="testAura(4)" class="w-10 h-10 bg-white/20 rounded-lg hover:bg-white/40 transition overflow-hidden p-1"><img src="personnage/aura/aura4.png" class="w-full h-full object-contain"></button>
                    <button onclick="testAura(5)" class="w-10 h-10 bg-white/20 rounded-lg hover:bg-white/40 transition overflow-hidden p-1"><img src="personnage/aura/aura5.png" class="w-full h-full object-contain"></button>
                    
                    <button onclick="testAura(6)" class="w-10 h-10 <?= $aura6_unlocked ? 'bg-indigo-500 hover:bg-indigo-400' : 'bg-gray-800 opacity-50 cursor-not-allowed' ?> rounded-lg transition font-bold relative">
                        🌈 <?php if(!$aura6_unlocked) echo '<span class="absolute inset-0 flex items-center justify-center text-lg drop-shadow-md bg-black/40 rounded-lg">🔒</span>'; ?>
                    </button>
                    <button onclick="testAura(7)" class="w-10 h-10 <?= $aura7_unlocked ? 'bg-indigo-500 hover:bg-indigo-400' : 'bg-gray-800 opacity-50 cursor-not-allowed' ?> rounded-lg transition font-bold relative">
                        ☁️ <?php if(!$aura7_unlocked) echo '<span class="absolute inset-0 flex items-center justify-center text-lg drop-shadow-md bg-black/40 rounded-lg">🔒</span>'; ?>
                    </button>
                </div>
            </div>

            <div class="lg:col-span-2 flex flex-col gap-6">
                
                <div class="bg-white p-6 md:p-8 rounded-3xl shadow-xl border-b-8 border-indigo-600 flex items-center gap-6">
                    <div class="text-6xl drop-shadow-lg">🏆</div>
                    <div class="flex-grow text-gray-900">
                        <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Rang Actuel</h2>
                        <p class="text-3xl md:text-4xl font-black <?= $currentRank[2] ?> mb-2"><?= $currentRank[0] ?></p>
                        <?php if($nextRank): ?>
                            <div class="w-full bg-gray-200 h-3 rounded-full overflow-hidden mb-1">
                                <div class="bg-indigo-600 h-full" style="width: <?= min(100, ($correct / $nextRank[1]) * 100) ?>%"></div>
                            </div>
                            <p class="text-xs text-gray-500 font-bold italic">Plus que <?= $nextRank[1] - $correct ?> bonnes réponses pour devenir "<?= $nextRank[0] ?>"</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-gray-900">
                    <div class="bg-white p-4 rounded-2xl shadow-sm text-center transform hover:scale-105 transition">
                        <p class="text-3xl font-black text-indigo-600"><?= $games ?></p>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Parties Jouées</p>
                    </div>
                    <div class="bg-white p-4 rounded-2xl shadow-sm text-center transform hover:scale-105 transition">
                        <p class="text-3xl font-black text-green-500"><?= $correct ?></p>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Bonnes Réponses</p>
                    </div>
                    <div class="bg-white p-4 rounded-2xl shadow-sm text-center transform hover:scale-105 transition">
                        <p class="text-3xl font-black text-red-500"><?= $wrong ?></p>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Erreurs</p>
                    </div>
                    <div class="bg-white p-4 rounded-2xl shadow-sm text-center transform hover:scale-105 transition">
                        <p class="text-3xl font-black text-indigo-400"><?= $precision ?>%</p>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Précision Globale</p>
                    </div>
                </div>
                
                <div class="bg-black/40 backdrop-blur-md p-6 rounded-3xl shadow-xl flex justify-around items-center border border-white/10">
                    <div class="text-center">
                        <p class="text-4xl font-black text-yellow-400 drop-shadow-[0_0_15px_rgba(250,204,21,0.6)]">🥇 <?= $p1 ?></p>
                        <p class="text-xs font-bold uppercase text-indigo-200 mt-2">Victoires</p>
                    </div>
                    <div class="text-center">
                        <p class="text-4xl font-black text-gray-300 drop-shadow-[0_0_10px_rgba(209,213,219,0.5)]">🥈 <?= $p2 ?></p>
                        <p class="text-xs font-bold uppercase text-indigo-200 mt-2">2ème Places</p>
                    </div>
                    <div class="text-center">
                        <p class="text-4xl font-black text-orange-400 drop-shadow-[0_0_10px_rgba(251,146,60,0.5)]">🥉 <?= $p3 ?></p>
                        <p class="text-xs font-bold uppercase text-indigo-200 mt-2">3ème Places</p>
                    </div>
                </div>

                <div class="bg-white text-gray-900 p-6 md:p-8 rounded-3xl shadow-xl">
                    <h3 class="font-black text-indigo-900 mb-6 uppercase tracking-widest border-b-2 border-indigo-100 pb-2">🎯 Quêtes Secrètes (Auras)</h3>
                    
                    <div class="flex flex-col md:flex-row items-center gap-6 mb-8">
                        <div class="text-5xl bg-gray-100 p-4 rounded-2xl shadow-inner border border-gray-200"><?= $aura6_unlocked ? '🌈' : '🔒' ?></div>
                        <div class="flex-grow w-full text-center md:text-left">
                            <p class="font-black text-xl <?= $aura6_unlocked ? 'text-indigo-600' : 'text-gray-500' ?>">Aura Arc-en-Ciel</p>
                            <p class="text-sm text-gray-500 mb-2">Participer à 10 parties. (<?= min(10, $games) ?>/10)</p>
                            <div class="w-full bg-gray-200 h-3 rounded-full overflow-hidden shadow-inner">
                                <div class="bg-gradient-to-r from-pink-500 via-yellow-400 to-indigo-500 h-full transition-all duration-1000" style="width: <?= min(100, ($games / 10) * 100) ?>%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row items-center gap-6">
                        <div class="text-5xl bg-gray-100 p-4 rounded-2xl shadow-inner border border-gray-200"><?= $aura7_unlocked ? '☁️' : '🔒' ?></div>
                        <div class="flex-grow w-full text-center md:text-left">
                            <p class="font-black text-xl <?= $aura7_unlocked ? 'text-indigo-600' : 'text-gray-500' ?>">Aura Lévitation</p>
                            <p class="text-sm text-gray-500 mb-2">Remporter 3 médailles d'or 🥇. (<?= min(3, $p1) ?>/3)</p>
                            <div class="w-full bg-gray-200 h-3 rounded-full overflow-hidden shadow-inner">
                                <div class="bg-indigo-400 h-full transition-all duration-1000" style="width: <?= min(100, ($p1 / 3) * 100) ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        const aura6Unlocked = <?= $aura6_unlocked ? 'true' : 'false' ?>;
        const aura7Unlocked = <?= $aura7_unlocked ? 'true' : 'false' ?>;

        function testAura(id) { 
            if (id === 6 && !aura6Unlocked) return alert("Vous n'avez pas encore débloqué l'Aura Arc-en-Ciel ! Participez à 10 parties pour l'obtenir.");
            if (id === 7 && !aura7Unlocked) return alert("Vous n'avez pas encore débloqué l'Aura Lévitation ! Gagnez 3 parties pour l'obtenir.");

            const cont = document.getElementById('prev-aura-container');
            const wrap = document.getElementById('char-wrapper');
            wrap.classList.remove('aura-float');
            
            if(id === 0) { 
                cont.innerHTML = ''; 
            } else if(id <= 5) {
                let zIndex = (id == 1 || id == 5) ? 30 : 5;
                cont.innerHTML = `<img src="personnage/aura/aura${id}.png" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] object-contain animate-pulse" style="z-index: ${zIndex};">`;
            } else if (id === 6) {
                cont.innerHTML = `<div class="aura-rainbow"></div>`;
            } else if (id === 7) {
                cont.innerHTML = ''; 
                wrap.classList.add('aura-float');
            }
        }
    </script>
</body>
</html>