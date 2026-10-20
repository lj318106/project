<?php
$host = 'localhost';
$dbname = 'company_management project';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'
        ]
    );

    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new RuntimeException('MySQLi connection failed: ' . $conn->connect_error);
    }
    $conn->set_charset('utf8mb4');
} catch (Throwable $e) {
    die('Database Connection Failed: ' . $e->getMessage());
}
?>