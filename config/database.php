<?php
declare(strict_types=1);

$host = 'localhost';
$db   = 'laboratory_booking_system';
$user = 'root';
$pass = ''; // Default XAMPP MySQL password. Change if yours is different.
$port = getenv('DB_PORT') ?: '3307';
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database connection failed. Check config/database.php and import database/laboratory_booking_system.sql.');
}
