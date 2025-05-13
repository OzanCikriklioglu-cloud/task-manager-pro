<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /Task_Manager_PRO/public/index.php');
    exit;
}

require 'C:\xampp\htdocs\Task_Manager_PRO\config\connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];
    $due_date = $_POST['due_date'];

    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, $options);

        $stmt = $pdo->prepare("UPDATE tasks SET title = :title, description = :description, priority = :priority, status = :status, due_date = :due_date WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
            'status' => $status,
            'due_date' => $due_date,
            'id' => $id,
            'user_id' => $_SESSION['user_id']
        ]);

        header('Location: taskmanager.php');
        exit;
    } catch (PDOException $e) {
        die("Güncelleme hatası: " . $e->getMessage());
    }
}
?>

