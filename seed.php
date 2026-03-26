<?php
require_once __DIR__ . '/vendor/autoload.php';

$env = parse_ini_file(__DIR__ . '/.env');
define('DB_HOST', $env['DB_HOST']);
define('DB_NAME', $env['DB_NAME']);
define('DB_USER', $env['DB_USER']);
define('DB_PASS', $env['DB_PASS']);

$pdo = new PDO(
    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
    DB_USER,
    DB_PASS
);

$hash = password_hash('motdepasse123', PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO Utilisateur (Nom_Utilisateur, Prenom, Email, Mot_de_Passe, Genre, Telephone, Date_de_Naissance, Id_Role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['Schnell', 'Nicolas', 'nicolas@test.fr', $hash, 'Homme', '0612345678', '2000-01-01', 2]);

echo "Utilisateur créé !";