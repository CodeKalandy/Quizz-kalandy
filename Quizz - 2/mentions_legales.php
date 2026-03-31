<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/logo.png">
    <title>Mentions Légales – Bernard Quizz</title>
    <style>
        body { background-color:#0f172a; background-image:radial-gradient(at 0% 0%,#1e1b4b 0px,transparent 50%),radial-gradient(at 100% 100%,#312e81 0px,transparent 50%); background-attachment:fixed; color:white; font-family:sans-serif; }
        .title-text { font-family:'Caveat',cursive; text-shadow:3px 3px 0px #312e81; letter-spacing:2px; }
        .game-card  { background-color:#1e1b4b; border:4px solid #312e81; border-radius:1.5rem; box-shadow:0 8px 0 0 #0b0f19; }
        .particle   { position:fixed; background:rgba(255,255,255,0.02); border-radius:50%; animation:drift infinite linear; pointer-events:none; z-index:0; }
        @keyframes drift { from{transform:translateY(100vh) rotate(0deg)} to{transform:translateY(-100vh) rotate(360deg)} }
        .ml-title { font-family:'Caveat',cursive; font-size:1.4rem; color:#a5b4fc; margin-top:2rem; margin-bottom:.5rem; border-bottom:2px solid #312e81; padding-bottom:.4rem; }
        .ml-body  { color:#c7d2fe; font-size:.95rem; line-height:1.7; }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8 relative">
<div class="particle" style="width:150px;height:150px;left:5%;animation-duration:25s;"></div>
<div class="particle" style="width:220px;height:220px;left:80%;animation-duration:35s;"></div>
<div class="relative z-10 max-w-3xl mx-auto">

    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center gap-4">
            <img src="images/logo.png" alt="Logo" class="h-12 drop-shadow-md" onerror="this.style.display='none'">
            <h1 class="title-text text-3xl text-yellow-400">Mentions Légales</h1>
        </div>
        <a href="index" class="bg-indigo-600 text-white px-5 py-2 rounded-xl font-black uppercase tracking-wider shadow-[0_4px_0_0_#1e1b4b] hover:bg-indigo-500 transition-all active:translate-y-1 text-sm border-2 border-indigo-400">← Accueil</a>
    </div>

    <div class="game-card p-8">
        <h2 class="title-text text-4xl text-white mb-6">⚖️ Mentions Légales & CGU</h2>

        <p class="ml-title">1. Éditeur du site</p>
        <p class="ml-body">Bernard Quizz est un projet de jeu interactif à but pédagogique et ludique, développé dans le cadre d'un projet scolaire.</p>

        <p class="ml-title">2. Hébergement</p>
        <p class="ml-body">Ce site est hébergé par <strong class="text-white">Alwaysdata</strong>, 91 Rue du Faubourg Saint-Honoré, 75008 Paris, France.</p>

        <p class="ml-title">3. Données Personnelles</p>
        <p class="ml-body">Les mots de passe sont chiffrés (bcrypt). Aucune donnée personnelle n'est revendue à des tiers. Les sessions de jeu sont stockées temporairement sur le serveur et effacées après chaque partie. Seuls le pseudo et les statistiques de jeu sont conservés en base de données.</p>

        <p class="ml-title">4. Cookies & Sessions</p>
        <p class="ml-body">Ce site utilise des cookies de session PHP pour maintenir votre connexion. Aucun cookie de tracking ou publicitaire n'est utilisé.</p>

        <p class="ml-title">5. Propriété Intellectuelle</p>
        <p class="ml-body">Les assets de personnages sont basés sur le projet open-source <a href="https://pinknose.me" target="_blank" class="text-indigo-400 hover:text-white underline">pinknose.me</a>. Les autres contenus visuels et le code source sont la propriété de leurs auteurs respectifs.</p>

        <p class="ml-title">6. Conditions d'Utilisation</p>
        <p class="ml-body">En utilisant ce site, vous acceptez de ne pas utiliser de pseudonymes offensants ou trompeurs, de ne pas tenter de compromettre la sécurité du site, et de respecter les autres joueurs au sein du tchat.</p>
    </div>

    <p class="text-center text-xs text-indigo-400/50 font-bold mt-8 pb-4">© 2026 Bernard Quizz. Tous droits réservés.</p>
</div>
</body>
</html>