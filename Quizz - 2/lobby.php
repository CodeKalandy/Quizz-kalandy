<?php
require_once 'db.php';
$pin = $_GET['pin'] ?? '';
$default_nick = isset($_SESSION['username']) ? $_SESSION['username'] : '';
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
        .preview-container { 
            width: 150px; 
            height: 150px; 
            position: relative; 
            margin: 0 auto 20px; 
            background: #f3f4f6; 
            border-radius: 20px; 
            overflow: hidden; 
            border: 4px solid #facc15; 
        }
        .layer { 
            position: absolute; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            display: block;
        }
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
                <img id="layer-beard" src="" class="layer" style="z-index: 25;">
                <img id="layer-top" src="" class="layer" style="z-index: 30;">
                <img id="layer-jacket" src="" class="layer" style="z-index: 35;">
                <img id="layer-hair" src="" class="layer" style="z-index: 40;">
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
                    <span class="text-xs font-black text-indigo-900 uppercase w-20">Bouche</span>
                    <div class="flex gap-2 items-center">
                        <button onclick="changeLayer('mouth', 'type', -1)" class="arrow-btn">◀</button>
                        <span id="lbl-mouth-type" class="text-sm font-bold w-12 text-center text-indigo-600">1</span>
                        <button onclick="changeLayer('mouth', 'type', 1)" class="arrow-btn">▶</button>
                    </div>
                </div>

                <div class="flex items-center justify-between bg-white/90 p-3 rounded-xl border border-indigo-100 shadow-sm">
                    <span class="text-xs font-black text-indigo-900 uppercase w-20">Barbe</span>
                    <div class="flex gap-2 items-center">
                        <button onclick="changeLayer('beard', 'type', -1)" class="arrow-btn">◀</button>
                        <span id="lbl-beard-type" class="text-sm font-bold w-6 text-center text-indigo-600">Ø</span>
                        <button onclick="changeLayer('beard', 'type', 1)" class="arrow-btn">▶</button>
                    </div>
                    <div class="flex gap-1 items-center border-l-2 border-indigo-100 pl-2">
                        <button onclick="changeLayer('beard', 'color', -1)" class="arrow-btn !bg-pink-100 !text-pink-600">◀</button>
                        <button onclick="changeLayer('beard', 'color', 1)" class="arrow-btn !bg-pink-100 !text-pink-600">▶</button>
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
                        <button onclick="changeLayer('top', 'color', -1)" class="arrow-btn !bg-pink-100 !text-pink-600">◀</button>
                        <button onclick="changeLayer('top', 'color', 1)" class="arrow-btn !bg-pink-100 !text-pink-600">▶</button>
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
                        <button onclick="changeLayer('jacket', 'color', -1)" class="arrow-btn !bg-pink-100 !text-pink-600">◀</button>
                        <button onclick="changeLayer('jacket', 'color', 1)" class="arrow-btn !bg-pink-100 !text-pink-600">▶</button>
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
                        <button onclick="changeLayer('hair', 'color', -1)" class="arrow-btn !bg-pink-100 !text-pink-600">◀</button>
                        <button onclick="changeLayer('hair', 'color', 1)" class="arrow-btn !bg-pink-100 !text-pink-600">▶</button>
                    </div>
                </div>

            </div>

            <button onclick="join()" class="w-full mt-6 bg-yellow-400 hover:bg-yellow-300 text-indigo-900 py-4 rounded-2xl font-black text-lg shadow-[0_4px_0_0_#ca8a04] active:shadow-none active:translate-y-1 transition-all uppercase tracking-widest">
                Rejoindre la salle !
            </button>
        </div>
    </div>

    <script>
        // Le lien pointe maintenant vers le dossier de ton serveur local
        const basePath = "personnage/images/sections/";
        
        const skinColors = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];
        const commonColors = [1, 8, 11, 15];
        const clothesColors = [1, 19, 31, 40];

        let state = {
            skin: { type: 1, colorIdx: 0, maxType: 1, hasColor: true, colors: skinColors, path: "Skin/1" },
            eyes: { type: 1, colorIdx: 0, maxType: 27, hasColor: false, path: "Eyes" },
            mouth: { type: 1, colorIdx: 0, maxType: 21, hasColor: false, path: "Mouth" },
            beard: { type: 0, colorIdx: 0, maxType: 11, hasColor: true, colors: commonColors, path: "Beards" },
            top: { type: 1, colorIdx: 0, maxType: 20, hasColor: true, colors: clothesColors, path: "Top/Men" },
            jacket: { type: 0, colorIdx: 0, maxType: 19, hasColor: true, colors: clothesColors, path: "Jacket/Men" }, 
            hair: { type: 1, colorIdx: 0, maxType: 21, hasColor: true, colors: commonColors, path: "Hair/Front/short" } 
        };

        function updateVisual(category) {
            const el = document.getElementById(`layer-${category}`);
            const data = state[category];
            
            if (data.type === 0) {
                el.src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=";
                const lbl = document.getElementById(`lbl-${category}-type`);
                if(lbl) lbl.innerText = "Ø";
                return;
            }

            const lblType = document.getElementById(`lbl-${category}-type`);
            if(lblType) lblType.innerText = data.type;
            const lblColor = document.getElementById(`lbl-${category}-color`);
            if(lblColor) lblColor.innerText = data.colorIdx + 1;

            let finalUrl = `${basePath}${data.path}`;
            if (data.hasColor) {
                let actualColorCode = data.colors[data.colorIdx];
                if (category === 'skin') {
                    finalUrl = `${basePath}Skin/1/${actualColorCode}.png`; 
                } else {
                    finalUrl = `${finalUrl}/${data.type}/${actualColorCode}.png`;
                }
            } else {
                finalUrl = `${finalUrl}/${data.type}.png`;
            }

            el.src = finalUrl;
        }

        function changeLayer(category, prop, direction) {
            let data = state[category];
            if (prop === 'type') {
                data.type += direction;
                let min = (category === 'jacket' || category === 'beard') ? 0 : 1;
                if (data.type > data.maxType) data.type = min;
                if (data.type < min) data.type = data.maxType;
            } else if (prop === 'color' && data.hasColor) {
                data.colorIdx += direction;
                if (data.colorIdx >= data.colors.length) data.colorIdx = 0;
                if (data.colorIdx < 0) data.colorIdx = data.colors.length - 1;
            }
            updateVisual(category);
        }

        window.onload = () => {
            Object.keys(state).forEach(cat => updateVisual(cat));
        };

        function join() {
            const nick = document.getElementById('nick').value.trim();
            if(!nick) return alert("Il nous faut ton pseudo !");

            const payload = { 
                nickname: nick, 
                skin: state.skin.type, skinColor: state.skin.colors[state.skin.colorIdx],
                eyes: state.eyes.type, mouth: state.mouth.type,
                beard: state.beard.type, beardColor: state.beard.colors[state.beard.colorIdx],
                top: state.top.type, topColor: state.top.colors[state.top.colorIdx],
                jacket: state.jacket.type, jacketColor: state.jacket.colors[state.jacket.colorIdx],
                hair: state.hair.type, hairColor: state.hair.colors[state.hair.colorIdx]
            };

            fetch(`api_live?action=join&pin=<?= htmlspecialchars($pin) ?>`, {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            }).then(r => r.json()).then(data => {
                if(data.status === 'success') window.location.href = `play`;
            });
        }
    </script>
</body>
</html>