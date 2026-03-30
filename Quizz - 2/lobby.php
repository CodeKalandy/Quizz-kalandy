<?php
require_once 'db.php';
$pin = $_GET['pin'] ?? '';
$default_nick = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$is_member = isset($_SESSION['user_id']) ? 'true' : 'false';

// Charger le Bernard favori si connecté
$fav_hair = 1; $fav_outfit = 1; $fav_aura = 0; $fav_effect = 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap" rel="stylesheet">
    <title>Ton Bernard - Bernard Quizz</title>
    <style>
        .preview-container { width: 150px; height: 150px; position: relative; margin: 0 auto 20px; background: #f3f4f6; border-radius: 20px; overflow: visible; border: 4px solid #facc15; }
        .layer { position: absolute; bottom: 0; width: 100%; height: 100%; object-contain: center; }
        .arrow-btn { background: #e0e7ff; color: #312e81; padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-weight: 900; transition: all 0.2s; }
        .arrow-btn:hover { background: #c7d2fe; transform: scale(1.1); }
        .arrow-btn:active { transform: scale(0.9); }
    </style>
</head>
<body class="bg-indigo-900 min-h-screen text-white flex flex-col items-center p-4 font-sans pb-16 relative overflow-hidden">

    <div class="fixed top-10 left-10 text-7xl text-white/5 font-black z-0 pointer-events-none">✦</div>
    <div class="fixed bottom-20 right-20 text-9xl text-white/5 font-black z-0 pointer-events-none">⬢</div>

    <div class="relative z-10 w-full max-w-md">
        <h1 class="text-3xl font-black mb-6 uppercase tracking-widest text-center text-yellow-400 mt-4 drop-shadow-lg" style="font-family: 'Caveat', cursive; font-size: 2.5rem;">Crée ton Bernard</h1>

        <div class="bg-white/10 backdrop-blur-md p-6 rounded-3xl shadow-2xl w-full border border-white/20">
            
            <div class="preview-container shadow-2xl" id="char-wrapper">
                <img id="layer-skin" src="" class="layer" style="z-index: 10;">
                <img id="layer-mouth" src="" class="layer" style="z-index: 20;">
                <img id="layer-eyes" src="" class="layer" style="z-index: 21;">
                <img id="layer-top" src="" class="layer" style="z-index: 30;">
                <img id="layer-jacket" src="" class="layer" style="z-index: 35;">
                <img id="layer-hair" src="" class="layer" style="z-index: 40;">
                <img id="layer-beard" src="" class="layer" style="z-index: 45;">
            </div>

            <input type="text" id="nick" maxlength="12" placeholder="TON PSEUDO" value="<?= htmlspecialchars($default_nick) ?>"
                   class="w-full p-4 bg-white/90 border-none rounded-2xl font-black text-center text-indigo-900 focus:ring-4 focus:ring-yellow-400 outline-none transition-all mb-6 shadow-inner">

            <div class="space-y-3">
                
                <div class="flex items-center justify-between bg-white/90 p-3 rounded-xl border border-indigo-100 shadow-sm">
                    <span class="text-xs font-black text-indigo-900 uppercase w-20">Peau</span>
                    <div class="flex gap-2 items-center">
                        <button onclick="changeLayer('skin', 'color', -1)" class="arrow-btn">◀</button>
                        <span id="lbl-skin-color" class="text-sm font-bold w-12 text-center text-indigo-600">1</span>
                        <button onclick="changeLayer('skin', 'color', 1)" class="arrow-btn">▶</button>
                    </div>
                </div>

                <div class="flex items-center justify-between bg-white/90 p-3 rounded-xl border border-indigo-100 shadow-sm">
                    <span class="text-xs font-black text-indigo-900 uppercase w-20">Yeux</span>
                    <div class="flex gap-2 items-center">
                        <button onclick="changeLayer('eyes', 'type', -1)" class="arrow-btn">◀</button>
                        <span id="lbl-eyes-type" class="text-sm font-bold w-12 text-center text-indigo-600">1</span>
                        <button onclick="changeLayer('eyes', 'type', 1)" class="arrow-btn">▶</button>
                    </div>
                </div>

                <div class="flex items-center justify-between bg-white/90 p-3 rounded-xl border border-indigo-100 shadow-sm">
                    <span class="text-xs font-black text-indigo-900 uppercase w-20">T-Shirt</span>
                    <div class="flex gap-2 items-center">
                        <button onclick="changeLayer('top', 'type', -1)" class="arrow-btn">◀</button>
                        <span id="lbl-top-type" class="text-sm font-bold w-6 text-center text-indigo-600">1</span>
                        <button onclick="changeLayer('top', 'type', 1)" class="arrow-btn">▶</button>
                    </div>
                    <div class="flex gap-1 items-center border-l-2 border-indigo-100 pl-2">
                        <button onclick="changeLayer('top', 'color', -1)" class="arrow-btn !bg-pink-100 !text-pink-600 hover:!bg-pink-200">◀</button>
                        <button onclick="changeLayer('top', 'color', 1)" class="arrow-btn !bg-pink-100 !text-pink-600 hover:!bg-pink-200">▶</button>
                    </div>
                </div>

                <div class="flex items-center justify-between bg-white/90 p-3 rounded-xl border border-indigo-100 shadow-sm">
                    <span class="text-xs font-black text-indigo-900 uppercase w-20">Veste</span>
                    <div class="flex gap-2 items-center">
                        <button onclick="changeLayer('jacket', 'type', -1)" class="arrow-btn">◀</button>
                        <span id="lbl-jacket-type" class="text-sm font-bold w-6 text-center text-indigo-600">Ø</span>
                        <button onclick="changeLayer('jacket', 'type', 1)" class="arrow-btn">▶</button>
                    </div>
                    <div class="flex gap-1 items-center border-l-2 border-indigo-100 pl-2">
                        <button onclick="changeLayer('jacket', 'color', -1)" class="arrow-btn !bg-pink-100 !text-pink-600 hover:!bg-pink-200">◀</button>
                        <button onclick="changeLayer('jacket', 'color', 1)" class="arrow-btn !bg-pink-100 !text-pink-600 hover:!bg-pink-200">▶</button>
                    </div>
                </div>

                <div class="flex items-center justify-between bg-white/90 p-3 rounded-xl border border-indigo-100 shadow-sm">
                    <span class="text-xs font-black text-indigo-900 uppercase w-20">Cheveux</span>
                    <div class="flex gap-2 items-center">
                        <button onclick="changeLayer('hair', 'type', -1)" class="arrow-btn">◀</button>
                        <span id="lbl-hair-type" class="text-sm font-bold w-6 text-center text-indigo-600">1</span>
                        <button onclick="changeLayer('hair', 'type', 1)" class="arrow-btn">▶</button>
                    </div>
                    <div class="flex gap-1 items-center border-l-2 border-indigo-100 pl-2">
                        <button onclick="changeLayer('hair', 'color', -1)" class="arrow-btn !bg-pink-100 !text-pink-600 hover:!bg-pink-200">◀</button>
                        <button onclick="changeLayer('hair', 'color', 1)" class="arrow-btn !bg-pink-100 !text-pink-600 hover:!bg-pink-200">▶</button>
                    </div>
                </div>

            </div>

            <button onclick="join()" class="w-full mt-6 bg-yellow-400 hover:bg-yellow-300 text-indigo-900 py-4 rounded-2xl font-black text-lg shadow-[0_4px_0_0_#ca8a04] active:shadow-none active:translate-y-1 transition-all uppercase tracking-widest">
                Rejoindre la salle !
            </button>
        </div>
    </div>

    <script>
        // LE BON LIEN GITHUB !
        const basePath = "https://codekalandy.github.io/Quizz-kalandy/Quizz%20-%202/personnage/images/sections/";
        const pinknoseColors = [1, 8, 11, 12, 13, 14, 15, 19, 31, 40]; 

        let state = {
            skin: { type: 1, colorIdx: 0, maxType: 1, hasColor: true, path: "Skin/1" },
            eyes: { type: 1, colorIdx: 0, maxType: 27, hasColor: false, path: "Eyes" },
            mouth: { type: 1, colorIdx: 0, maxType: 21, hasColor: false, path: "Mouth" },
            top: { type: 1, colorIdx: 0, maxType: 20, hasColor: true, path: "Top/Men" },
            jacket: { type: 0, colorIdx: 0, maxType: 19, hasColor: true, path: "Jacket/Men" }, 
            hair: { type: 1, colorIdx: 0, maxType: 21, hasColor: true, path: "Hair/Front/short" } 
        };

        function updateVisual(category) {
            const el = document.getElementById(`layer-${category}`);
            const data = state[category];
            
            if (data.type === 0) {
                el.src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=";
                document.getElementById(`lbl-${category}-type`).innerText = "Ø";
                return;
            }

            const lblType = document.getElementById(`lbl-${category}-type`);
            if(lblType) lblType.innerText = data.type;
            const lblColor = document.getElementById(`lbl-${category}-color`);
            if(lblColor) lblColor.innerText = data.colorIdx + 1;

            let finalUrl = `${basePath}${data.path}`;
            if (data.hasColor) {
                let actualColorCode = pinknoseColors[data.colorIdx];
                if (category === 'skin') {
                    finalUrl = `${basePath}Skin/1/${data.colorIdx + 1}.png`; 
                } else {
                    finalUrl = `${finalUrl}/${data.type}/${actualColorCode}.png`;
                }
            } else {
                finalUrl = `${finalUrl}/${data.type}.png`;
            }

            el.src = finalUrl;
            
            el.onerror = function() {
                this.src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="; 
            };
        }

        function changeLayer(category, prop, direction) {
            let data = state[category];
            if (prop === 'type') {
                data.type += direction;
                if (data.type > data.maxType) data.type = (category === 'jacket') ? 0 : 1;
                if (data.type < (category === 'jacket' ? 0 : 1)) data.type = data.maxType;
            } else if (prop === 'color' && data.hasColor) {
                data.colorIdx += direction;
                if (category === 'skin') {
                    if (data.colorIdx > 14) data.colorIdx = 0; 
                    if (data.colorIdx < 0) data.colorIdx = 14;
                } else {
                    if (data.colorIdx >= pinknoseColors.length) data.colorIdx = 0;
                    if (data.colorIdx < 0) data.colorIdx = pinknoseColors.length - 1;
                }
            }
            updateVisual(category);
        }

        // Initialisation des images
        window.onload = () => {
            Object.keys(state).forEach(cat => updateVisual(cat));
        };

        function join() {
            const nick = document.getElementById('nick').value.trim();
            if(!nick) return alert("Il nous faut ton pseudo !");
            event.target.innerText = "Connexion..."; event.target.disabled = true;

            fetch(`api_live?action=join&pin=<?= htmlspecialchars($pin) ?>`, {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nickname: nick, is_member: <?= $is_member ?> })
            }).then(r => r.json()).then(data => {
                if(data.status === 'success') {
                    window.location.href = `play`;
                }
            });
        }
    </script>
</body>
</html>