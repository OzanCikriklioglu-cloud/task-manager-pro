<?php
// C:\xampp\htdocs\Task_Manager_PRO\app\views\auth\taskmanager.php
session_start();

// Sayfa önbelleklemesini tamamen kapat
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

// Giriş kontrolü: loggedin değilse index.php'ye yönlendir
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /Task_Manager_PRO/public/index.php');
    exit;
}

// Kullanıcı ID'sini al
$user_id = $_SESSION['user_id'];

// Filtre parametresini al (all, completed, incomplete)
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Veritabanı bağlantısı
require 'C:\xampp\htdocs\Task_Manager_PRO\config\connection.php';

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// SQL sorgusunu oluştur
$sql = "
    SELECT t.id, t.title, t.description, t.priority, t.due_date, t.status, t.created_at, t.completed_at,
           c.name AS category_name
    FROM tasks t
    LEFT JOIN categories c ON t.category_id = c.id
    WHERE t.user_id = :user_id
";

// Filtre ekle
if ($filter === 'completed') {
    $sql .= " AND t.status = 'completed'";
} elseif ($filter === 'incomplete') {
    $sql .= " AND t.status = 'incomplete'";
}

$sql .= " ORDER BY t.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Task Manager</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 12px; border: 1px solid #ddd; }
        th { background-color: #f4f4f4; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-danger { 
            background-color: #dc3545; 
            color: white; 
            padding: 6px 12px; 
            text-decoration: none; 
            border-radius: 4px; 
        }
        .btn-danger:hover { opacity: 0.9; }
        .filters { margin-bottom: 20px; }
        .filter-btn {
            display: inline-block;
            padding: 8px 16px;
            margin-right: 10px;
            text-decoration: none;
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #333;
        }
        .filter-btn:hover {
            background-color: #e9e9e9;
        }
        .active-filter {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
    </style>
    <link rel="stylesheet" href="/Task_Manager_PRO/public/Appear/style.css">
</head>
<body>
    <div class="header">
        <h1>Task Manager</h1>
        <!-- Güvenli Logout -->
        <a href="/Task_Manager_PRO/public/logout.php" class="btn-danger">Çıkış Yap</a>
    </div>

    <!-- Filtre butonları -->
    <div class="filters">
        <a href="?filter=all" class="filter-btn <?= $filter === 'all' ? 'active-filter' : '' ?>">Hepsi</a>
        <a href="?filter=completed" class="filter-btn <?= $filter === 'completed' ? 'active-filter' : '' ?>">Bitirilmiş</a>
        <a href="?filter=incomplete" class="filter-btn <?= $filter === 'incomplete' ? 'active-filter' : '' ?>">Bitirilmemiş</a>
    </div>

    <p><a href="/Task_Manager_PRO/app/views/tasks/create.php">Yeni Görev Ekle</a></p>

    <?php if (count($tasks) === 0): ?>
        <p>Henüz hiçbir görev eklenmemiş.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Başlık</th>
                    <th>Açıklama</th>
                    <th>Kategori</th>
                    <th>Öncelik</th>
                    <th>Durum</th>
                    <th>Son Tarih</th>
                    <th>Oluşturulma</th>
                    <th>Tamamlanma</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?= $task['id'] ?></td>
                        <td><?= htmlspecialchars($task['title']) ?></td>
                        <td><?= nl2br(htmlspecialchars($task['description'])) ?></td>
                        <td><?= htmlspecialchars($task['category_name'] ?? 'Yok') ?></td>
                        <td style="color: <?= 
                            $task['priority'] === 'low' ? 'green' : (
                            $task['priority'] === 'medium' ? 'orange' : 'red') 
                        ?>;">
                            <?= ucfirst($task['priority']) ?>
                        </td>
                        <td><?= ucfirst(str_replace('_', ' ', $task['status'])) ?></td>
                        <td><?= $task['due_date'] ? date('Y-m-d H:i', strtotime($task['due_date'])) : '-' ?></td>
                        <td><?= date('Y-m-d H:i', strtotime($task['created_at'])) ?></td>
                        <td><?= $task['completed_at'] ? date('Y-m-d H:i', strtotime($task['completed_at'])) : '-' ?></td>
                        <td>
                            <a href="edit.php?id=<?= $task['id'] ?>">Düzenle</a> |
                            <a href="delete.php?id=<?= $task['id'] ?>" onclick="return confirm('Silmek istediğinize emin misiniz?');">Sil</a>
                            
                            <?php if($task['status'] === 'incomplete'): ?>
                                | <a href="complete_task.php?id=<?= $task['id'] ?>&filter=<?= $filter ?>" class="complete-link">Tamamla</a>
                            <?php endif; ?>
                        </td>
                      
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>