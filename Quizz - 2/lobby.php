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

    <div class="relative z-10 w-full max-w-6xl">
        <h1 class="text-3xl font-black mb-6 uppercase tracking-widest text-center text-yellow-400 mt-4 drop-shadow-lg" style="font-family: 'Caveat', cursive; font-size: 2.5rem;">Crée ton Bernard</h1>

        <div class="bg-white/10 backdrop-blur-md p-6 rounded-3xl shadow-2xl w-full border border-white/20">
            
            <div class="preview-container shadow-2xl" id="char-wrapper">
                <img id="layer-aura" src="" class="layer" style="z-index: 0;">
                <img id="layer-hair-back" src="" class="layer" style="z-index: 5;">
                <img id="layer-skin" src="" class="layer" style="z-index: 10;">
                <img id="layer-spot" src="" class="layer" style="z-index: 15;">
                <img id="layer-mouth" src="" class="layer" style="z-index: 20;">
                <img id="layer-eyes" src="" class="layer" style="z-index: 21;">
                <img id="layer-eyebrow" src="" class="layer" style="z-index: 22;">
                <img id="layer-nose" src="" class="layer" style="z-index: 23;">
                <img id="layer-beard" src="" class="layer" style="z-index: 25;">
                <img id="layer-mustache" src="" class="layer" style="z-index: 26;">
                <img id="layer-top" src="" class="layer" style="z-index: 30;">
                <img id="layer-jacket" src="" class="layer" style="z-index: 31;">
                
                <img id="layer-antiquity" src="" class="layer" style="z-index: 32;">
                <img id="layer-medieval" src="" class="layer" style="z-index: 33;">
                <img id="layer-neutral" src="" class="layer" style="z-index: 34;">
                <img id="layer-job" src="" class="layer" style="z-index: 35;">
                <img id="layer-pirate" src="" class="layer" style="z-index: 36;">
                <img id="layer-halloween" src="" class="layer" style="z-index: 37;">
                <img id="layer-christmas" src="" class="layer" style="z-index: 38;">
                
                <img id="layer-hair-front" src="" class="layer" style="z-index: 40;">
                <img id="layer-effect" src="" class="layer" style="z-index: 50;">
            </div>

            <input type="text" id="nick" maxlength="12" placeholder="TON PSEUDO" value="<?= htmlspecialchars($default_nick) ?>"
                   class="max-w-md mx-auto block w-full p-4 bg-white/90 border-none rounded-2xl font-black text-center text-indigo-900 focus:ring-4 focus:ring-yellow-400 outline-none transition-all mb-6 shadow-inner">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <div class="space-y-3">
                    <h2 class="text-2xl font-bold text-yellow-400 mb-4 text-center font-caveat border-b border-yellow-400/30 pb-2">Visage & Corps</h2>
                    
                    <div class="flex items-center justify-between bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm">
                        <span class="text-xs font-black text-indigo-900 uppercase w-20">Peau</span>
                        <div class="flex gap-2 items-center">
                            <button onclick="changeLayer('skin', 'color', -1)" class="arrow-btn">◀</button>
                            <span id="lbl-skin-color" class="text-sm font-bold w-6 text-center text-indigo-600">1</span>
                            <button onclick="changeLayer('skin', 'color', 1)" class="arrow-btn">▶</button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm">
                        <span class="text-xs font-black text-indigo-900 uppercase w-20">Yeux</span>
                        <div class="flex gap-2 items-center">
                            <button onclick="changeLayer('eyes', 'type', -1)" class="arrow-btn">◀</button>
                            <span id="lbl-eyes-type" class="text-sm font-bold w-6 text-center text-indigo-600">1</span>
                            <button onclick="changeLayer('eyes', 'type', 1)" class="arrow-btn">▶</button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm">
                        <span class="text-xs font-black text-indigo-900 uppercase w-20">Bouche</span>
                        <div class="flex gap-2 items-center">
                            <button onclick="changeLayer('mouth', 'type', -1)" class="arrow-btn">◀</button>
                            <span id="lbl-mouth-type" class="text-sm font-bold w-6 text-center text-indigo-600">1</span>
                            <button onclick="changeLayer('mouth', 'type', 1)" class="arrow-btn">▶</button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm">
                        <span class="text-xs font-black text-indigo-900 uppercase w-20">Nez</span>
                        <div class="flex gap-2 items-center">
                            <button onclick="changeLayer('nose', 'type', -1)" class="arrow-btn">◀</button>
                            <span id="lbl-nose-type" class="text-sm font-bold w-6 text-center text-indigo-600">1</span>
                            <button onclick="changeLayer('nose', 'type', 1)" class="arrow-btn">▶</button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm">
                        <span class="text-xs font-black text-indigo-900 uppercase w-20">Sourcils</span>
                        <div class="flex gap-1 items-center">
                            <button onclick="changeLayer('eyebrow', 'type', -1)" class="arrow-btn">◀</button>
                            <span id="lbl-eyebrow-type" class="text-sm font-bold w-4 text-center text-indigo-600">Ø</span>
                            <button onclick="changeLayer('eyebrow', 'type', 1)" class="arrow-btn">▶</button>
                        </div>
                        <div class="flex gap-1 items-center border-l-2 border-indigo-100 pl-1">
                            <button onclick="changeLayer('eyebrow', 'color', -1)" class="arrow-btn !bg-pink-100 !text-pink-600">◀</button>
                            <button onclick="changeLayer('eyebrow', 'color', 1)" class="arrow-btn !bg-pink-100 !text-pink-600">▶</button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm">
                        <span class="text-xs font-black text-indigo-900 uppercase w-20">Taches</span>
                        <div class="flex gap-2 items-center">
                            <button onclick="changeLayer('spot', 'type', -1)" class="arrow-btn">◀</button>
                            <span id="lbl-spot-type" class="text-sm font-bold w-6 text-center text-indigo-600">Ø</span>
                            <button onclick="changeLayer('spot', 'type', 1)" class="arrow-btn">▶</button>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <h2 class="text-2xl font-bold text-yellow-400 mb-4 text-center font-caveat border-b border-yellow-400/30 pb-2">Pilosité</h2>
                    
                    <div class="bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm space-y-2">
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
                            <span class="text-[10px] text-gray-500 uppercase">Type & Coul.</span>
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

                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex items-center justify-between bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm">
                            <span class="text-[10px] font-black text-indigo-900 uppercase w-12">Noël</span>
                            <button onclick="changeLayer('christmas', 'type', -1)" class="arrow-btn !py-1 !px-2">◀</button>
                            <span id="lbl-christmas-type" class="text-xs font-bold w-4 text-center text-indigo-600">Ø</span>
                            <button onclick="changeLayer('christmas', 'type', 1)" class="arrow-btn !py-1 !px-2">▶</button>
                        </div>
                        <div class="flex items-center justify-between bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm">
                            <span class="text-[10px] font-black text-indigo-900 uppercase w-12">Hallow.</span>
                            <button onclick="changeLayer('halloween', 'type', -1)" class="arrow-btn !py-1 !px-2">◀</button>
                            <span id="lbl-halloween-type" class="text-xs font-bold w-4 text-center text-indigo-600">Ø</span>
                            <button onclick="changeLayer('halloween', 'type', 1)" class="arrow-btn !py-1 !px-2">▶</button>
                        </div>
                        <div class="flex items-center justify-between bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm">
                            <span class="text-[10px] font-black text-indigo-900 uppercase w-12">Pirate</span>
                            <button onclick="changeLayer('pirate', 'type', -1)" class="arrow-btn !py-1 !px-2">◀</button>
                            <span id="lbl-pirate-type" class="text-xs font-bold w-4 text-center text-indigo-600">Ø</span>
                            <button onclick="changeLayer('pirate', 'type', 1)" class="arrow-btn !py-1 !px-2">▶</button>
                        </div>
                        <div class="flex items-center justify-between bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm">
                            <span class="text-[10px] font-black text-indigo-900 uppercase w-12">Médiéval</span>
                            <button onclick="changeLayer('medieval', 'type', -1)" class="arrow-btn !py-1 !px-2">◀</button>
                            <span id="lbl-medieval-type" class="text-xs font-bold w-4 text-center text-indigo-600">Ø</span>
                            <button onclick="changeLayer('medieval', 'type', 1)" class="arrow-btn !py-1 !px-2">▶</button>
                        </div>
                        <div class="flex items-center justify-between bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm">
                            <span class="text-[10px] font-black text-indigo-900 uppercase w-12">Antiquité</span>
                            <button onclick="changeLayer('antiquity', 'type', -1)" class="arrow-btn !py-1 !px-2">◀</button>
                            <span id="lbl-antiquity-type" class="text-xs font-bold w-4 text-center text-indigo-600">Ø</span>
                            <button onclick="changeLayer('antiquity', 'type', 1)" class="arrow-btn !py-1 !px-2">▶</button>
                        </div>
                        <div class="flex items-center justify-between bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm">
                            <span class="text-[10px] font-black text-indigo-900 uppercase w-12">Métier</span>
                            <button onclick="changeLayer('job', 'type', -1)" class="arrow-btn !py-1 !px-2">◀</button>
                            <span id="lbl-job-type" class="text-xs font-bold w-4 text-center text-indigo-600">Ø</span>
                            <button onclick="changeLayer('job', 'type', 1)" class="arrow-btn !py-1 !px-2">▶</button>
                        </div>
                        <div class="flex items-center justify-between bg-white/90 p-2 rounded-xl border border-indigo-100 shadow-sm col-span-2">
                            <span class="text-[10px] font-black text-indigo-900 uppercase w-16">Neutre</span>
                            <div class="flex gap-2">
                                <button onclick="changeLayer('neutral', 'type', -1)" class="arrow-btn !py-1 !px-2">◀</button>
                                <span id="lbl-neutral-type" class="text-xs font-bold w-4 text-center text-indigo-600">Ø</span>
                                <button onclick="changeLayer('neutral', 'type', 1)" class="arrow-btn !py-1 !px-2">▶</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-span-1 md:col-span-2 lg:col-span-3 mt-4 border-t border-white/20 pt-4">
                    <h2 class="text-2xl font-bold text-yellow-400 mb-4 text-center font-caveat">Auras & Effets</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl mx-auto">
                        <div class="flex items-center justify-between bg-yellow-100 p-3 rounded-xl border border-yellow-300 shadow-sm">
                            <span class="text-sm font-black text-yellow-800 uppercase w-20">Aura</span>
                            <div class="flex gap-2 items-center">
                                <button onclick="changeLayer('aura', 'type', -1)" class="arrow-btn !bg-yellow-200 hover:!bg-yellow-300">◀</button>
                                <span id="lbl-aura-type" class="text-sm font-bold w-6 text-center text-yellow-800">Ø</span>
                                <button onclick="changeLayer('aura', 'type', 1)" class="arrow-btn !bg-yellow-200 hover:!bg-yellow-300">▶</button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between bg-purple-100 p-3 rounded-xl border border-purple-300 shadow-sm">
                            <span class="text-sm font-black text-purple-800 uppercase w-20">Effet</span>
                            <div class="flex gap-2 items-center">
                                <button onclick="changeLayer('effect', 'type', -1)" class="arrow-btn !bg-purple-200 hover:!bg-purple-300">◀</button>
                                <span id="lbl-effect-type" class="text-sm font-bold w-6 text-center text-purple-800">Ø</span>
                                <button onclick="changeLayer('effect', 'type', 1)" class="arrow-btn !bg-purple-200 hover:!bg-purple-300">▶</button>
                            </div>
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

        let state = {
            skin: { type: 1, colorIdx: 0, maxType: 1, hasColor: true, colors: skinColors, path: "Skin/1" },
            eyes: { type: 1, colorIdx: 0, maxType: 27, hasColor: false, path: "Eyes" },
            mouth: { type: 1, colorIdx: 0, maxType: 21, hasColor: false, path: "Mouth" },
            nose: { type: 1, colorIdx: 0, maxType: 15, hasColor: false, path: "Nose" },
            eyebrow: { type: 0, colorIdx: 0, maxType: 17, hasColor: true, colors: commonColors, path: "Eyebrow" },
            spot: { type: 0, colorIdx: 0, maxType: 13, hasColor: false, path: "Spot" },
            
            hair: { type: 1, colorIdx: 0, maxType: 15, hasColor: true, colors: commonColors, styleIdx: 0 }, 
            beard: { type: 0, colorIdx: 0, maxType: 11, hasColor: true, colors: commonColors, path: "Beards" },
            mustache: { type: 0, colorIdx: 0, maxType: 11, hasColor: true, colors: commonColors, path: "Mustaches" },
            
            top: { type: 1, colorIdx: 0, maxType: 20, hasColor: true, colors: clothesColors, path: "Top/Men" },
            jacket: { type: 0, colorIdx: 0, maxType: 19, hasColor: true, colors: clothesColors, path: "Jacket/Men" }, 
            
            antiquity: { type: 0, colorIdx: 0, maxType: 9, hasColor: false, path: "Antiquity/Men" },
            christmas: { type: 0, colorIdx: 0, maxType: 12, hasColor: false, path: "Christmas/Men" },
            halloween: { type: 0, colorIdx: 0, maxType: 7, hasColor: false, path: "Halloween/Men" },
            job: { type: 0, colorIdx: 0, maxType: 17, hasColor: false, path: "Job/Men" },
            medieval: { type: 0, colorIdx: 0, maxType: 23, hasColor: false, path: "Medieval/Men" },
            neutral: { type: 0, colorIdx: 0, maxType: 4, hasColor: false, path: "Neutral/Men" },
            pirate: { type: 0, colorIdx: 0, maxType: 6, hasColor: false, path: "Pirate/Men" },

            aura: { type: 0, colorIdx: 0, maxType: 5, hasColor: false },
            effect: { type: 0, colorIdx: 0, maxType: 5, hasColor: false }
        };

        function updateVisual(category) {
            const data = state[category];
            const lblType = document.getElementById(`lbl-${category}-type`);
            const lblColor = document.getElementById(`lbl-${category}-color`);
            const lblStyle = document.getElementById(`lbl-${category}-style`);
            
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
                
                // Fallback de sécurité si un numéro est sauté dans les fichiers
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
            if (category === 'effect') {
                let elEffect = document.getElementById(`layer-effect`);
                elEffect.src = `personnage/effets/effet${data.type}.png`;
                elEffect.onerror = function() { this.src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="; };
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
            
            // Fallback de sécurité (très utile pour Antiquité par exemple)
            el.onerror = function() {
                this.src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="; 
            };
        }

        function changeLayer(category, prop, direction) {
            let data = state[category];
            
            if (prop === 'type') {
                data.type += direction;
                let min = ['skin', 'eyes', 'mouth', 'nose', 'top'].includes(category) ? 1 : 0;
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
            Object.keys(state).forEach(cat => updateVisual(cat));
        };

        function join() {
            const nick = document.getElementById('nick').value.trim();
            if(!nick) return alert("Il nous faut ton pseudo !");

            const payload = {
                nickname: nick,
                skin: state.skin.type, skinColor: state.skin.colorIdx,
                eyes: state.eyes.type, mouth: state.mouth.type, nose: state.nose.type,
                eyebrow: state.eyebrow.type, eyebrowColor: state.eyebrow.colorIdx, spot: state.spot.type,
                hair: state.hair.type, hairColor: state.hair.colorIdx, hairStyle: state.hair.styleIdx,
                beard: state.beard.type, beardColor: state.beard.colorIdx,
                mustache: state.mustache.type, mustacheColor: state.mustache.colorIdx,
                top: state.top.type, topColor: state.top.colorIdx,
                jacket: state.jacket.type, jacketColor: state.jacket.colorIdx,
                antiquity: state.antiquity.type, christmas: state.christmas.type, halloween: state.halloween.type,
                job: state.job.type, medieval: state.medieval.type, neutral: state.neutral.type, pirate: state.pirate.type,
                aura: state.aura.type, effect: state.effect.type
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