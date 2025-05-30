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
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Comic+Neue:wght@400;700&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Görev Detayı</title>
    <link rel="stylesheet" href="/Task_Manager_PRO/public/Appear/viewStyle.css">
</head>
<body>
    <div class="task-detail-container">
        <h1>Görev Detayları</h1>
        
        <div class="detail-item">
            <strong>📌 Başlık</strong>
            <p><?= htmlspecialchars($task['title']) ?></p>
        </div>
    

        <div class="detail-item">
            <strong>📝 Açıklama</strong>
            <p><?= nl2br(htmlspecialchars($task['description'])) ?></p>
        </div>

        <div class="detail-item">
            <strong>🏷 Kategori</strong>
            <p class="category-highlight">
                <?= htmlspecialchars($task['category_name'] ?? '📦 Kategorisiz') ?>
            </p>
        </div>
        <div class="detail-item">
            <strong>📅 Tarih</strong>
            <p style="color: <?= $task['status'] === 'completed' ? '#2ECC40' : '#FF4136' ?>;">
                <?php if($task['status'] === 'completed'): ?>
                    ✅ Tamamlandı: <?= date('Y-m-d H:i', strtotime($task['completed_at'])) ?>
                <?php else: ?>
                    ⏳ Son Tarih: <?= $task['due_date'] ? date('Y-m-d H:i', strtotime($task['due_date'])) : 'Belirtilmemiş' ?>
                <?php endif; ?>
            </p>
        </div>

        <!-- Diğer alanlar aynı .detail-item class'ı ile devam edecek -->
        
        <a href="../auth/taskmanager.php?filter=<?= htmlspecialchars($_GET['filter'] ?? 'all') ?>">
            ◀️ Geri Dön
        </a>
    </div>
</body>
</html>
