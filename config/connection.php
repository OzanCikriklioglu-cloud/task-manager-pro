<?php
// MySQL server infos
$host     = "127.0.0.1:3307"; // your host address
$dbname   = 'task_manager_pro';  // your database name
$charset  = 'utf8mb4';

$dsn      = "mysql:host={$host};dbname={$dbname};charset={$charset}";

$dbUser   = 'root';     // Your xamp username its root at default
$dbPass   = 'Ozan123456?';  //  your xampp password

$options  = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
