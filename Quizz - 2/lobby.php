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
            overflow: visible; 
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
        .arrow-btn { background: #e0e7ff; color: #312e81; padding: 0.25rem 0.5rem; border-radius: 0.5rem; font-weight: 900; transition: all 0.2s; }
        .arrow-btn:hover { background: #c7d2fe; transform: scale(1.1); }
        .arrow-btn:active { transform: scale(0.9); }
    </style>
</head>
<body class="bg-indigo-900 min-h-screen text-white flex flex-col items-center p-4 font-sans pb-16 relative overflow-hidden">

    <div class="fixed top-10 left-10 text-7xl text-white/5 font-black z-0 pointer-events-none">✦</div>
    <div class="fixed bottom-20 right-20 text-9xl text-white/5 font-black z-0 pointer-events-none">⬢</div>

    <div class="relative z-10 w-full max-w-5xl">
        <h1 class="text-3xl font-black mb-6 uppercase tracking-widest text-center text-yellow-400 mt-4 drop-shadow-lg" style="font-family: 'Caveat', cursive; font-size: 2.5rem;">Crée ton Bernard</h1>

        <div class="bg-white/10 backdrop-blur-md p-6 rounded-3xl shadow-2xl w-full border border-white/20">
            
            <div class="preview-container shadow-2xl" id="char-wrapper">
                <img id="layer-aura" src="" class="layer" style="z-index: 0;">
                <img id="layer-hair-back" src="" class="layer" style="z-index: 5;">
                <img id="layer-skin" src="" class="layer" style="z-index: 10;">
                
                <img id="layer-mouth" src="" class="layer" style="z-index: 20;">
                <img id="layer-eyes" src="" class="layer" style="z-index: 21;">
                <img id="layer-eyebrow" src="" class="layer" style="z-index: 22;">
                <img id="layer-nose" src="" class="layer" style="z-index: 23;">
                
                <img id="layer-beard" src="" class="layer" style="z-index: 25;">
                <img id="layer-mustache" src="" class="layer" style="z-index: 26;">
                <img id="layer-top" src="" class="layer" style="z-index: 30;">
                <img id="layer-jacket" src="" class="layer" style="z-index: 31;">
                
                <img id="layer-special" src="" class="layer" style="z-index: 38;">
                
                <img id="layer-hair-front" src="" class="layer" style="z-index: 40;">
                <img id="layer-effect" src="" class="layer" style="z-index: 50;">
            </div>

            <input type="text" id="nick" maxlength="12" placeholder="TON PSEUDO" value="<?= htmlspecialchars($default_nick) ?>"
                   class="max-w-md mx-auto block w-full p-4 bg-white/90 border-none rounded-2xl font-black text-center text-indigo-900 focus:ring-4 focus:ring-yellow-400 outline-none transition-all mb-6 shadow-inner">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="space-y-3">
                    <h2 class="text-2xl font-bold text-yellow-400 mb-4 text-center font-caveat border-b border-yellow-400/30 pb-2">Personnage</h2>
                    
                    <div class="flex items-center justify-between bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm">
                        <span class="text-xs font-black text-indigo-900 uppercase w-20">Peau</span>
                        <div class="flex gap-2 items-center">
                            <button onclick="changeLayer('skin', 'color', -1)" class="arrow-btn">◀</button>
                            <span id="lbl-skin-color" class="text-sm font-bold w-6 text-center text-indigo-600">1</span>
                            <button onclick="changeLayer('skin', 'color', 1)" class="arrow-btn">▶</button>
                        </div>
                    </div>

                    <div class="bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm space-y-2 mt-2">
                        <span class="text-xs font-black text-indigo-900 uppercase block">Cheveux</span>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-gray-500 uppercase">Longueur</span>
                            <div class="flex gap-1 items-center">
                                <button onclick="changeLayer('hair', 'style', -1)" class="arrow-btn !py-1 !px-2">◀</button>
                                <span id="lbl-hair-style" class="text-[10px] font-bold w-16 text-center text-indigo-600 uppercase">COURT</span>
                                <button onclick="changeLayer('hair', 'style', 1)" class="arrow-btn !py-1 !px-2">▶</button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-gray-500 uppercase">Coupe & Coul.</span>
                            <div class="flex gap-1 items-center">
                                <button onclick="changeLayer('hair', 'type', -1)" class="arrow-btn !py-1 !px-2">◀</button>
                                <span id="lbl-hair-type" class="text-xs font-bold w-4 text-center text-indigo-600">1</span>
                                <button onclick="changeLayer('hair', 'type', 1)" class="arrow-btn !py-1 !px-2">▶</button>
                            </div>
                            <div class="flex gap-1 items-center border-l-2 border-indigo-100 pl-1">
                                <button onclick="changeLayer('hair', 'color', -1)" class="arrow-btn !bg-pink-100 !text-pink-600 !py-1 !px-2">◀</button>
                                <button onclick="changeLayer('hair', 'color', 1)" class="arrow-btn !bg-pink-100 !text-pink-600 !py-1 !px-2">▶</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <h2 class="text-2xl font-bold text-yellow-400 mb-4 text-center font-caveat border-b border-yellow-400/30 pb-2">Pilosité</h2>

                    <div class="flex items-center justify-between bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm">
                        <span class="text-xs font-black text-indigo-900 uppercase w-20">Barbe</span>
                        <div class="flex gap-1 items-center">
                            <button onclick="changeLayer('beard', 'type', -1)" class="arrow-btn">◀</button>
                            <span id="lbl-beard-type" class="text-sm font-bold w-4 text-center text-indigo-600">Ø</span>
                            <button onclick="changeLayer('beard', 'type', 1)" class="arrow-btn">▶</button>
                        </div>
                        <div class="flex gap-1 items-center border-l-2 border-indigo-100 pl-1">
                            <button onclick="changeLayer('beard', 'color', -1)" class="arrow-btn !bg-pink-100 !text-pink-600">◀</button>
                            <button onclick="changeLayer('beard', 'color', 1)" class="arrow-btn !bg-pink-100 !text-pink-600">▶</button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm">
                        <span class="text-xs font-black text-indigo-900 uppercase w-20">Moustache</span>
                        <div class="flex gap-1 items-center">
                            <button onclick="changeLayer('mustache', 'type', -1)" class="arrow-btn">◀</button>
                            <span id="lbl-mustache-type" class="text-sm font-bold w-4 text-center text-indigo-600">Ø</span>
                            <button onclick="changeLayer('mustache', 'type', 1)" class="arrow-btn">▶</button>
                        </div>
                        <div class="flex gap-1 items-center border-l-2 border-indigo-100 pl-1">
                            <button onclick="changeLayer('mustache', 'color', -1)" class="arrow-btn !bg-pink-100 !text-pink-600">◀</button>
                            <button onclick="changeLayer('mustache', 'color', 1)" class="arrow-btn !bg-pink-100 !text-pink-600">▶</button>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <h2 class="text-2xl font-bold text-yellow-400 mb-4 text-center font-caveat border-b border-yellow-400/30 pb-2">Tenues</h2>
                    
                    <div class="flex items-center justify-between bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm">
                        <span class="text-xs font-black text-indigo-900 uppercase w-20">T-Shirt</span>
                        <div class="flex gap-1 items-center">
                            <button onclick="changeLayer('top', 'type', -1)" class="arrow-btn">◀</button>
                            <span id="lbl-top-type" class="text-sm font-bold w-4 text-center text-indigo-600">1</span>
                            <button onclick="changeLayer('top', 'type', 1)" class="arrow-btn">▶</button>
                        </div>
                        <div class="flex gap-1 items-center border-l-2 border-indigo-100 pl-1">
                            <button onclick="changeLayer('top', 'color', -1)" class="arrow-btn !bg-pink-100 !text-pink-600">◀</button>
                            <button onclick="changeLayer('top', 'color', 1)" class="arrow-btn !bg-pink-100 !text-pink-600">▶</button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm">
                        <span class="text-xs font-black text-indigo-900 uppercase w-20">Veste</span>
                        <div class="flex gap-1 items-center">
                            <button onclick="changeLayer('jacket', 'type', -1)" class="arrow-btn">◀</button>
                            <span id="lbl-jacket-type" class="text-sm font-bold w-4 text-center text-indigo-600">Ø</span>
                            <button onclick="changeLayer('jacket', 'type', 1)" class="arrow-btn">▶</button>
                        </div>
                        <div class="flex gap-1 items-center border-l-2 border-indigo-100 pl-1">
                            <button onclick="changeLayer('jacket', 'color', -1)" class="arrow-btn !bg-pink-100 !text-pink-600">◀</button>
                            <button onclick="changeLayer('jacket', 'color', 1)" class="arrow-btn !bg-pink-100 !text-pink-600">▶</button>
                        </div>
                    </div>

                    <div class="bg-indigo-100/90 p-2 rounded-xl border border-indigo-200 shadow-sm space-y-2 mt-2">
                        <span class="text-xs font-black text-indigo-900 uppercase block text-center border-b border-indigo-200 pb-1">Costume Spécial</span>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-gray-600 uppercase font-bold">Thème</span>
                            <div class="flex gap-1 items-center">
                                <button onclick="changeLayer('special', 'theme', -1)" class="arrow-btn !py-1 !px-2">◀</button>
                                <span id="lbl-special-theme" class="text-[10px] font-black w-16 text-center text-indigo-700 uppercase">AUCUN</span>
                                <button onclick="changeLayer('special', 'theme', 1)" class="arrow-btn !py-1 !px-2">▶</button>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-gray-600 uppercase font-bold">Variante</span>
                            <div class="flex gap-1 items-center">
                                <button onclick="changeLayer('special', 'type', -1)" class="arrow-btn !py-1 !px-2">◀</button>
                                <span id="lbl-special-type" class="text-xs font-black w-16 text-center text-indigo-700">Ø</span>
                                <button onclick="changeLayer('special', 'type', 1)" class="arrow-btn !py-1 !px-2">▶</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-span-1 md:col-span-3 mt-2 border-t border-white/20 pt-4">
                    <h2 class="text-2xl font-bold text-yellow-400 mb-4 text-center font-caveat">Auras & Effets</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl mx-auto">
                        
                        <div class="flex items-center justify-between bg-yellow-100 p-3 rounded-xl border border-yellow-300 shadow-sm h-[68px]">
                            <span class="text-sm font-black text-yellow-800 uppercase w-20">Aura</span>
                            <div class="flex gap-2 items-center">
                                <button onclick="changeLayer('aura', 'type', -1)" class="arrow-btn !bg-yellow-200 hover:!bg-yellow-300">◀</button>
                                <span id="lbl-aura-type" class="text-sm font-bold w-6 text-center text-yellow-800">Ø</span>
                                <button onclick="changeLayer('aura', 'type', 1)" class="arrow-btn !bg-yellow-200 hover:!bg-yellow-300">▶</button>
                            </div>
                        </div>

                        <div class="flex flex-col bg-purple-100 p-2 rounded-xl border border-purple-300 shadow-sm justify-center h-[68px]">
                            <div class="flex items-center justify-between w-full mb-1">
                                <span class="text-sm font-black text-purple-800 uppercase w-16">Effet</span>
                                <span id="lbl-effect-name" class="text-[11px] font-black text-purple-600 uppercase truncate px-2">AUCUN</span>
                                <div class="flex gap-1 items-center">
                                    <button onclick="changeLayer('effect', 'type', -1)" class="arrow-btn !bg-purple-200 hover:!bg-purple-300 !py-1 !px-2">◀</button>
                                    <span id="lbl-effect-type" class="text-xs font-bold w-4 text-center text-purple-800">Ø</span>
                                    <button onclick="changeLayer('effect', 'type', 1)" class="arrow-btn !bg-purple-200 hover:!bg-purple-300 !py-1 !px-2">▶</button>
                                </div>
                            </div>
                            <span id="lbl-effect-desc" class="text-[10px] text-center text-purple-500 font-semibold italic truncate w-full"></span>
                        </div>

                    </div>
                </div>

            </div>

            <button onclick="join()" class="w-full mt-8 bg-yellow-400 hover:bg-yellow-300 text-indigo-900 py-4 rounded-2xl font-black text-lg shadow-[0_4px_0_0_#ca8a04] active:shadow-none active:translate-y-1 transition-all uppercase tracking-widest">
                Rejoindre la salle !
            </button>
        </div>
    </div>

    <script>
        const basePath = "personnage/images/sections/";
        
        const skinColors = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];
        const commonColors = [1, 8, 11, 15];
        const clothesColors = [1, 19, 31, 40];
        
        const hairStyles = ['very_short', 'short', 'medium', 'long', 'shaved'];
        const maxHairByType = { 'very_short': 15, 'short': 17, 'medium': 18, 'long': 21, 'shaved': 6 };

        // Configuration de la nouvelle catégorie "Spécial"
        const specialThemes = [
            { key: "none", name: "Aucun", path: "", max: 0 },
            { key: "neutral", name: "Neutre", path: "Jacket/Men/Neutral/Men", max: 4 },
            { key: "job", name: "Métier", path: "Jacket/Men/Job/Men", max: 17 },
            { key: "antiquity", name: "Antiquité", path: "Jacket/Men/Antiquity/Men", max: 9 },
            { key: "medieval", name: "Médiéval", path: "Jacket/Men/Medieval/Men", max: 23 },
            { key: "pirate", name: "Pirate", path: "Jacket/Men/Pirate/Men", max: 6 },
            { key: "halloween", name: "Halloween", path: "Jacket/Men/Halloween/Men", max: 7 },
            { key: "christmas", name: "Noël", path: "Jacket/Men/Christmas/Men", max: 12 }
        ];

        // Configuration des textes pour la catégorie "Effet"
        const effectDetails = [
            { name: "Aucun", desc: "" },
            { name: "Arc-en-Ciel", desc: "Participer à 10 parties. (1/10)" },
            { name: "Lévitation", desc: "Remporter 3 médailles d'or 🥇. (0/3)" },
            { name: "Effet 3", desc: "À débloquer..." },
            { name: "Effet 4", desc: "À débloquer..." },
            { name: "Effet 5", desc: "À débloquer..." }
        ];

        let state = {
            skin: { type: 1, colorIdx: 0, maxType: 1, hasColor: true, colors: skinColors, path: "Skin/1" },
            hair: { type: 1, colorIdx: 0, maxType: 15, hasColor: true, colors: commonColors, styleIdx: 0 }, 
            beard: { type: 0, colorIdx: 0, maxType: 11, hasColor: true, colors: commonColors, path: "Beards" },
            mustache: { type: 0, colorIdx: 0, maxType: 11, hasColor: true, colors: commonColors, path: "Mustaches" },
            top: { type: 1, colorIdx: 0, maxType: 20, hasColor: true, colors: clothesColors, path: "Top/Men" },
            jacket: { type: 0, colorIdx: 0, maxType: 19, hasColor: true, colors: clothesColors, path: "Jacket/Men" }, 
            special: { themeIdx: 0, type: 0 }, 
            aura: { type: 0, colorIdx: 0, maxType: 5, hasColor: false },
            effect: { type: 0, colorIdx: 0, maxType: 5, hasColor: false }
        };

        function updateVisual(category) {
            const data = state[category];
            const lblType = document.getElementById(`lbl-${category}-type`);
            const lblColor = document.getElementById(`lbl-${category}-color`);
            const lblStyle = document.getElementById(`lbl-${category}-style`);
            
            // Gestion de l'affichage de la catégorie unique Spécial
            if (category === 'special') {
                let theme = specialThemes[data.themeIdx];
                document.getElementById('lbl-special-theme').innerText = theme.name;
                
                if (data.themeIdx === 0 || data.type === 0) {
                    document.getElementById('layer-special').src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=";
                    document.getElementById('lbl-special-type').innerText = "Ø";
                } else {
                    document.getElementById('lbl-special-type').innerText = data.type;
                    let elSpecial = document.getElementById('layer-special');
                    elSpecial.src = `${basePath}${theme.path}/${data.type}.png`;
                    elSpecial.onerror = function() { this.src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="; };
                }
                return;
            }

            // Gestion de l'affichage des textes pour la catégorie Effet
            if (category === 'effect') {
                let effectInfo = effectDetails[data.type] || effectDetails[0];
                document.getElementById('lbl-effect-type').innerText = data.type === 0 ? "Ø" : data.type;
                document.getElementById('lbl-effect-name').innerText = effectInfo.name;
                document.getElementById('lbl-effect-desc').innerText = effectInfo.desc;
                
                let elEffect = document.getElementById(`layer-effect`);
                if (data.type === 0) {
                    elEffect.src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=";
                } else {
                    elEffect.src = `personnage/effets/effet${data.type}.png`;
                    elEffect.onerror = function() { this.src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="; };
                }
                return;
            }

            if (data.type === 0) {
                if (category === 'hair') {
                    document.getElementById('layer-hair-front').src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=";
                    document.getElementById('layer-hair-back').src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=";
                } else {
                    document.getElementById(`layer-${category}`).src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=";
                }
                if(lblType) lblType.innerText = "Ø";
                return;
            }

            if(lblType) lblType.innerText = data.type;
            if(lblColor) lblColor.innerText = data.colorIdx + 1;
            if(lblStyle) lblStyle.innerText = hairStyles[data.styleIdx].replace('_', ' ');

            if (category === 'hair') {
                let colorCode = data.colors[data.colorIdx];
                let style = hairStyles[data.styleIdx];
                
                let elFront = document.getElementById('layer-hair-front');
                let elBack = document.getElementById('layer-hair-back');
                
                elFront.src = `${basePath}Hair/Front/${style}/${data.type}/${colorCode}.png`;
                elBack.src = `${basePath}Hair/Back/${style}/${data.type}/${colorCode}.png`;
                
                elFront.onerror = function() { this.src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="; };
                elBack.onerror = function() { this.src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="; };
                return;
            }
            if (category === 'aura') {
                let elAura = document.getElementById(`layer-aura`);
                elAura.src = `personnage/aura/aura${data.type}.png`;
                elAura.onerror = function() { this.src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="; };
                return;
            }

            const el = document.getElementById(`layer-${category}`);
            let finalUrl = `${basePath}${data.path}`;
            if (data.hasColor) {
                let colorCode = data.colors[data.colorIdx];
                if (category === 'skin') {
                    finalUrl = `${basePath}Skin/1/${colorCode}.png`; 
                } else {
                    finalUrl = `${finalUrl}/${data.type}/${colorCode}.png`;
                }
            } else {
                finalUrl = `${finalUrl}/${data.type}.png`;
            }

            el.src = finalUrl;
            el.onerror = function() { this.src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="; };
        }

        function changeLayer(category, prop, direction) {
            let data = state[category];
            
            if (category === 'special') {
                if (prop === 'theme') {
                    data.themeIdx += direction;
                    if (data.themeIdx >= specialThemes.length) data.themeIdx = 0;
                    if (data.themeIdx < 0) data.themeIdx = specialThemes.length - 1;
                    data.type = (data.themeIdx === 0) ? 0 : 1; 
                } else if (prop === 'type' && data.themeIdx > 0) {
                    data.type += direction;
                    let max = specialThemes[data.themeIdx].max;
                    if (data.type > max) data.type = 1;
                    if (data.type < 1) data.type = max;
                }
                updateVisual(category);
                return;
            }

            if (prop === 'type') {
                data.type += direction;
                let min = ['skin', 'top'].includes(category) ? 1 : 0;
                if (data.type > data.maxType) data.type = min;
                if (data.type < min) data.type = data.maxType;
            } 
            else if (prop === 'color' && data.hasColor) {
                data.colorIdx += direction;
                if (data.colorIdx >= data.colors.length) data.colorIdx = 0;
                if (data.colorIdx < 0) data.colorIdx = data.colors.length - 1;
            }
            else if (prop === 'style' && category === 'hair') {
                data.styleIdx += direction;
                if (data.styleIdx >= hairStyles.length) data.styleIdx = 0;
                if (data.styleIdx < 0) data.styleIdx = hairStyles.length - 1;
                data.maxType = maxHairByType[hairStyles[data.styleIdx]] || 15;
                if (data.type > data.maxType) data.type = data.maxType;
            }
            
            updateVisual(category);
        }

        window.onload = () => {
            // Rendu strict des éléments du visage basiques (non modifiables dans l'UI)
            document.getElementById('layer-eyes').src = `${basePath}Eyes/1.png`;
            document.getElementById('layer-mouth').src = `${basePath}Mouth/1.png`;
            document.getElementById('layer-nose').src = `${basePath}Nose/1.png`;
            document.getElementById('layer-eyebrow').src = `${basePath}Eyebrow/1/1.png`;

            // Initialisation des autres éléments
            Object.keys(state).forEach(cat => updateVisual(cat));
        };

        function join() {
            const nick = document.getElementById('nick').value.trim();
            if(!nick) return alert("Il nous faut ton pseudo !");

            const payload = {
                nickname: nick,
                skin: state.skin.type, skinColor: state.skin.colorIdx,
                // On fixe automatiquement les paramètres du visage pour le backend
                eyes: 1, mouth: 1, nose: 1, eyebrow: 1, eyebrowColor: 0, spot: 0,
                
                hair: state.hair.type, hairColor: state.hair.colorIdx, hairStyle: state.hair.styleIdx,
                beard: state.beard.type, beardColor: state.beard.colorIdx,
                mustache: state.mustache.type, mustacheColor: state.mustache.colorIdx,
                top: state.top.type, topColor: state.top.colorIdx,
                jacket: state.jacket.type, jacketColor: state.jacket.colorIdx,
                
                // On remet tous les spéciaux à 0 par défaut
                antiquity: 0, christmas: 0, halloween: 0, job: 0, medieval: 0, neutral: 0, pirate: 0,
                aura: state.aura.type, effect: state.effect.type
            };

            // On écrase la valeur uniquement pour le costume spécial sélectionné
            if (state.special.themeIdx > 0 && state.special.type > 0) {
                let themeKey = specialThemes[state.special.themeIdx].key;
                payload[themeKey] = state.special.type;
            }

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