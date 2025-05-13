<?php
session_start();

// Kullanıcı giriş yapmamışsa yönlendir
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /Task_Manager_PRO/public/index.php');
    exit;
}

require 'C:\xampp\htdocs\Task_Manager_PRO\config\connection.php';

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// ID parametresi kontrolü
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Geçersiz görev ID');
}

$task_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Silme işlemi (sadece giriş yapan kullanıcıya ait görevler silinsin)
$sql = "DELETE FROM tasks WHERE id = :id AND user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id' => $task_id,
    ':user_id' => $user_id
]);

// Silme sonrası yönlendir
header('Location: /Task_Manager_PRO/app/views/auth/taskmanager.php');
exit;
