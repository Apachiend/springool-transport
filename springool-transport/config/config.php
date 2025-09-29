<?php
// config/config.php
require_once __DIR__ . '/../vendor/autoload.php'; // если будешь использовать dotenv

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'springool_db');
define('DB_USER', $_ENV['DB_USER'] ?? 'db_user');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS
    );
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Database connection error: ' . $e->getMessage());
    die('Ошибка подключения к базе данных');
}
?>