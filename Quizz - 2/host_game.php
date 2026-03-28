<?php
require_once 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$quiz_id = $_GET['quiz_id'] ?? null;
$current_pin = $_GET['pin'] ?? null;

if (isset($_POST['start_session'])) {
    $pin = rand(100000, 999999);
    $mode = $_POST['game_mode'] ?? 'classique';
    
    $chemin_dossier = __DIR__ . '/sessions';
    if (!is_dir($chemin_dossier)) { mkdir($chemin_dossier, 0777, true); }
    
    // Ajout du paramètre 'mode' et 'eliminated' pour le Battle Royale
    $fichier_partie = $chemin_dossier . '/game_' . $pin . '.json';
    $blank = [
        'mode' => $mode,
        'eliminated' => [],
        'players' => [], 
        'scores' => new stdClass(), 
        'answers' => new stdClass(), 
        'status' => 'lobby', 
        'current_q_index' => -1, 
        'last_update' => time()
    ];
    
    file_put_contents($fichier_partie, json_encode($blank));
    header("Location: host_game.php?pin=$pin&quiz_id=$quiz_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Salon - Bernard Quizz</title>
</head>
<body class="bg-indigo-600 min-h-screen text-white flex flex-col items-center p-10 font-sans">
    <?php if (!$current_pin): ?>
        <div class="bg-white p-8 rounded-2xl shadow-xl text-center text-gray-800 w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-indigo-900">Configuration de la partie</h2>
            <form method="POST" class="flex flex-col gap-4">
                <div class="text-left">
                    <label class="font-bold text-sm uppercase text-gray-500">Mode de jeu</label>
                    <select name="game_mode" class="w-full p-4 border-2 rounded-xl mt-2 outline-none focus:border-indigo-500 font-bold text-gray-700 bg-gray-50">
                        <option value="classique">🏆 Classique (Points au temps)</option>
                        <option value="br">⚔️ Battle Royale (Élimination à chaque question)</option>
                    </select>
                </div>
                <button name="start_session" class="mt-4 bg-indigo-600 text-white px-10 py-4 rounded-xl font-black text-xl hover:bg-indigo-700 transition shadow-lg w-full">
                    CRÉER LE SALON
                </button>
            </form>
        </div>
    <?php else: ?>
        <p class="text-xl mb-2 italic text-indigo-200">Rejoignez sur votre téléphone avec le code :</p>
        <h1 class="text-7xl md:text-9xl font-black mb-12 tracking-widest bg-white text-indigo-600 px-12 py-6 rounded-3xl shadow-2xl"><?= htmlspecialchars($current_pin) ?></h1>
        
        <div class="w-full max-w-5xl bg-indigo-800 bg-opacity-40 p-8 rounded-3xl border-2 border-indigo-400 border-dashed">
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <h3 class="text-2xl font-bold uppercase tracking-widest">Joueurs : <span id="count" class="text-yellow-400 text-3xl font-black">0</span></h3>
                <button id="go-btn" onclick="startGame()" class="hidden bg-green-500 hover:bg-green-400 text-white px-10 py-4 rounded-2xl font-black text-xl transition shadow-lg transform hover:scale-105">LANCER LE JEU !</button>
            </div>
            <div id="list" class="flex flex-wrap gap-6 justify-center"></div>
        </div>

        <script>
            function refresh() {
                fetch(`api_live.php?action=get_state&pin=<?= $current_pin ?>`)
                .then(r => r.json())
                .then(data => {
                    const list = document.getElementById('list');
                    const count = document.getElementById('count');
                    const btn = document.getElementById('go-btn');
                    
                    count.innerText = data.players.length;
                    if(data.players.length > 0) btn.classList.remove('hidden');
                    else btn.classList.add('hidden');

                    list.innerHTML = '';
                    data.players.forEach(p => {
                        const div = document.createElement('div');
                        div.className = 'bg-white text-indigo-900 px-6 py-3 rounded-xl font-black text-lg shadow-md animate-bounce';
                        div.innerText = p.nickname;
                        list.appendChild(div);
                    });
                });
            }
            
            function startGame() {
                fetch(`api_live.php?action=start_game&pin=<?= $current_pin ?>&quiz_id=<?= $quiz_id ?>`)
                .then(() => window.location.href = `host_screen.php?pin=<?= $current_pin ?>`);
            }

            setInterval(refresh, 1500);
            refresh();
        </script>
    <?php endif; ?>
</body>
</html>