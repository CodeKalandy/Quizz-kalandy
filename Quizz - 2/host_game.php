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
        'mode'            => $mode,
        'eliminated'      => [],
        'players'         => [],
        'scores'          => new stdClass(),
        'correct_counts'  => new stdClass(),
        'wrong_counts'    => new stdClass(),
        'response_times'  => new stdClass(),
        'streaks'         => new stdClass(),
        'hearts'          => new stdClass(),
        'answers'         => new stdClass(),
        'chat'            => [],
        'status'          => 'lobby',
        'current_q_index' => -1,
        'last_update'     => time()
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap" rel="stylesheet">
    <title>Salon d'attente - Bernard Quizz</title>
    <style>
        /* === THEME GLOBAL === */
        body {
            background-color: #0f172a;
            background-image:
                radial-gradient(at 0% 0%, #1e1b4b 0px, transparent 50%),
                radial-gradient(at 100% 100%, #312e81 0px, transparent 50%);
            background-attachment: fixed;
            color: white;
            font-family: sans-serif;
        }

        .game-card {
            background-color: #1e1b4b;
            border: 4px solid #312e81;
            border-radius: 1.5rem;
            box-shadow: 0 8px 0 0 #0b0f19;
        }

        .title-text {
            font-family: 'Caveat', cursive;
            text-shadow: 3px 3px 0px #312e81;
            letter-spacing: 2px;
        }

        /* PIN géant */
        .pin-display {
            font-family: 'Caveat', cursive;
            background-color: #1e1b4b;
            border: 6px solid #facc15;
            box-shadow: 0 10px 0 0 #ca8a04, 0 0 60px rgba(250,204,21,0.2);
            border-radius: 2rem;
            color: #facc15;
            text-shadow: 3px 3px 0px #92400e;
            letter-spacing: 8px;
        }

        /* Boutons style jeu vidéo */
        .game-btn {
            background-color: #10b981; color: white;
            border: 4px solid #047857;
            box-shadow: 0 8px 0 0 #064e3b;
            border-radius: 1.5rem;
            font-weight: 900; font-size: 1.4rem;
            text-transform: uppercase; letter-spacing: 2px;
            padding: 1rem 2.5rem;
            transition: all 0.1s;
            text-shadow: 2px 2px 0px #065f46;
            cursor: pointer;
        }
        .game-btn:hover { background-color: #34d399; }
        .game-btn:active { transform: translateY(8px); box-shadow: 0 0px 0 0 #064e3b; }
        .game-btn:disabled { background-color: #374151; border-color: #1f2937; box-shadow: 0 8px 0 0 #111827; color: #6b7280; cursor: not-allowed; text-shadow: none; transform: none; }

        .mode-card {
            background-color: #2e2a72; border: 3px solid #4338ca;
            border-radius: 1rem; cursor: pointer; transition: all 0.15s;
        }
        .mode-card:hover { border-color: #facc15; background-color: #3730a3; transform: translateY(-2px); }
        .mode-card.selected { border-color: #facc15; background-color: #3730a3; box-shadow: 0 4px 0 0 #a16207, 0 0 20px rgba(250,204,21,0.2); }

        /* Avatar - conteneur circulaire */
        .avatar-circle {
            position: relative;
            background-color: #312e81;
            border-radius: 50%;
            border: 3px solid #4338ca;
            overflow: visible;
            flex-shrink: 0;
        }
        .avatar-circle .avatar-inner {
            position: absolute; inset: 0;
            border-radius: 50%;
            overflow: hidden;
        }
        .avatar-layer {
            position: absolute; top: 0; left: 0;
            width: 100%; height: 100%;
            object-fit: contain;
        }

        /* Aura */
        .aura-wrap { animation: pulseAura 2.5s ease-in-out infinite alternate; }
        @keyframes pulseAura { 0% { opacity: 0.4; } 100% { opacity: 1; } }
        .effect-rainbow-border { animation: rainbowBorder 3s linear infinite; }
        @keyframes rainbowBorder {
            0%   { border-color: #ef4444; box-shadow: 0 4px 0 0 #991b1b, 0 0 20px #ef4444; }
            25%  { border-color: #f59e0b; box-shadow: 0 4px 0 0 #92400e, 0 0 20px #f59e0b; }
            50%  { border-color: #10b981; box-shadow: 0 4px 0 0 #065f46, 0 0 20px #10b981; }
            75%  { border-color: #3b82f6; box-shadow: 0 4px 0 0 #1e40af, 0 0 20px #3b82f6; }
            100% { border-color: #ef4444; box-shadow: 0 4px 0 0 #991b1b, 0 0 20px #ef4444; }
        }
        .effect-levitate { animation: levitate 3s ease-in-out infinite; }
        @keyframes levitate { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }

        /* Badge VIP */
        .neon-vip { text-shadow: 0 0 5px #fff, 0 0 10px #facc15, 0 0 20px #facc15; color: #facc15; }

        /* Particules */
        .particle {
            position: fixed; background: rgba(255,255,255,0.03);
            border-radius: 50%; animation: drift infinite linear; pointer-events: none; z-index: 0;
        }
        @keyframes drift { from{transform:translateY(100vh) rotate(0deg)} to{transform:translateY(-100vh) rotate(360deg)} }

        /* Animation d'entrée joueur */
        @keyframes popIn { 0%{transform:scale(0.3);opacity:0} 80%{transform:scale(1.1);opacity:1} 100%{transform:scale(1);opacity:1} }
        .player-card { animation: popIn 0.4s ease-out forwards; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center p-6 relative">

    <div class="particle" style="width:120px;height:120px;left:5%;animation-duration:25s;"></div>
    <div class="particle" style="width:200px;height:200px;left:80%;animation-duration:35s;"></div>
    <div class="particle" style="width:80px;height:80px;left:45%;animation-duration:20s;"></div>

    <div class="relative z-10 w-full max-w-6xl flex flex-col items-center">

        <!-- Logo -->
        <img src="images/logo.png" alt="Logo" class="h-16 mb-8 drop-shadow-lg" onerror="this.style.display='none'">

        <?php if (!$current_pin): ?>
        <!-- ===== FORMULAIRE DE CRÉATION ===== -->
        <div class="game-card p-8 w-full max-w-lg">
            <div class="text-center mb-8">
                <span class="title-text text-4xl text-yellow-400 block">Nouvelle partie</span>
                <p class="text-indigo-300 text-sm mt-2 uppercase tracking-widest font-bold">Choisis ton mode de jeu</p>
            </div>

            <form method="POST" class="flex flex-col gap-4">
                <input type="hidden" name="game_mode" id="selected_mode" value="classique">

                <div onclick="selectMode('classique')" class="mode-card selected p-5 flex items-start gap-4" id="mode-classique">
                    <span class="text-4xl">🏆</span>
                    <div>
                        <p class="font-black text-white text-lg uppercase tracking-wide">Classique</p>
                        <p class="text-indigo-300 text-sm mt-1">Points au temps — plus tu réponds vite, plus tu marques. Streaks de bonnes réponses = bonus.</p>
                    </div>
                </div>

                <div onclick="selectMode('br')" class="mode-card p-5 flex items-start gap-4" id="mode-br">
                    <span class="text-4xl">⚔️</span>
                    <div>
                        <p class="font-black text-white text-lg uppercase tracking-wide">Battle Royale</p>
                        <p class="text-indigo-300 text-sm mt-1">Le dernier de chaque manche est éliminé. Survie jusqu'au bout.</p>
                    </div>
                </div>

                <div onclick="selectMode('survie')" class="mode-card p-5 flex items-start gap-4" id="mode-survie">
                    <span class="text-4xl">❤️</span>
                    <div>
                        <p class="font-black text-white text-lg uppercase tracking-wide">Survie</p>
                        <p class="text-indigo-300 text-sm mt-1">Chaque joueur a 3 cœurs. Une mauvaise réponse = -1 cœur. 0 cœurs = éliminé.</p>
                    </div>
                </div>

                <button type="submit" name="start_session" class="game-btn mt-4">
                    Créer le salon
                </button>
            </form>
        </div>

        <script>
            function selectMode(mode) {
                document.getElementById('selected_mode').value = mode;
                document.querySelectorAll('.mode-card').forEach(c => c.classList.remove('selected'));
                document.getElementById('mode-' + mode).classList.add('selected');
            }
        </script>

        <?php else: ?>
        <!-- ===== SALON D'ATTENTE ===== -->

        <!-- PIN + titre -->
        <div class="text-center mb-8">
            <p class="text-indigo-300 uppercase tracking-widest font-bold text-sm mb-3">Rejoins sur bernardquizz.fr avec le code :</p>
            <div class="pin-display text-7xl md:text-9xl font-black px-10 py-4 inline-block">
                <?= htmlspecialchars($current_pin) ?>
            </div>
        </div>

        <!-- Zone joueurs -->
        <div class="game-card p-6 w-full">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <h3 class="title-text text-2xl text-white">
                    Joueurs connectés :
                    <span id="count" class="text-yellow-400 text-4xl ml-2">0</span>
                </h3>
                <button id="go-btn" onclick="startGame()"
                        class="game-btn hidden">
                    Lancer la partie !
                </button>
            </div>
            <div id="list" class="flex flex-wrap gap-6 justify-center min-h-[120px]">
                <div class="flex flex-col items-center justify-center w-full text-indigo-400 text-sm font-bold uppercase tracking-widest animate-pulse">
                    En attente de joueurs...
                </div>
            </div>
        </div>

        <script>
            const basePath = "personnage/images/sections/";
            const skinColors  = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15];
            const commonColors  = [1,8,11,15];
            const clothesColors = [1,19,31,40];
            const hairStyles  = ['very_short','short','medium','long','shaved'];
            const specialThemes = [
                {key:"none",  path:"",                       max:0},
                {key:"neutral",   path:"Jacket/Men/Neutral/Men",  max:4},
                {key:"job",       path:"Jacket/Men/Job/Men",      max:17},
                {key:"antiquity", path:"Jacket/Men/Antiquity/Men",max:9},
                {key:"medieval",  path:"Jacket/Men/Medieval/Men", max:23},
                {key:"pirate",    path:"Jacket/Men/Pirate/Men",   max:6},
                {key:"halloween", path:"Jacket/Men/Halloween/Men",max:7},
                {key:"christmas", path:"Jacket/Men/Christmas/Men",max:12},
            ];

            /**
             * Construit le HTML de l'avatar avec le nouveau système de couches.
             * @param {object} p        - données joueur depuis api_live
             * @param {string} size     - ex: "72px" (largeur/hauteur du cercle)
             * @param {boolean} badge   - afficher le badge VIP
             */
            function buildAvatarHtml(p, size, showBadge = true) {
                const BLANK = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=";
                const s = parseInt(size);

                // Résolution des paths de couches
                const skinColor  = skinColors[p.skinColor ?? 0] ?? 1;
                const commonC    = commonColors[p.hairColor ?? 0] ?? 1;
                const hairStyle  = hairStyles[p.hairStyle ?? 1] ?? 'short';
                const hairType   = p.hair ?? 1;
                const beardC     = commonColors[p.beardColor ?? 0] ?? 1;
                const mustacheC  = commonColors[p.mustacheColor ?? 0] ?? 1;
                const topC       = clothesColors[p.topColor ?? 0] ?? 1;
                const jacketC    = clothesColors[p.jacketColor ?? 0] ?? 1;

                // Couches dans l'ordre z-index
                const layers = [];

                // Aura (z=0 ou z=60 selon type)
                let auraZ = 0, auraHtml = '';
                if (p.aura > 0 && p.aura <= 5) {
                    auraZ = (p.aura == 1 || p.aura == 5) ? 60 : 0;
                    auraHtml = `<img src="personnage/aura/aura${p.aura}.png" class="avatar-layer aura-wrap" style="z-index:${auraZ};" onerror="this.src='${BLANK}'">`;
                }

                // Cheveux arrière (z=5)
                let hairBackSrc = BLANK;
                if (hairType > 0) {
                    hairBackSrc = `${basePath}Hair/Back/${hairStyle}/${hairType}/${commonC}.png`;
                }

                // Peau (z=10)
                const skinSrc = `${basePath}Skin/1/${skinColor}.png`;

                // T-shirt (z=20)
                let topSrc = BLANK;
                if ((p.top ?? 1) > 0) {
                    topSrc = `${basePath}Top/Men/${p.top ?? 1}/${topC}.png`;
                }

                // Veste classique (z=30)
                let jacketSrc = BLANK;
                if ((p.jacket ?? 0) > 0) {
                    jacketSrc = `${basePath}Jacket/Men/${p.jacket}/${jacketC}.png`;
                }

                // Costume spécial (z=31) — trouve le thème actif
                let specialSrc = BLANK;
                for (let t of specialThemes) {
                    if (t.key !== 'none' && (p[t.key] ?? 0) > 0) {
                        specialSrc = `${basePath}${t.path}/${p[t.key]}.png`;
                        jacketSrc  = BLANK; // costume spécial remplace la veste
                        break;
                    }
                }

                // Barbe (z=40), Moustache (z=41)
                let beardSrc = BLANK, mustacheSrc = BLANK;
                if ((p.beard ?? 0) > 0) beardSrc = `${basePath}Beards/${p.beard}/${beardC}.png`;
                if ((p.mustache ?? 0) > 0) mustacheSrc = `${basePath}Mustaches/${p.mustache}/${mustacheC}.png`;

                // Bouche (z=50), Yeux (z=51), Sourcils (z=52), Nez (z=53)
                const mouthSrc   = `${basePath}Mouth/${p.mouth ?? 1}.png`;
                const eyesSrc    = `${basePath}Eyes/${p.eyes ?? 1}.png`;
                const eyebrowC   = commonColors[p.eyebrowColor ?? 0] ?? 1;
                const eyebrowSrc = `${basePath}Eyebrow/${p.eyebrow ?? 1}/${eyebrowC}.png`;
                const noseSrc    = `${basePath}Nose/${p.nose ?? 1}.png`;

                // Cheveux avant (z=54)
                let hairFrontSrc = BLANK;
                if (hairType > 0) {
                    hairFrontSrc = `${basePath}Hair/Front/${hairStyle}/${hairType}/${commonC}.png`;
                }

                // Effet visuel (z=70) - type 3+ = image, 1 = arc-en-ciel (CSS), 2 = lévitation (CSS)
                let effectImgSrc = BLANK;
                if ((p.effect ?? 0) >= 3) {
                    effectImgSrc = `personnage/effets/effet${p.effect}.png`;
                }

                // Classes d'effet CSS sur le wrapper
                let wrapClass = '';
                if (p.effect == 1) wrapClass += ' effect-rainbow-border';
                if (p.effect == 2) wrapClass += ' effect-levitate';

                // Badge VIP
                const badgeHtml = (showBadge && p.is_member)
                    ? `<div style="position:absolute;bottom:-4px;right:-4px;background:#facc15;color:#000;font-weight:900;width:${Math.round(s*0.3)}px;height:${Math.round(s*0.3)}px;display:flex;align-items:center;justify-content:center;border-radius:50%;border:2px solid white;z-index:50;font-size:${Math.round(s*0.12)}px;">★</div>`
                    : '';

                const nameClass = p.is_member ? 'neon-vip' : '';

                function img(src, z, extra = '') {
                    return `<img src="${src}" class="avatar-layer" style="z-index:${z};" onerror="this.src='${BLANK}'" ${extra}>`;
                }

                return `
                <div class="avatar-circle ${wrapClass}" style="width:${s}px;height:${s}px;">
                    ${auraHtml}
                    <div class="avatar-inner">
                        ${img(skinSrc, 10)}
                        ${img(topSrc, 20)}
                        ${img(jacketSrc, 30)}
                        ${img(specialSrc, 31)}
                        ${img(beardSrc, 40)}
                        ${img(mustacheSrc, 41)}
                        ${img(mouthSrc, 50)}
                        ${img(eyesSrc, 51)}
                        ${img(eyebrowSrc, 52)}
                        ${img(noseSrc, 53)}
                        ${img(hairFrontSrc, 54)}
                        ${img(effectImgSrc, 70)}
                    </div>
                    ${img(hairBackSrc, 5)}
                    ${badgeHtml}
                </div>`;
            }

            function refresh() {
                fetch(`api_live?action=get_state&pin=<?= htmlspecialchars($current_pin) ?>`)
                .then(r => r.json())
                .then(data => {
                    const list  = document.getElementById('list');
                    const count = document.getElementById('count');
                    const btn   = document.getElementById('go-btn');

                    count.innerText = data.players.length;

                    if (data.players.length > 0) {
                        btn.classList.remove('hidden');
                    } else {
                        btn.classList.add('hidden');
                    }

                    if (data.players.length === 0) {
                        list.innerHTML = `<div class="flex flex-col items-center justify-center w-full text-indigo-400 text-sm font-bold uppercase tracking-widest animate-pulse">En attente de joueurs...</div>`;
                        return;
                    }

                    list.innerHTML = '';
                    data.players.forEach(p => {
                        const avatarHtml = buildAvatarHtml(p, '80px', true);
                        const neonClass  = p.is_member ? 'neon-vip' : 'text-indigo-100';

                        const card = document.createElement('div');
                        card.className = 'player-card game-card p-4 flex flex-col items-center gap-3 min-w-[110px]';
                        card.innerHTML = `
                            ${avatarHtml}
                            <span class="text-sm font-black uppercase tracking-wider truncate max-w-[100px] ${neonClass}">${p.nickname}</span>
                        `;
                        list.appendChild(card);
                    });
                });
            }

            function startGame() {
                document.getElementById('go-btn').disabled = true;
                document.getElementById('go-btn').innerText = 'Lancement...';
                fetch(`api_live?action=start_game&pin=<?= htmlspecialchars($current_pin) ?>&quiz_id=<?= (int)$quiz_id ?>`)
                .then(() => window.location.href = `host_screen.php?pin=<?= htmlspecialchars($current_pin) ?>`);
            }

            setInterval(refresh, 1500);
            refresh();
        </script>

        <?php endif; ?>
    </div>
</body>
</html>
