<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/logo.png">
    <title>Documentation – Bernard Quizz</title>
    <style>
        body { background-color:#0f172a; background-image:radial-gradient(at 0% 0%,#1e1b4b 0px,transparent 50%),radial-gradient(at 100% 100%,#312e81 0px,transparent 50%); background-attachment:fixed; color:white; font-family:sans-serif; }
        .title-text { font-family:'Caveat',cursive; text-shadow:3px 3px 0px #312e81; letter-spacing:2px; }
        .game-card  { background-color:#1e1b4b; border:4px solid #312e81; border-radius:1.5rem; box-shadow:0 8px 0 0 #0b0f19; }
        .particle   { position:fixed; background:rgba(255,255,255,0.02); border-radius:50%; animation:drift infinite linear; pointer-events:none; z-index:0; }
        @keyframes drift { from{transform:translateY(100vh) rotate(0deg)} to{transform:translateY(-100vh) rotate(360deg)} }
        .section-title { font-family:'Caveat',cursive; font-size:1.6rem; color:#a5b4fc; border-bottom:3px solid #312e81; padding-bottom:.5rem; margin-bottom:1rem; margin-top:2rem; }
        .doc-item { display:flex; gap:.75rem; align-items:flex-start; padding:.6rem 0; border-bottom:1px solid #1e2a5e; }
        .doc-item:last-child { border-bottom:none; }
        .doc-icon { font-size:1.4rem; flex-shrink:0; margin-top:.1rem; }
        .doc-item p { color:#c7d2fe; font-size:.95rem; line-height:1.6; }
        .doc-item strong { color:white; }
        .highlight { display:inline-block; padding:.15rem .5rem; border-radius:.4rem; font-weight:900; font-size:.8rem; }
        .h-yellow { background:#854d0e; color:#fde68a; }
        .h-red    { background:#7f1d1d; color:#fecaca; }
        .h-pink   { background:#831843; color:#fbcfe8; }
        .h-green  { background:#14532d; color:#bbf7d0; }
        .h-blue   { background:#1e3a8a; color:#bfdbfe; }
        .score-table { width:100%; border-collapse:collapse; font-size:.9rem; margin-bottom:1rem; }
        .score-table th { text-align:left; padding:.5rem .75rem; color:#a5b4fc; font-size:.7rem; text-transform:uppercase; letter-spacing:1px; border-bottom:2px solid #312e81; }
        .score-table td { padding:.5rem .75rem; color:#c7d2fe; border-bottom:1px solid #1e2a5e; }
        .score-table tr:last-child td { border-bottom:none; }
        .rank-row { display:flex; align-items:center; gap:1rem; padding:.6rem .8rem; border-radius:.75rem; margin-bottom:.4rem; background:#2e2a72; border:2px solid #3730a3; }
        .rank-row:last-child { margin-bottom:0; }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8 relative">
<div class="particle" style="width:150px;height:150px;left:5%;animation-duration:25s;"></div>
<div class="particle" style="width:220px;height:220px;left:80%;animation-duration:35s;"></div>
<div class="relative z-10 max-w-3xl mx-auto">

    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center gap-4">
            <img src="images/logo.png" alt="Logo" class="h-12 drop-shadow-md" onerror="this.style.display='none'">
            <h1 class="title-text text-3xl text-yellow-400">Documentation</h1>
        </div>
        <a href="index" class="bg-indigo-600 text-white px-5 py-2 rounded-xl font-black uppercase tracking-wider shadow-[0_4px_0_0_#1e1b4b] hover:bg-indigo-500 transition-all active:translate-y-1 text-sm border-2 border-indigo-400">← Accueil</a>
    </div>

    <div class="game-card p-8">
        <h2 class="title-text text-4xl text-white mb-2">📖 Comment jouer à Bernard Quizz ?</h2>
        <p class="text-indigo-300 text-sm">Tout ce qu'il faut savoir pour devenir un champion des quizz.</p>

        <!-- Modes -->
        <p class="section-title">🎮 Les Modes de Jeu</p>
        <div class="doc-item"><span class="doc-icon">🏆</span><p><strong>Classique :</strong> Gagnez des points en répondant vite et juste. Les séries de bonnes réponses déclenchent un <span class="highlight h-yellow">bonus streak 🔥</span>. Mode idéal pour débuter.</p></div>
        <div class="doc-item"><span class="doc-icon">⚔️</span><p><strong>Battle Royale :</strong> À chaque manche, <span class="highlight h-red">le dernier du classement est éliminé</span>. Seul le meilleur survit. Parfait pour les parties entre amis !</p></div>
        <div class="doc-item"><span class="doc-icon">❤️</span><p><strong>Survie :</strong> Vous avez <span class="highlight h-pink">3 cœurs ❤️❤️❤️</span>. Une mauvaise réponse = un cœur perdu. Zéro cœur = éliminé. Le mode le plus stressant !</p></div>

        <!-- Score -->
        <p class="section-title">🧮 Le Système de Score</p>
        <p class="text-indigo-300 text-sm mb-4">Chaque bonne réponse rapporte entre <strong class="text-white">500 et 1000 points</strong> selon votre vitesse.</p>
        <table class="score-table">
            <thead><tr><th>Situation</th><th>Points</th></tr></thead>
            <tbody>
                <tr><td>Bonne réponse très rapide</td><td><span class="highlight h-green">1000 pts</span></td></tr>
                <tr><td>Bonne réponse lente</td><td><span class="highlight h-blue">500 pts minimum</span></td></tr>
                <tr><td>Streak de 3 bonnes réponses ou + 🔥</td><td><span class="highlight h-yellow">+200 pts bonus</span></td></tr>
                <tr><td>Dernière question</td><td><span class="highlight h-red">× 2 (compte double !)</span></td></tr>
                <tr><td>Mauvaise réponse</td><td>0 pt — streak remis à zéro</td></tr>
            </tbody>
        </table>

        <!-- Joker -->
        <p class="section-title">🃏 Le Joker 50/50</p>
        <div class="doc-item"><span class="doc-icon">🃏</span><p>Disponible <strong>une seule fois par partie</strong> pour les joueurs inscrits. Il supprime automatiquement 2 des 3 mauvaises réponses. Utilisez-le sur la question la plus difficile pour maximiser vos chances !</p></div>

        <!-- Membres -->
        <p class="section-title">👑 Les Avantages Membres</p>
        <p class="text-indigo-300 text-sm mb-4">Créer un compte est <strong class="text-white">100% gratuit</strong>. Voici ce que vous débloquez :</p>
        <div class="doc-item"><span class="doc-icon">👗</span><p><strong>Personnalisation complète :</strong> Toutes les coiffures, couleurs de peau, barbes, moustaches et t-shirts. Les joueurs anonymes n'ont accès qu'à la moitié des options.</p></div>
        <div class="doc-item"><span class="doc-icon">✨</span><p><strong>Auras & Effets :</strong> Effets visuels animés autour de votre Bernard, débloqués via les quêtes.</p></div>
        <div class="doc-item"><span class="doc-icon">🃏</span><p><strong>Joker 50/50 :</strong> Une fois par partie, éliminez 2 mauvaises réponses.</p></div>
        <div class="doc-item"><span class="doc-icon">📊</span><p><strong>Statistiques & Rangs :</strong> Suivez vos performances et progressez vers le rang de Légende de Bernard.</p></div>
        <div class="doc-item"><span class="doc-icon">💾</span><p><strong>Bernard Favori :</strong> Sauvegardez votre apparence préférée sur votre profil.</p></div>

        <!-- Rangs -->
        <p class="section-title">🏅 Les Rangs</p>
        <p class="text-indigo-300 text-sm mb-4">Votre rang évolue selon le nombre de <strong class="text-white">bonnes réponses cumulées</strong> sur toutes vos parties.</p>
        <div class="rank-row"><span class="text-2xl">🥉</span><div class="flex-grow"><p class="font-black text-orange-400">Merguez de Bronze</p><p class="text-xs text-indigo-400">Rang de départ — 0 bonnes réponses</p></div></div>
        <div class="rank-row"><span class="text-2xl">🔵</span><div class="flex-grow"><p class="font-black text-blue-400">Apprenti Bernard</p><p class="text-xs text-indigo-400">50 bonnes réponses</p></div></div>
        <div class="rank-row"><span class="text-2xl">🟣</span><div class="flex-grow"><p class="font-black text-purple-400">Expert des Quiz</p><p class="text-xs text-indigo-400">150 bonnes réponses</p></div></div>
        <div class="rank-row"><span class="text-2xl">🔴</span><div class="flex-grow"><p class="font-black text-red-400">Maître des Questions</p><p class="text-xs text-indigo-400">350 bonnes réponses</p></div></div>
        <div class="rank-row" style="background:#2d1f4e;border-color:#7c3aed;"><span class="text-2xl">⭐</span><div class="flex-grow"><p class="font-black text-yellow-400">Légende de Bernard</p><p class="text-xs text-indigo-400">700 bonnes réponses — rang maximum</p></div></div>

        <!-- Quêtes -->
        <p class="section-title">🎯 Les Quêtes</p>
        <div class="doc-item"><span class="doc-icon">🌈</span><p><strong>Effet Arc-en-Ciel :</strong> Participez à <strong>10 parties</strong> pour débloquer la bordure néon multicolore animée.</p></div>
        <div class="doc-item"><span class="doc-icon">☁️</span><p><strong>Effet Lévitation :</strong> Remportez <strong>3 médailles d'or 🥇</strong> pour faire léviter votre Bernard.</p></div>
        <div class="doc-item"><span class="doc-icon">🔒</span><p><strong>Effets Mystère :</strong> D'autres effets sont cachés et seront débloqués dans de futures mises à jour.</p></div>

        <!-- Créer un quiz -->
        <p class="section-title">✏️ Créer un Quiz</p>
        <p class="text-indigo-300 text-sm mb-4">La création est réservée aux utilisateurs ayant le rôle <span class="highlight h-blue">Créateur</span> ou supérieur. Contactez un administrateur pour l'obtenir.</p>
        <div class="doc-item"><span class="doc-icon">1️⃣</span><p>Depuis votre tableau de bord, cliquez sur <strong>Créer un Quiz</strong> et donnez-lui un titre, une description et une image de couverture.</p></div>
        <div class="doc-item"><span class="doc-icon">2️⃣</span><p>Ajoutez vos questions : rédigez-les, renseignez les 4 options, indiquez la bonne réponse et choisissez le temps alloué (10, 20 ou 30 secondes).</p></div>
        <div class="doc-item"><span class="doc-icon">3️⃣</span><p>Choisissez si votre quiz est <strong>Public</strong> (visible par tous) ou <strong>Privé</strong> (visible uniquement par vous). Modifiable à tout moment.</p></div>
        <div class="doc-item"><span class="doc-icon">4️⃣</span><p>Cliquez sur <strong>Lancer</strong>, choisissez le mode de jeu et partagez le <strong>code PIN</strong> affiché à l'écran avec vos joueurs.</p></div>

        <!-- Awards -->
        <p class="section-title">🎖️ Les Awards de Fin de Partie</p>
        <p class="text-indigo-300 text-sm mb-4">En plus du podium, 3 prix spéciaux sont décernés à chaque fin de partie :</p>
        <div class="doc-item"><span class="doc-icon">⚡</span><p><strong>L'Éclair :</strong> Meilleur temps de réponse moyen sur les bonnes réponses.</p></div>
        <div class="doc-item"><span class="doc-icon">🐌</span><p><strong>La Tortue :</strong> Joueur ayant pris le plus de temps pour répondre en moyenne.</p></div>
        <div class="doc-item"><span class="doc-icon">🍀</span><p><strong>Le Miraculé :</strong> Joueur encore en jeu ayant fait le plus d'erreurs — chanceux jusqu'au bout !</p></div>
    </div>

    <p class="text-center text-xs text-indigo-400/50 font-bold mt-8 pb-4">
        Avatars basés sur les assets open-source de <a href="https://pinknose.me" target="_blank" class="hover:text-indigo-300">pinknose.me</a>
    </p>
</div>
</body>
</html>