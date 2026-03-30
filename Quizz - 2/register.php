<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->fetch()) {
        $error = "Ce pseudo est déjà pris.";
    } elseif (strlen($username) < 3 || strlen($username) > 12) {
        $error = "Le pseudo doit faire entre 3 et 12 caractères.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'joueur')");
        if ($stmt->execute([$username, $hashed])) {
            // Lien propre sans .php
            header("Location: login");
            exit;
        } else {
            $error = "Erreur lors de l'inscription.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Inscription - Bernard Quizz</title>
</head>
<body class="bg-indigo-900 flex items-center justify-center min-h-screen text-gray-800 font-sans">
    <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md">
        <h2 class="text-3xl font-black text-center text-indigo-900 mb-6 uppercase tracking-widest">Créer un compte</h2>
        
        <?php if (isset($error)): ?>
            <p class="text-red-500 text-center font-bold mb-4 bg-red-50 p-2 rounded-lg"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-600 uppercase">Choisis un Pseudo</label>
                <input type="text" name="username" required maxlength="12" class="w-full p-4 mt-1 bg-gray-100 border-none rounded-2xl focus:ring-4 focus:ring-indigo-200 outline-none font-bold">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-600 uppercase">Mot de passe</label>
                <input type="password" name="password" required minlength="4" class="w-full p-4 mt-1 bg-gray-100 border-none rounded-2xl focus:ring-4 focus:ring-indigo-200 outline-none font-bold">
            </div>
            <button type="submit" class="w-full bg-green-500 text-white p-4 rounded-2xl font-black text-lg hover:bg-green-600 transition shadow-lg mt-4">
                S'INSCRIRE
            </button>
        </form>

        <p class="mt-6 text-center text-sm font-bold text-gray-500">
            Déjà inscrit ? <a href="login" class="text-indigo-600 hover:underline">Connecte-toi</a>
        </p>
        <p class="mt-2 text-center text-sm font-bold">
            <a href="index" class="text-gray-400 hover:text-gray-600">← Retour à l'accueil</a>
        </p>
    </div>
</body>
</html>