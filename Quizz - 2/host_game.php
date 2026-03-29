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
    <style>
        @keyframes rainbow { 100% { filter: hue-rotate(360deg); } }
        .aura-rainbow { position: absolute; top: -15%; left: -15%; width: 130%; height: 130%; border-radius: 50%; box-shadow: 0 0 20px 5px #f43f5e, inset 0 0 20px 5px #f43f5e; animation: rainbow 2.5s linear infinite; z-index: 5; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        .aura-float { animation: float 3s ease-in-out infinite; }
        .neon-vip { text-shadow: 0 0 5px #fff, 0 0 10px #facc15, 0 0 20px #facc15; color: #facc15; }
    </style>
</head>
<body class="bg-indigo-600 min-h-screen text-white flex flex-col items-center p-10 font-sans">
    
    <img src="images/logo.png" alt="Logo" class="h-20 mb-6 drop-shadow-lg absolute top-6 left-6">

    <?php if (!$current_pin): ?>
        <div class="bg-white p-8 rounded-2xl shadow-xl text-center text-gray-800 w-full max-w-md mt-16">
            <h2 class="text-2xl font-bold mb-6 text-indigo-900">Configuration de la partie</h2>
            <form method="POST" class="flex flex-col gap-4">
                <div class="text-left">
                    <label class="font-bold text-sm uppercase text-gray-500">Mode de jeu</label>
                    <select name="game_mode" class="w-full p-4 border-2 rounded-xl mt-2 outline-none focus:border-indigo-500 font-bold text-gray-700 bg-gray-50">
                        <option value="classique">🏆 Classique (Points au temps)</option>
                        <option value="br">⚔️ Battle Royale (Élimination)</option>
                        <option value="survie">❤️ Survie (3 Erreurs max)</option>
                    </select>
                </div>
                <button name="start_session" class="mt-4 bg-indigo-600 text-white px-10 py-4 rounded-xl font-black text-xl hover:bg-indigo-700 transition shadow-lg w-full">
                    CRÉER LE SALON
                </button>
            </form>
        </div>
    <?php else: ?>
        <p class="text-xl mb-2 italic text-indigo-200 mt-10">Rejoignez sur votre téléphone avec le code :</p>
        <h1 class="text-7xl md:text-9xl font-black mb-12 tracking-widest bg-white text-indigo-600 px-12 py-6 rounded-3xl shadow-2xl"><?= htmlspecialchars($current_pin) ?></h1>
        
        <div class="w-full max-w-6xl bg-indigo-800 bg-opacity-40 p-8 rounded-3xl border-2 border-indigo-400 border-dashed">
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <h3 class="text-2xl font-bold uppercase tracking-widest">Joueurs en attente : <span id="count" class="text-yellow-400 text-3xl font-black">0</span></h3>
                <button id="go-btn" onclick="startGame()" class="hidden bg-green-500 hover:bg-green-400 text-white px-10 py-4 rounded-2xl font-black text-xl transition shadow-lg transform hover:scale-105">LANCER LE JEU !</button>
            </div>
            <div id="list" class="flex flex-wrap gap-8 justify-center"></div>
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
                        let auraHtml = '';
                        let floatClass = p.aura == 7 ? 'aura-float' : '';
                        
                        if (p.aura > 0 && p.aura <= 5) {
                            let zAura = (p.aura == 1 || p.aura == 5) ? 30 : 5;
                            auraHtml = `<img src="personnage/aura/aura${p.aura}.png" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] object-contain animate-pulse" style="z-index: ${zAura};">`;
                        } else if (p.aura == 6) {
                            auraHtml = `<div class="aura-rainbow"></div>`;
                        }

                        let badge = p.is_member ? `<div class="absolute -bottom-2 -right-2 bg-yellow-400 text-black text-[12px] font-black w-6 h-6 flex items-center justify-center rounded-full border-2 border-white z-40 shadow-lg" title="Joueur VIP">★</div>` : '';
                        let nameClass = p.is_member ? 'neon-vip' : 'text-indigo-900';

                        list.innerHTML += `
                            <div class="bg-white bg-opacity-90 p-4 rounded-2xl font-bold text-center shadow-lg transform transition hover:-translate-y-2 flex flex-col items-center">
                                <div class="relative w-20 h-20 bg-gray-100 rounded-full shadow-inner overflow-visible border-2 border-indigo-200 mb-3 flex items-end justify-center ${floatClass}">
                                    ${auraHtml}
                                    <div class="relative w-full h-full overflow-hidden rounded-full flex items-end justify-center">
                                        <img src="personnage/tenue/tenue${p.outfit}.png" class="absolute w-[90%] h-[90%] object-contain bottom-0" style="z-index: 10;">
                                        <img src="personnage/cheveux/cheveux${p.hair}.png" class="absolute w-[90%] h-[90%] object-contain bottom-0" style="z-index: 20;">
                                    </div>
                                    ${badge}
                                </div>
                                <span class="truncate text-sm uppercase tracking-widest px-2 ${nameClass}">${p.nickname}</span>
                            </div>`;
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