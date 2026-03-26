<?php
// Credenciales admin
$host = "10.10.2.254";
$db   = "pharmasphere_db";
$user = "admin_pharma";
$pass = "Admin_IAW_pharma";
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("Error de conexión BD (admin): " . $e->getMessage());
}
?>
