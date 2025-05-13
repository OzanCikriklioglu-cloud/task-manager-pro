<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /Task_Manager_PRO/public/index.php');
    exit;
}

require 'C:\xampp\htdocs\Task_Manager_PRO\config\connection.php';

$task_id = $_GET['id'] ?? null;
if (!$task_id) {
    die("Geçersiz görev ID.");
}

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);

    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id AND user_id = :user_id");
    $stmt->execute(['id' => $task_id, 'user_id' => $_SESSION['user_id']]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        die("Görev bulunamadı.");
    }
} catch (PDOException $e) {
    die("Hata: " . $e->getMessage());
}
?>

<form action="update.php" method="POST">
    <input type="hidden" name="id" value="<?= $task['id'] ?>">
    <!-- Mevcut status değerini korumak için hidden field -->
    <input type="hidden" name="status" value="<?= htmlspecialchars($task['status']) ?>">

    <label>Başlık:</label><br>
    <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>"><br><br>

    <label>Açıklama:</label><br>
    <textarea name="description"><?= htmlspecialchars($task['description']) ?></textarea><br><br>

    <label>Öncelik:</label><br>
    <select name="priority">
        <option value="low" <?= $task['priority'] === 'low' ? 'selected' : '' ?>>Düşük</option>
        <option value="medium" <?= $task['priority'] === 'medium' ? 'selected' : '' ?>>Orta</option>
        <option value="high" <?= $task['priority'] === 'high' ? 'selected' : '' ?>>Yüksek</option>
    </select><br><br>

    <label>Son Tarih:</label><br>
    <input type="datetime-local" name="due_date"
           value="<?= $task['due_date'] ? date('Y-m-d\TH:i', strtotime($task['due_date'])) : '' ?>"><br><br>

    <button type="submit">Güncelle</button>
</form>
