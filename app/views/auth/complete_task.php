<?php
session_start();

// Giriş kontrolü
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /Task_Manager_PRO/public/index.php');
    exit;
}

// Veritabanı bağlantısı
require 'C:\xampp\htdocs\Task_Manager_PRO\config\connection.php';

// Parametreleri al
$task_id = $_GET['id'];
$user_id = $_SESSION['user_id'];
$filter = $_GET['filter'] ?? 'all'; // Mevcut filtreyi koru

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    
    // Sadece kullanıcının kendi görevini ve incomplete olanı güncelle
    $stmt = $pdo->prepare("
        UPDATE tasks 
        SET status = 'completed', completed_at = NOW() 
        WHERE id = :task_id 
        AND user_id = :user_id 
        AND status = 'incomplete'
    ");
    $stmt->execute([
        ':task_id' => $task_id,
        ':user_id' => $user_id
    ]);

} catch (PDOException $e) {
    die("Hata: " . $e->getMessage());
}

// Aynı filtreyle geri dön
header("Location: /Task_Manager_PRO/app/views/auth/taskmanager.php?filter=$filter");
exit;
?>