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
    <title>Avatar - Bernard Quizz</title>
    <style>
        .preview-container { 
            width: 120px; height: 120px; position: relative; 
            margin: 0 auto 20px; background: #f3f4f6; border-radius: 20px; overflow: visible;
        }
    </style>
</head>
<body class="bg-indigo-600 min-h-screen text-white flex flex-col items-center p-4 font-sans">

    <h1 class="text-2xl font-black mb-4 uppercase tracking-widest text-center">Crée ton Bernard</h1>

    <div class="bg-white text-gray-800 p-6 rounded-3xl shadow-2xl w-full max-w-md">
        
        <div class="preview-container shadow-inner border-4 border-indigo-100 flex items-end justify-center">
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
                    <?php for($i=1; $i<=10; $i++): 
                        $locked = ($is_member === 'false' && $i > 5);
                    ?>
                        <div onclick="setHair(<?= $i ?>)" class="relative w-12 h-12 md:w-14 md:h-14 bg-gray-50 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-indigo-400 transition-all p-1 <?= $locked ? 'opacity-60 grayscale' : '' ?>">
                            <img src="personnage/cheveux/cheveux<?= $i ?>.png" class="w-full h-full object-contain" onerror="this.parentElement.style.display='none'">
                            <?php if($locked): ?>
                                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-10 rounded-xl"><span class="text-xl drop-shadow-md">🔒</span></div>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div>
                <p class="text-[10px] font-black text-gray-400 mb-2 uppercase tracking-widest text-center">Style vestimentaire</p>
                <div class="flex flex-wrap justify-center gap-2 py-1">
                    <?php for($i=1; $i<=10; $i++): 
                        $locked = ($is_member === 'false' && $i > 5);
                    ?>
                        <div onclick="setOutfit(<?= $i ?>)" class="relative w-12 h-12 md:w-14 md:h-14 bg-gray-50 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-indigo-400 transition-all p-1 <?= $locked ? 'opacity-60 grayscale' : '' ?>">
                            <img src="personnage/tenue/tenue<?= $i ?>.png" class="w-full h-full object-contain" onerror="this.parentElement.style.display='none'">
                            <?php if($locked): ?>
                                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-10 rounded-xl"><span class="text-xl drop-shadow-md">🔒</span></div>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div>
                <div class="flex justify-center items-center mb-2 gap-2">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Aura magique</p>
                    <?php if($is_member === 'false'): ?>
                        <span class="text-[10px] font-black text-yellow-500 uppercase bg-yellow-50 px-2 py-0.5 rounded">🔒 VIP UNIQUEMENT</span>
                    <?php endif; ?>
                </div>
                <div class="flex flex-wrap justify-center gap-2 py-1">
                    <div onclick="setAura(0)" class="w-12 h-12 md:w-14 md:h-14 bg-gray-100 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center cursor-pointer font-bold text-gray-400">Ø</div>
                    <?php for($i=1; $i<=5; $i++): 
                        $locked = ($is_member === 'false');
                    ?>
                        <div onclick="setAura(<?= $i ?>)" class="relative w-12 h-12 md:w-14 md:h-14 bg-gray-50 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-indigo-400 transition-all p-1 <?= $locked ? 'opacity-60 grayscale' : '' ?>">
                            <img src="personnage/aura/aura<?= $i ?>.png" class="w-full h-full object-contain" onerror="this.parentElement.style.display='none'">
                            <?php if($locked): ?>
                                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-10 rounded-xl"><span class="text-xl drop-shadow-md">🔒</span></div>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
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
            if (!isMember && id > 5) return alert("Cette coiffure est réservée aux membres inscrits !");
            hair = id; 
            document.getElementById('prev-hair').src = `personnage/cheveux/cheveux${id}.png`; 
        }
        function setOutfit(id) { 
            if (!isMember && id > 5) return alert("Cette tenue est réservée aux membres inscrits !");
            outfit = id; 
            document.getElementById('prev-outfit').src = `personnage/tenue/tenue${id}.png`; 
        }
        function setAura(id) { 
            if (!isMember && id > 0) return alert("Les auras sont réservées aux membres inscrits !");
            aura = id; 
            const cont = document.getElementById('prev-aura-container');
            if(id === 0) {
                cont.innerHTML = '';
            } else {
                let zIndex = (id == 1 || id == 5) ? 30 : 5;
                cont.innerHTML = `<img src="personnage/aura/aura${id}.png" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] object-contain animate-pulse" style="z-index: ${zIndex};">`;
            }
        }

        function join() {
            const nick = document.getElementById('nick').value.trim();
            if(!nick) return alert("Hé ! Il nous faut ton pseudo !");

            fetch(`api_live.php?action=join&pin=<?= htmlspecialchars($pin) ?>`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    nickname: nick, 
                    hair: hair, 
                    outfit: outfit, 
                    aura: aura,
                    is_member: isMember
                })
            })
            .then(r => r.json())
            .then(data => {
                if(data.status === 'success') {
                    localStorage.setItem('quiz_nickname', nick);
                    window.location.href = `game_screen.php?pin=<?= htmlspecialchars($pin) ?>&nick=${encodeURIComponent(nick)}`;
                }
            });
        }
    </script>
</body>
</html>