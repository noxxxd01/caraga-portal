<?php
/**
 * Database connection.
 * Include this from any file (index.php, api/*.php, config/install.php)
 * that needs the shared $db PDO instance.
 */

$db_host = 'localhost';
$db_user = 'root';
$db_pass = ''; // Default XAMPP password is empty
$db_name = 'dict_caraga_db';

try {
    // Connect to MySQL server without database first to ensure database existence
    $pdo_init = new PDO("mysql:host=$db_host", $db_user, $db_pass);
    $pdo_init->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Auto-create database if not exists
    $pdo_init->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    // Connect to the specific database
    $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage() . "<br>Please ensure the XAMPP Control Panel is running, and Apache and MySQL services are started.");
}
