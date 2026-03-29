<?php
require_once 'db.php';
$pin = $_GET['pin'] ?? '';
$default_nick = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$is_member = isset($_SESSION['user_id']) ? 'true' : 'false';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap" rel="stylesheet">
    <title>Avatar - Bernard Quizz</title>
    <style>
        .preview-container { width: 120px; height: 120px; position: relative; margin: 0 auto 20px; background: #f3f4f6; border-radius: 20px; overflow: visible; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        @keyframes rainbow { 100% { filter: hue-rotate(360deg); } }
        .aura-rainbow { position: absolute; top: -15%; left: -15%; width: 130%; height: 130%; border-radius: 50%; box-shadow: 0 0 20px 5px #f43f5e, inset 0 0 20px 5px #f43f5e; animation: rainbow 2.5s linear infinite; z-index: 5; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        .aura-float { animation: float 3s ease-in-out infinite; }
    </style>
</head>
<body class="bg-indigo-600 min-h-screen text-white flex flex-col items-center p-4 font-sans">

    <img src="images/logo.png" class="h-16 mb-2 object-contain drop-shadow-md">
    <h1 class="text-2xl font-black mb-4 uppercase tracking-widest text-center" style="font-family: 'Caveat', cursive; font-size: 2rem;">Crée ton personnage</h1>

    <div class="bg-white text-gray-800 p-6 rounded-3xl shadow-2xl w-full max-w-md">
        
        <div class="preview-container shadow-inner border-4 border-indigo-100 flex items-end justify-center" id="char-wrapper">
            <div id="prev-aura-container"></div>
            <div class="relative w-full h-full overflow-hidden rounded-[15px] flex items-end justify-center z-10">
                <img id="prev-outfit" src="personnage/tenue/tenue1.png" class="absolute w-[90%] h-[90%] object-contain bottom-0" style="z-index: 10;">
                <img id="prev-hair" src="personnage/cheveux/cheveux1.png" class="absolute w-[90%] h-[90%] object-contain bottom-0" style="z-index: 20;">
            </div>
            <?php if($is_member === 'true'): ?>
                <div class="absolute -bottom-3 -right-3 bg-yellow-400 text-black text-[14px] font-black w-8 h-8 flex items-center justify-center rounded-full border-2 border-white z-40 shadow-lg" title="Joueur VIP">★</div>
            <?php endif; ?>
        </div>

        <div class="space-y-6 mt-6">
            <input type="text" id="nick" maxlength="12" placeholder="TON PSEUDO" value="<?= htmlspecialchars($default_nick) ?>"
                   class="w-full p-4 bg-gray-100 border-none rounded-2xl font-black text-center text-indigo-600 focus:ring-4 focus:ring-indigo-200 outline-none transition-all">

            <div>
                <p class="text-[10px] font-black text-gray-400 mb-2 uppercase tracking-widest text-center">Coupe de cheveux</p>
                <div class="flex flex-wrap justify-center gap-2 py-1">
                    <?php for($i=1; $i<=10; $i++): $locked = ($is_member === 'false' && $i > 5); ?>
                        <div onclick="setHair(<?= $i ?>)" class="relative w-12 h-12 md:w-14 md:h-14 bg-gray-50 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-indigo-400 transition-all p-1 <?= $locked ? 'opacity-60 grayscale' : '' ?>">
                            <img src="personnage/cheveux/cheveux<?= $i ?>.png" class="w-full h-full object-contain">
                            <?php if($locked): ?><div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-10 rounded-xl"><span class="text-xl drop-shadow-md">🔒</span></div><?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div>
                <p class="text-[10px] font-black text-gray-400 mb-2 uppercase tracking-widest text-center">Style vestimentaire</p>
                <div class="flex flex-wrap justify-center gap-2 py-1">
                    <?php for($i=1; $i<=10; $i++): $locked = ($is_member === 'false' && $i > 5); ?>
                        <div onclick="setOutfit(<?= $i ?>)" class="relative w-12 h-12 md:w-14 md:h-14 bg-gray-50 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-indigo-400 transition-all p-1 <?= $locked ? 'opacity-60 grayscale' : '' ?>">
                            <img src="personnage/tenue/tenue<?= $i ?>.png" class="w-full h-full object-contain">
                            <?php if($locked): ?><div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-10 rounded-xl"><span class="text-xl drop-shadow-md">🔒</span></div><?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div>
                <div class="flex justify-center items-center mb-2 gap-2">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Aura magique</p>
                    <?php if($is_member === 'false'): ?><span class="text-[10px] font-black text-yellow-500 uppercase bg-yellow-50 px-2 py-0.5 rounded">🔒 VIP</span><?php endif; ?>
                </div>
                <div class="flex flex-wrap justify-center gap-2 py-1">
                    <div onclick="setAura(0)" class="w-12 h-12 md:w-14 md:h-14 bg-gray-100 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center cursor-pointer font-bold text-gray-400">Ø</div>
                    <?php for($i=1; $i<=5; $i++): $locked = ($is_member === 'false'); ?>
                        <div onclick="setAura(<?= $i ?>)" class="relative w-12 h-12 md:w-14 md:h-14 bg-gray-50 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-indigo-400 transition-all p-1 <?= $locked ? 'opacity-60 grayscale' : '' ?>">
                            <img src="personnage/aura/aura<?= $i ?>.png" class="w-full h-full object-contain">
                            <?php if($locked): ?><div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-10 rounded-xl"><span class="text-xl drop-shadow-md">🔒</span></div><?php endif; ?>
                        </div>
                    <?php endfor; ?>
                    <div onclick="setAura(6)" class="relative w-12 h-12 md:w-14 md:h-14 bg-gray-50 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-indigo-400 transition-all flex items-center justify-center text-2xl <?= ($is_member === 'false') ? 'opacity-60 grayscale' : '' ?>">🌈<?php if($is_member === 'false'): ?><div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-10 rounded-xl"><span class="text-xl drop-shadow-md">🔒</span></div><?php endif; ?></div>
                    <div onclick="setAura(7)" class="relative w-12 h-12 md:w-14 md:h-14 bg-gray-50 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-indigo-400 transition-all flex items-center justify-center text-2xl <?= ($is_member === 'false') ? 'opacity-60 grayscale' : '' ?>">☁️<?php if($is_member === 'false'): ?><div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-10 rounded-xl"><span class="text-xl drop-shadow-md">🔒</span></div><?php endif; ?></div>
                </div>
            </div>

            <button onclick="join()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black text-lg shadow-lg transform active:scale-95 transition-all uppercase tracking-widest">
                C'est parti !
            </button>
        </div>
    </div>

    <script>
        const isMember = <?= $is_member ?>;
        let hair = 1, outfit = 1, aura = 0;

        function setHair(id) { 
            if (!isMember && id > 5) return alert("Cette coiffure est réservée aux VIP !");
            hair = id; document.getElementById('prev-hair').src = `personnage/cheveux/cheveux${id}.png`; 
        }
        function setOutfit(id) { 
            if (!isMember && id > 5) return alert("Cette tenue est réservée aux VIP !");
            outfit = id; document.getElementById('prev-outfit').src = `personnage/tenue/tenue${id}.png`; 
        }
        function setAura(id) { 
            if (!isMember && id > 0) return alert("Les auras sont réservées aux VIP !");
            aura = id; 
            const cont = document.getElementById('prev-aura-container');
            const wrap = document.getElementById('char-wrapper');
            wrap.classList.remove('aura-float');
            
            if(id === 0) { cont.innerHTML = ''; }
            else if(id <= 5) {
                let zIndex = (id == 1 || id == 5) ? 30 : 5;
                cont.innerHTML = `<img src="personnage/aura/aura${id}.png" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] object-contain animate-pulse" style="z-index: ${zIndex};">`;
            } else if (id === 6) {
                cont.innerHTML = `<div class="aura-rainbow"></div>`;
            } else if (id === 7) {
                cont.innerHTML = ''; wrap.classList.add('aura-float');
            }
        }

        function join() {
            const nick = document.getElementById('nick').value.trim();
            if(!nick) return alert("Hé ! Il nous faut ton pseudo !");
            event.target.innerText = "Connexion..."; event.target.disabled = true;

            fetch(`api_live.php?action=join&pin=<?= htmlspecialchars($pin) ?>`, {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nickname: nick, hair: hair, outfit: outfit, aura: aura, is_member: isMember })
            }).then(async r => {
                if(!r.ok) throw new Error("Erreur serveur");
                return JSON.parse(await r.text());
            }).then(data => {
                if(data.status === 'success') {
                    try { localStorage.setItem('quiz_nickname', nick); } catch(e) {}
                    window.location.href = `game_screen.php?pin=<?= htmlspecialchars($pin) ?>&nick=${encodeURIComponent(nick)}`;
                } else { throw new Error(); }
            }).catch(err => {
                alert("Impossible de rejoindre."); document.querySelector('button').disabled = false; document.querySelector('button').innerText = "C'est parti !";
            });
        }
    </script>
</body>
</html>