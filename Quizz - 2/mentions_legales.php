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
        .ml-body  { color:#c7d2fe; font-size:.95rem; line-height:1.7; margin-top:.5rem; }
        .ml-body a { color:#818cf8; text-decoration:underline; }
        .ml-body a:hover { color:white; }
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

        <p class="ml-title">1. Présentation du site</p>
        <p class="ml-body">
            <strong class="text-white">Bernard Quizz</strong> est une application web de quiz multijoueur en temps réel, développée dans le cadre d'un projet scolaire en administration IT (programme ADMIN1).<br>
            Le site est accessible à l'adresse : <strong class="text-white">bthiery.alwaysdata.net</strong><br>
            Responsable du projet : <strong class="text-white">Kalandy (Bernard)</strong>
        </p>

        <p class="ml-title">2. Hébergement</p>
        <p class="ml-body">
            Ce site est hébergé par <strong class="text-white">Alwaysdata SAS</strong><br>
            91 Rue du Faubourg Saint-Honoré, 75008 Paris, France<br>
            Site web : <a href="https://www.alwaysdata.com" target="_blank">www.alwaysdata.com</a>
        </p>

        <p class="ml-title">3. Propriété Intellectuelle</p>
        <p class="ml-body">
            Le code source de Bernard Quizz est la propriété de son auteur. Les assets de personnages (Bernard) sont basés sur le projet open-source <a href="https://pinknose.me" target="_blank">pinknose.me</a>, utilisés conformément à sa licence.<br>
            Toute reproduction, distribution ou modification du contenu sans autorisation préalable est interdite.
        </p>

        <p class="ml-title">4. Données Personnelles</p>
        <p class="ml-body">
            Les données collectées lors de l'inscription sont : le <strong class="text-white">pseudo</strong> et le <strong class="text-white">mot de passe</strong> (chiffré via bcrypt, non lisible même par les administrateurs).<br>
            Les statistiques de jeu (parties jouées, bonnes réponses, podiums) sont également conservées.<br>
            <strong class="text-white">Aucune donnée personnelle sensible</strong> (nom, prénom, adresse e-mail, numéro de téléphone) n'est collectée.<br>
            <strong class="text-white">Aucune donnée n'est revendue ou transmise à des tiers.</strong><br>
            Conformément au RGPD, vous pouvez demander la suppression de votre compte en contactant un administrateur directement sur la plateforme.<br>
            <strong class="text-white">MAIL EN COURS DE CREATION</strong>
        </p>

        <p class="ml-title">5. Durée de Conservation</p>
        <p class="ml-body">
            Les comptes et statistiques sont conservés tant que la plateforme est active.<br>
            Les sessions de jeu (fichiers JSON temporaires) sont stockées sur le serveur pendant la durée de la partie, puis peuvent être supprimées manuellement par les administrateurs.<br>
            Le projet étant scolaire, les données pourront être supprimées en intégralité à la fin du projet.
        </p>

        <p class="ml-title">6. Cookies & Sessions</p>
        <p class="ml-body">
            Bernard Quizz utilise uniquement des <strong class="text-white">cookies de session PHP</strong> nécessaires au fonctionnement de la connexion utilisateur.<br>
            <strong class="text-white">Aucun cookie publicitaire, analytique ou de tracking tiers</strong> n'est utilisé.<br>
            La fermeture du navigateur supprime automatiquement les cookies de session.
        </p>

        <p class="ml-title">7. Conditions d'Utilisation</p>
        <p class="ml-body">
            En utilisant Bernard Quizz, vous acceptez de :<br><br>
            — Ne pas utiliser de pseudo offensant, discriminatoire ou trompeur.<br>
            — Ne pas tenter de compromettre la sécurité, la stabilité ou le bon fonctionnement de la plateforme.<br>
            — Ne pas usurper l'identité d'un autre utilisateur.<br>
            — Respecter les autres joueurs dans le tchat en partie.<br>
            — Ne pas tenter de manipuler les scores ou de tricher lors des parties.<br><br>
            Tout manquement à ces règles peut entraîner la suppression du compte par un administrateur, sans préavis.
        </p>

        <p class="ml-title">8. Limitation de Responsabilité</p>
        <p class="ml-body">
            Bernard Quizz est un projet pédagogique et non commercial. L'auteur ne peut être tenu responsable des interruptions de service, pertes de données ou dysfonctionnements techniques liés à l'hébergement ou à une utilisation incorrecte de la plateforme.
        </p>

        <p class="ml-title">9. Droit Applicable</p>
        <p class="ml-body">
            Les présentes mentions légales sont soumises au droit français. En cas de litige, les tribunaux français seront seuls compétents.
        </p>
    </div>

    <p class="text-center text-xs text-indigo-400/50 font-bold mt-8 pb-4">© 2026 Bernard Quizz — Projet scolaire ADMIN1. Tous droits réservés.</p>
</div>
</body>
</html>