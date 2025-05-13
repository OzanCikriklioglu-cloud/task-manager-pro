<?php
// MySQL server infos
$host     = "x"; // your host address
$dbname   = 'x';  // your database name
$charset  = 'utf8mb4';

$dsn      = "mysql:host={$host};dbname={$dbname};charset={$charset}";

$dbUser   = 'root';     // Your xamp username its root at default
$dbPass   = 'x';  //  your xampp password

$options  = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
