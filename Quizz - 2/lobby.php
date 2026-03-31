<?php
require_once 'db.php';
$pin = $_GET['pin'] ?? '';
$default_nick = isset($_SESSION['username']) ? $_SESSION['username'] : '';
// Inscrit = accès complet. Anonyme = moitié des options, pas d'aura/effets.
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap" rel="stylesheet">
    <title>Ton Bernard - Bernard Quizz</title>
    <style>
        /* === THEME GLOBAL (GAME UI) === */
        html, body {
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background-color: #0f172a; 
            background-image: 
                radial-gradient(at 0% 0%, #1e1b4b 0px, transparent 50%),
                radial-gradient(at 100% 100%, #312e81 0px, transparent 50%);
            color: white;
        }
        
        .game-card {
            background-color: #1e1b4b; 
            border: 4px solid #312e81;
            border-radius: 1.5rem;
            box-shadow: 0 8px 0 0 #0b0f19;
        }

        .preview-container { 
            width: 170px; height: 170px; 
            position: relative; margin: 0 auto; 
            background-color: #312e81; 
            border-radius: 2rem; 
            border: 6px solid #facc15;
            box-shadow: 0 8px 0 0 #ca8a04, inset 0 8px 20px rgba(0,0,0,0.5);
            overflow: visible; 
            transition: all 0.3s ease;
        }
        .layer { position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: block; object-fit: contain; }
        
        .category-title {
            font-family: 'Caveat', cursive;
            text-shadow: 2px 2px 0px rgba(0,0,0,0.5);
            font-size: 2.2rem;
            text-align: center;
            margin-bottom: 1rem;
            letter-spacing: 2px;
        }

        .control-row {
            display: flex; align-items: center; justify-content: space-between;
            background-color: #2e2a72; padding: 0.6rem 0.8rem; border-radius: 1rem;
            border: 3px solid #3b3687; margin-bottom: 0.6rem;
        }

        .control-label { font-size: 0.75rem; font-weight: 900; color: #a5b4fc; text-transform: uppercase; }
        
        /* Les Boutons d'action type "Jeux Vidéo" */
        .arrow-btn { 
            display: flex; align-items: center; justify-content: center;
            background-color: #3b82f6; color: white; padding: 0.4rem 0.6rem; border-radius: 0.5rem; 
            font-weight: 900; transition: transform 0.1s, box-shadow 0.1s; 
            box-shadow: 0 4px 0 0 #1d4ed8; 
            cursor: pointer;
        }
        .arrow-btn:active { transform: translateY(4px); box-shadow: 0 0px 0 0 #1d4ed8; }
        
        .arrow-btn.pink { background-color: #ec4899; box-shadow: 0 4px 0 0 #be185d; }
        .arrow-btn.pink:active { box-shadow: 0 0px 0 0 #be185d; }

        .val-display { font-size: 0.9rem; font-weight: 900; width: 35px; text-align: center; color: white; }

        /* Bouton Valider Géant */
        .join-btn {
            background-color: #10b981; color: white; border: 4px solid #047857;
            box-shadow: 0 8px 0 0 #064e3b; border-radius: 2rem;
            font-weight: 900; font-size: 1.6rem; text-transform: uppercase;
            padding: 1.2rem 2rem; width: 100%; transition: all 0.1s; letter-spacing: 2px;
            text-shadow: 2px 2px 0px #065f46;
        }
        .join-btn:hover { background-color: #34d399; }
        .join-btn:active { transform: translateY(8px); box-shadow: 0 0px 0 0 #064e3b; }

        /* === ANIMATIONS & EFFETS === */
        
        /* Animation Fading pour l'Aura */
        .aura-fade { animation: pulse-aura 2.5s ease-in-out infinite alternate; }
        @keyframes pulse-aura {
            0% { opacity: 0.4; }
            100% { opacity: 1; }
        }

        /* Effet 2: Lévitation Magique */
        .effect-levitation { animation: levitate-strong 2s ease-in-out infinite !important; }
        @keyframes levitate-strong {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-25px) scale(1.05); }
        }

        /* Effet 1: Arc-en-ciel Néon */
        .effect-rainbow { animation: rainbow-glow 3s linear infinite !important; }
        @keyframes rainbow-glow {
            0% { border-color: #ef4444; box-shadow: 0 8px 0 0 #991b1b, 0 0 25px #ef4444; }
            20% { border-color: #f59e0b; box-shadow: 0 8px 0 0 #92400e, 0 0 25px #f59e0b; }
            40% { border-color: #eab308; box-shadow: 0 8px 0 0 #854d0e, 0 0 25px #eab308; }
            60% { border-color: #10b981; box-shadow: 0 8px 0 0 #065f46, 0 0 25px #10b981; }
            80% { border-color: #3b82f6; box-shadow: 0 8px 0 0 #1e40af, 0 0 25px #3b82f6; }
            100% { border-color: #ef4444; box-shadow: 0 8px 0 0 #991b1b, 0 0 25px #ef4444; }
        }

        .particle {
            position: absolute; background: rgba(255,255,255,0.03); border-radius: 50%;
            animation: drift infinite linear; pointer-events: none; z-index: 0;
        }
        @keyframes drift { from { transform: translateY(100vh) rotate(0deg); } to { transform: translateY(-100vh) rotate(360deg); } }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center p-4 font-sans pb-16 relative overflow-x-hidden">

    <div class="particle" style="width: 120px; height: 120px; left: 10%; animation-duration: 25s;"></div>
    <div class="particle" style="width: 200px; height: 200px; left: 80%; animation-duration: 35s;"></div>
    <div class="particle" style="width: 80px; height: 80px; left: 40%; animation-duration: 20s;"></div>

    <div class="relative z-10 w-full max-w-5xl flex flex-col items-center mt-6">
        
        <div class="game-card p-8 w-full max-w-md flex flex-col items-center relative mb-8">
            <div class="absolute -top-5 bg-yellow-400 text-indigo-900 px-6 py-2 rounded-xl font-black text-3xl shadow-[0_4px_0_0_#ca8a04] transform -rotate-2 border-2 border-yellow-500" style="font-family: 'Caveat', cursive;">
                Crée ton Bernard
            </div>
            
            <div class="preview-container mb-6 mt-4" id="char-wrapper">
                
                <img id="layer-aura" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer aura-fade" style="z-index: 0;">

                <img id="layer-hair-back" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index: 5;">
                
                <img id="layer-skin" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index: 10;">
                
                <img id="layer-top" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index: 20;">
                
                <img id="layer-jacket" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index: 30;">
                <img id="layer-special" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index: 31;">
                
                <img id="layer-beard" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index: 40;">
                <img id="layer-mustache" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index: 41;">
                
                <img id="layer-mouth" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index: 50;">
                <img id="layer-eyes" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index: 51;">
                <img id="layer-eyebrow" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index: 52;">
                <img id="layer-nose" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index: 53;">
                <img id="layer-hair-front" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index: 54;">
                
                <img id="layer-effect" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="layer" style="z-index: 70;">
            </div>

            <input type="text" id="nick" maxlength="12" placeholder="TON PSEUDO" value="<?= htmlspecialchars($default_nick) ?>"
                   class="w-full p-4 bg-[#0f172a] border-4 border-[#312e81] rounded-2xl font-black text-center text-white text-xl focus:border-yellow-400 focus:ring-0 outline-none transition-all shadow-[inset_0_4px_10px_rgba(0,0,0,0.5)] placeholder-gray-500 uppercase tracking-wider">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full">
            
            <div class="game-card p-5">
                <h2 class="category-title text-pink-400">La Base</h2>
                
                <div class="control-row">
                    <span class="control-label">Peau</span>
                    <div class="flex gap-1 items-center">
                        <button onclick="changeLayer('skin', 'color', -1)" class="arrow-btn pink">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <span id="lbl-skin-color" class="val-display">1</span>
                        <button onclick="changeLayer('skin', 'color', 1)" class="arrow-btn pink">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="bg-[#2e2a72] p-3 rounded-2xl border-2 border-[#3b3687] mt-3">
                    <span class="control-label block mb-2 text-indigo-300">Cheveux</span>
                    
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] text-indigo-400 font-bold uppercase">Style</span>
                        <div class="flex gap-1 items-center">
                            <button onclick="changeLayer('hair', 'style', -1)" class="arrow-btn !py-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                            <span id="lbl-hair-style" class="val-display !w-20 text-[10px]">COURT</span>
                            <button onclick="changeLayer('hair', 'style', 1)" class="arrow-btn !py-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-indigo-400 font-bold uppercase">Coupe</span>
                        <div class="flex gap-1 items-center">
                            <button onclick="changeLayer('hair', 'type', -1)" class="arrow-btn !py-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                            <span id="lbl-hair-type" class="val-display text-xs !w-6">1</span>
                            <button onclick="changeLayer('hair', 'type', 1)" class="arrow-btn !py-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                        <div class="flex gap-1 items-center border-l-2 border-[#3b3687] pl-1">
                            <button onclick="changeLayer('hair', 'color', -1)" class="arrow-btn pink !py-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                            <button onclick="changeLayer('hair', 'color', 1)" class="arrow-btn pink !py-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="game-card p-5">
                <h2 class="category-title text-amber-400">Le Style</h2>
                
                <div class="control-row">
                    <span class="control-label text-[10px]">Barbe</span>
                    <div class="flex gap-1 items-center">
                        <button onclick="changeLayer('beard', 'type', -1)" class="arrow-btn !py-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <span id="lbl-beard-type" class="val-display !w-4 text-xs">Ø</span>
                        <button onclick="changeLayer('beard', 'type', 1)" class="arrow-btn !py-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                    <div class="flex gap-1 items-center border-l-2 border-[#3b3687] pl-1">
                        <button onclick="changeLayer('beard', 'color', -1)" class="arrow-btn pink !py-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button onclick="changeLayer('beard', 'color', 1)" class="arrow-btn pink !py-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="control-row">
                    <span class="control-label text-[10px]">Moust.</span>
                    <div class="flex gap-1 items-center">
                        <button onclick="changeLayer('mustache', 'type', -1)" class="arrow-btn !py-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <span id="lbl-mustache-type" class="val-display !w-4 text-xs">Ø</span>
                        <button onclick="changeLayer('mustache', 'type', 1)" class="arrow-btn !py-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                    <div class="flex gap-1 items-center border-l-2 border-[#3b3687] pl-1">
                        <button onclick="changeLayer('mustache', 'color', -1)" class="arrow-btn pink !py-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button onclick="changeLayer('mustache', 'color', 1)" class="arrow-btn pink !py-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="control-row mt-3 !bg-yellow-900/30 !border-yellow-700/50">
                    <span class="control-label text-yellow-500">Aura</span>
                    <div class="flex gap-1 items-center">
                        <button onclick="changeLayer('aura', 'type', -1)" class="arrow-btn !bg-yellow-600 !shadow-[0_4px_0_0_#a16207]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <span id="lbl-aura-type" class="val-display text-yellow-200">Ø</span>
                        <button onclick="changeLayer('aura', 'type', 1)" class="arrow-btn !bg-yellow-600 !shadow-[0_4px_0_0_#a16207]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="bg-purple-900/30 p-2 rounded-xl border-2 border-purple-700/50 mt-3">
                    <div class="flex items-center justify-between mb-1">
                        <span class="control-label text-purple-400">Effet</span>
                        <span id="lbl-effect-name" class="text-[10px] font-black text-purple-300 uppercase truncate px-1">AUCUN</span>
                        <div class="flex gap-1 items-center">
                            <button onclick="changeLayer('effect', 'type', -1)" class="arrow-btn !bg-purple-600 !shadow-[0_4px_0_0_#7e22ce] !py-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                            <span id="lbl-effect-type" class="val-display text-purple-100 !w-4 text-xs">Ø</span>
                            <button onclick="changeLayer('effect', 'type', 1)" class="arrow-btn !bg-purple-600 !shadow-[0_4px_0_0_#7e22ce] !py-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    </div>
                    <div id="lbl-effect-desc" class="text-[9px] text-center text-purple-400 font-semibold italic truncate w-full h-3"></div>
                </div>
            </div>

            <div class="game-card p-5">
                <h2 class="category-title text-teal-400">Tenues</h2>
                
                <div class="control-row">
                    <span class="control-label">T-Shirt</span>
                    <div class="flex gap-1 items-center">
                        <button onclick="changeLayer('top', 'type', -1)" class="arrow-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <span id="lbl-top-type" class="val-display">1</span>
                        <button onclick="changeLayer('top', 'type', 1)" class="arrow-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                    <div class="flex gap-1 items-center border-l-2 border-[#3b3687] pl-1">
                        <button onclick="changeLayer('top', 'color', -1)" class="arrow-btn pink">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button onclick="changeLayer('top', 'color', 1)" class="arrow-btn pink">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="control-row">
                    <span class="control-label">Veste</span>
                    <div class="flex gap-1 items-center">
                        <button onclick="changeLayer('jacket', 'type', -1)" class="arrow-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <span id="lbl-jacket-type" class="val-display">Ø</span>
                        <button onclick="changeLayer('jacket', 'type', 1)" class="arrow-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                    <div class="flex gap-1 items-center border-l-2 border-[#3b3687] pl-1">
                        <button onclick="changeLayer('jacket', 'color', -1)" class="arrow-btn pink">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button onclick="changeLayer('jacket', 'color', 1)" class="arrow-btn pink">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="bg-teal-900/30 p-3 rounded-2xl border-2 border-teal-700/50 mt-3">
                    <span class="control-label block mb-2 text-teal-400">Costume Spécial</span>
                    
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] text-teal-200 font-bold uppercase">Thème</span>
                        <div class="flex gap-1 items-center">
                            <button onclick="changeLayer('special', 'theme', -1)" class="arrow-btn !bg-teal-600 !shadow-[0_4px_0_0_#0f766e] !py-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                            <span id="lbl-special-theme" class="val-display !w-20 text-[10px] text-teal-100">AUCUN</span>
                            <button onclick="changeLayer('special', 'theme', 1)" class="arrow-btn !bg-teal-600 !shadow-[0_4px_0_0_#0f766e] !py-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-teal-200 font-bold uppercase">Variante</span>
                        <div class="flex gap-1 items-center">
                            <button onclick="changeLayer('special', 'type', -1)" class="arrow-btn !bg-teal-600 !shadow-[0_4px_0_0_#0f766e] !py-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                            <span id="lbl-special-type" class="val-display text-xs text-teal-100">Ø</span>
                            <button onclick="changeLayer('special', 'type', 1)" class="arrow-btn !bg-teal-600 !shadow-[0_4px_0_0_#0f766e] !py-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="w-full max-w-md mt-10">
            <button onclick="join()" class="join-btn">Rejoindre la partie !</button>
        </div>

    </div>

    <script>
        const basePath = "personnage/images/sections/";
        
        // Limites selon connexion : inscrit = tout, anonyme = moitié
        const IS_LOGGED_IN = <?= $is_logged_in ? 'true' : 'false' ?>;

        const skinColors    = IS_LOGGED_IN
            ? [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]
            : [1,2,3,4,5,6,7,8];
        const commonColors  = [1, 8, 11, 15];
        const clothesColors = [1, 19, 31, 40];
        
        const hairStyles    = ['very_short', 'short', 'medium', 'long', 'shaved'];
        const maxHairByType = IS_LOGGED_IN
            ? { 'very_short': 15, 'short': 17, 'medium': 18, 'long': 21, 'shaved': 6 }
            : { 'very_short': 7,  'short': 8,  'medium': 9,  'long': 10, 'shaved': 3 };

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

        const effectDetails = [
            { name: "Aucun", desc: "" },
            { name: "Arc-en-Ciel", desc: "Participer à 10 parties. (1/10)" },
            { name: "Lévitation", desc: "Remporter 3 médailles d'or 🥇. (0/3)" },
            { name: "Effet Mystère", desc: "À débloquer..." },
            { name: "Effet Mystère", desc: "À débloquer..." },
            { name: "Effet Mystère", desc: "À débloquer..." }
        ];

        let state = {
            skin: { type: 1, colorIdx: 0, maxType: 1, hasColor: true, colors: skinColors, path: "Skin/1" },
            hair: { type: 1, colorIdx: 0, maxType: 15, hasColor: true, colors: commonColors, styleIdx: 0 }, 
            beard:    { type: 0, colorIdx: 0, maxType: IS_LOGGED_IN ? 11 : 5,  hasColor: true, colors: commonColors,  path: "Beards" },
            mustache: { type: 0, colorIdx: 0, maxType: IS_LOGGED_IN ? 11 : 5,  hasColor: true, colors: commonColors,  path: "Mustaches" },
            top:      { type: 1, colorIdx: 0, maxType: IS_LOGGED_IN ? 20 : 10, hasColor: true, colors: clothesColors, path: "Top/Men" },
            jacket:   { type: 0, colorIdx: 0, maxType: IS_LOGGED_IN ? 19 : 0,  hasColor: true, colors: clothesColors, path: "Jacket/Men" }, 
            special: { themeIdx: 0, type: 0 }, 
            aura:   { type: 0, colorIdx: 0, maxType: IS_LOGGED_IN ? 5 : 0, hasColor: false },
            effect: { type: 0, colorIdx: 0, maxType: IS_LOGGED_IN ? 5 : 0, hasColor: false }
        };

        function updateVisual(category) {
            const data = state[category];
            const lblType = document.getElementById(`lbl-${category}-type`);
            const lblColor = document.getElementById(`lbl-${category}-color`);
            const lblStyle = document.getElementById(`lbl-${category}-style`);
            
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

            // GESTION DES EFFETS CSS MAGIC
            if (category === 'effect') {
                let effectInfo = effectDetails[data.type] || effectDetails[0];
                document.getElementById('lbl-effect-type').innerText = data.type === 0 ? "Ø" : data.type;
                document.getElementById('lbl-effect-name').innerText = effectInfo.name;
                document.getElementById('lbl-effect-desc').innerText = effectInfo.desc;
                
                let wrapper = document.getElementById('char-wrapper');
                wrapper.classList.remove('effect-rainbow', 'effect-levitation');

                if (data.type === 1) wrapper.classList.add('effect-rainbow');
                if (data.type === 2) wrapper.classList.add('effect-levitation');

                let elEffect = document.getElementById('layer-effect');
                if (data.type > 2) {
                    elEffect.src = `personnage/effets/effet${data.type}.png`;
                    elEffect.onerror = function() { this.src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="; };
                } else {
                    elEffect.src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=";
                }
                return;
            }

            // GESTION DE L'AURA AVEC Z-INDEX DYNAMIQUE
            if (category === 'aura') {
                let elAura = document.getElementById(`layer-aura`);
                
                if (data.type === 0) {
                    elAura.src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=";
                    if(lblType) lblType.innerText = "Ø";
                    return;
                }
                
                if(lblType) lblType.innerText = data.type;
                elAura.src = `personnage/aura/aura${data.type}.png`;
                
                // Si l'aura est 1 ou 5 -> Tout devant. Sinon (2, 3, 4) -> Tout derrière
                if (data.type === 1 || data.type === 5) {
                    elAura.style.zIndex = "60";
                } else {
                    elAura.style.zIndex = "0";
                }

                elAura.onerror = function() { this.src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="; };
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
            if(lblStyle) lblStyle.innerText = hairStyles[data.styleIdx].replace('_', ' ').toUpperCase();

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
                
                if (data.themeIdx > 0 && data.type > 0) {
                    state.jacket.type = 0;
                    updateVisual('jacket');
                }
                
                updateVisual(category);
                return;
            }

            if (prop === 'type') {
                data.type += direction;
                let min = ['skin', 'top'].includes(category) ? 1 : 0;
                if (data.type > data.maxType) data.type = min;
                if (data.type < min) data.type = data.maxType;

                if (category === 'jacket' && data.type > 0) {
                    state.special.themeIdx = 0;
                    state.special.type = 0;
                    updateVisual('special');
                }
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
            document.getElementById('layer-eyes').src = `${basePath}Eyes/1.png`;
            document.getElementById('layer-mouth').src = `${basePath}Mouth/1.png`;
            document.getElementById('layer-nose').src = `${basePath}Nose/1.png`;
            document.getElementById('layer-eyebrow').src = `${basePath}Eyebrow/1/1.png`;

            Object.keys(state).forEach(cat => updateVisual(cat));
        };

        function join() {
            const nick = document.getElementById('nick').value.trim();
            if(!nick) return alert("Il nous faut ton pseudo !");

            const payload = {
                nickname: nick,
                skin: state.skin.type, skinColor: state.skin.colorIdx,
                eyes: 1, mouth: 1, nose: 1, eyebrow: 1, eyebrowColor: 0, spot: 0,
                
                hair: state.hair.type, hairColor: state.hair.colorIdx, hairStyle: state.hair.styleIdx,
                beard: state.beard.type, beardColor: state.beard.colorIdx,
                mustache: state.mustache.type, mustacheColor: state.mustache.colorIdx,
                top: state.top.type, topColor: state.top.colorIdx,
                jacket: state.jacket.type, jacketColor: state.jacket.colorIdx,
                
                antiquity: 0, christmas: 0, halloween: 0, job: 0, medieval: 0, neutral: 0, pirate: 0,
                aura: state.aura.type, effect: state.effect.type
            };

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