<?php
session_start();

// Chargement du fichier .env pour la sécurité des accès
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }
}

// Les valeurs de secours sont des valeurs "bidon" de développement local.
$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? 'nom_de_la_base'; 
$user = $_ENV['DB_USER'] ?? 'root'; 
$pass = $_ENV['DB_PASS'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données."); // On évite d'afficher l'erreur exacte aux utilisateurs
}

function hasRole($roleNeeded) {
    $roles = ['utilisateur' => 1, 'createur' => 2, 'admin' => 3, 'fondateur' => 4];
    if (!isset($_SESSION['role'])) return false;
    return $roles[$_SESSION['role']] >= $roles[$roleNeeded];
}