<?php
session_start();
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header('Location: /Task_Manager_PRO/public/index.php');
    exit;
}

require 'C:\xampp\htdocs\Task_Manager_PRO\config\connection.php';
$pdo = new PDO($dsn, $dbUser, $dbPass, $options);

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

// Görevi getir, kullanıcıya ait değilse yönlendir
$stmt = $pdo->prepare("
    SELECT t.*, c.name AS category_name
    FROM tasks t
    LEFT JOIN categories c ON t.category_id = c.id
    WHERE t.id = :id AND t.user_id = :user_id
");
$stmt->execute([':id'=>$task_id, ':user_id'=>$user_id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    echo "Bu görevi görüntüleme yetkiniz yok veya görev bulunamadı.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Görev Detayı</title>
    <link rel="stylesheet" href="/Task_Manager_PRO/public/Appear/style.css">
</head>
<body>
    <h1>Görev Detayı</h1>
    <p><strong>ID:</strong> <?= $task['id'] ?></p>
    <p><strong>Başlık:</strong> <?= htmlspecialchars($task['title']) ?></p>
    <p><strong>Açıklama:</strong><br><?= nl2br(htmlspecialchars($task['description'])) ?></p>
    <p><strong>Kategori:</strong> <?= htmlspecialchars($task['category_name'] ?? 'Yok') ?></p>
    <p><strong>Öncelik:</strong> <?= ucfirst($task['priority']) ?></p>
    <p><strong>Durum:</strong> <?= ucfirst(str_replace('_',' ',$task['status'])) ?></p>
    <p><strong>Son Tarih:</strong> <?= $task['due_date'] ? date('Y-m-d H:i', strtotime($task['due_date'])) : '-' ?></p>
    <p><strong>Oluşturulma:</strong> <?= date('Y-m-d H:i', strtotime($task['created_at'])) ?></p>
    <p><strong>Tamamlanma:</strong> <?= $task['completed_at'] ? date('Y-m-d H:i', strtotime($task['completed_at'])) : '-' ?></p>

    <p>
        <a href="../auth/taskmanager.php?filter=<?= htmlspecialchars($_GET['filter'] ?? 'all') ?>">
            Geri Dön
        </a>
    </p>
</body>
</html>
