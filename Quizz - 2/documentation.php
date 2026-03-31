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
        .doc-item { display:flex; gap:.75rem; align-items:flex-start; padding:.6rem 0; border-bottom:1px solid #312e81; }
        .doc-item:last-child { border-bottom:none; }
        .doc-icon { font-size:1.4rem; flex-shrink:0; margin-top:.1rem; }
        .doc-item p { color:#c7d2fe; font-size:.95rem; line-height:1.6; }
        .doc-item strong { color:white; }
        .highlight { display:inline-block; padding:.15rem .5rem; border-radius:.4rem; font-weight:900; font-size:.8rem; }
        .h-yellow { background:#854d0e; color:#fde68a; }
        .h-red    { background:#7f1d1d; color:#fecaca; }
        .h-pink   { background:#831843; color:#fbcfe8; }
        .h-green  { background:#14532d; color:#bbf7d0; }
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

        <!-- Modes de jeu -->
        <p class="section-title">🎮 Les Modes de Jeu</p>
        <div class="doc-item">
            <span class="doc-icon">🏆</span>
            <p><strong>Classique :</strong> Gagnez des points en répondant vite et juste. Plus vous êtes rapides, plus vous marquez. Les séries de bonnes réponses déclenchent un <span class="highlight h-yellow">bonus streak 🔥</span>.</p>
        </div>
        <div class="doc-item">
            <span class="doc-icon">⚔️</span>
            <p><strong>Battle Royale :</strong> À chaque manche, <span class="highlight h-red">le dernier du classement est éliminé</span>. Le dernier survivant remporte la partie !</p>
        </div>
        <div class="doc-item">
            <span class="doc-icon">❤️</span>
            <p><strong>Survie :</strong> Vous avez <span class="highlight h-pink">3 cœurs ❤️❤️❤️</span>. Une mauvaise réponse = un cœur perdu. Arrivez à zéro et vous êtes éliminé.</p>
        </div>

        <!-- Statut inscrit -->
        <p class="section-title">👑 Le Statut Membre (Inscrit)</p>
        <p class="text-indigo-300 text-sm mb-4">Créer un compte est gratuit et débloque des avantages exclusifs :</p>
        <div class="doc-item">
            <span class="doc-icon">👗</span>
            <p><strong>Tenues complètes :</strong> Accès à toutes les coiffures, couleurs de peau, barbes, moustaches et t-shirts. Les joueurs anonymes n'ont accès qu'à la moitié.</p>
        </div>
        <div class="doc-item">
            <span class="doc-icon">✨</span>
            <p><strong>Auras Magiques :</strong> Débloquez des effets visuels autour de votre Bernard — arc-en-ciel, lévitation, et d'autres surprises à découvrir via les quêtes.</p>
        </div>
        <div class="doc-item">
            <span class="doc-icon">🃏</span>
            <p><strong>Joker 50/50 :</strong> Une fois par partie, supprimez 2 mauvaises réponses pour doubler vos chances.</p>
        </div>
        <div class="doc-item">
            <span class="doc-icon">📊</span>
            <p><strong>Statistiques & Titres :</strong> Suivez vos victoires, bonnes réponses et progressez dans les rangs (Merguez de Bronze → Légende de Bernard).</p>
        </div>
        <div class="doc-item">
            <span class="doc-icon">💾</span>
            <p><strong>Bernard Favori :</strong> Sauvegardez votre apparence préférée sur votre profil pour la retrouver à chaque partie.</p>
        </div>

        <!-- Quêtes -->
        <p class="section-title">🎯 Les Quêtes</p>
        <div class="doc-item">
            <span class="doc-icon">🌈</span>
            <p><strong>Effet Arc-en-Ciel :</strong> Participez à <strong>10 parties</strong> pour débloquer cet effet de bordure néon multicolore.</p>
        </div>
        <div class="doc-item">
            <span class="doc-icon">☁️</span>
            <p><strong>Effet Lévitation :</strong> Remportez <strong>3 médailles d'or</strong> pour faire flotter votre Bernard en jeu.</p>
        </div>

        <!-- Dernière question -->
        <p class="section-title">🚨 La Dernière Question</p>
        <div class="doc-item">
            <span class="doc-icon">✖️2</span>
            <p>La dernière question de chaque partie <strong>compte double</strong>. Un retournement de situation est toujours possible jusqu'au bout !</p>
        </div>
    </div>

    <p class="text-center text-xs text-indigo-400/50 font-bold mt-8 pb-4">
        Avatars basés sur les assets open-source de <a href="https://pinknose.me" target="_blank" class="hover:text-indigo-300">pinknose.me</a>
    </p>
</div>
</body>
</html>