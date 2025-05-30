<?php
// app/views/tasks/create.php

session_start();

// 🔒 Logout sonrası "geri" tuşuyla erişimi engellemek için önbelleği kapat:
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

// 🔐 Eğer giriş yapılmamışsa login sayfasına yönlendir:
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /Task_Manager_PRO/public/index.php');
    exit;
}

// Get user ID from session (fallback to 1 for demo purposes)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

// Include database connection
require 'C:\xampp\htdocs\Task_Manager_PRO\config\connection.php';

try {
    // Establish PDO connection
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    die("Database connection error: " . $e->getMessage());
}

$error = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form input
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $priority    = in_array($_POST['priority'], ['low','medium','high']) ? $_POST['priority'] : 'medium';
    $due_date    = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

    // Prepare INSERT statement
    $sql = "
        INSERT INTO tasks
            (user_id, category_id, title, description, priority, due_date)
        VALUES
            (:user_id, :category_id, :title, :description, :priority, :due_date)
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id',     $user_id,    PDO::PARAM_INT);
    $stmt->bindValue(':category_id', $category_id, $category_id !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
    $stmt->bindValue(':title',       $title,      PDO::PARAM_STR);
    $stmt->bindValue(':description', $description,PDO::PARAM_STR);
    $stmt->bindValue(':priority',    $priority,   PDO::PARAM_STR);
    $stmt->bindValue(':due_date',    $due_date,   $due_date ? PDO::PARAM_STR : PDO::PARAM_NULL);

    // Execute and redirect on success
    if ($stmt->execute()) {
        header('Location: /Task_Manager_PRO/app/views/auth/taskmanager.php');
        exit;
    } else {
        $error = 'An error occurred while adding the task.';
    }
}

// Fetch user’s categories for the dropdown
$catStmt = $pdo->prepare("SELECT id, name FROM categories WHERE user_id = :user_id");
$catStmt->execute([':user_id' => $user_id]);
$categories = $catStmt->fetchAll();

// Prepare current datetime for the <input> min attribute
$currentDatetime = date('Y-m-d\\TH:i');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Comic+Neue:wght@400;700&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Create Task</title>
    <link rel="stylesheet" href="/Task_Manager_PRO/public/Appear/style.css">
</head>
<body>
    <h1>Create New Task</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="create.php" method="post">
        <label for="category_id">Category (optional):</label>
        <select name="category_id" id="category_id">
            <option value="">Default</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <br>

        <label for="title">Title:</label>
        <input type="text" name="title" id="title" maxlength="100" required>
        <br>

        <label for="description">Description:</label>
        <textarea name="description" id="description"></textarea>
        <br>

        <label for="priority">Priority:</label>
        <select name="priority" id="priority">
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
        </select>
        <br>

        <label for="due_date">Due Date:</label>
        <input type="datetime-local" name="due_date" id="due_date" min="<?= $currentDatetime ?>">
        <br><br>

        <button type="submit">Create Task</button>
    </form>
    <div class="text-center mt-4">
        <a href="/Task_Manager_PRO/app/views/auth/taskmanager.php" class="btn btn-secondary">← Go Back</a>
    </div>
</body>
</html>
