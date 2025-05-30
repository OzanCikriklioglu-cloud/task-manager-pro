<?php
// index.php
session_start();

// Check for registration success flash message
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Comic+Neue:wght@400;700&display=swap" rel="stylesheet">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/Task_Manager_PRO/public/Appear/style.css">

</head>
<body>
    <canvas id="matrix-canvas"></canvas>
    <?php if ($flash): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="hero-section">
        <div class="container">
            <h1 class="mb-4" style="color:rgb(209, 15, 15);">Welcome to Task Manager Pro</h1>
            <div class="btn-group">
                <a href="/Task_Manager_PRO/app/views/auth/login.php" class="btn btn-primary btn-lg px-4" style="margin-right: 5px;margin-left: 390px;" >Login</a>
                <a href="/Task_Manager_PRO/app/views/auth/register.php" class="btn btn-outline-primary btn-lg px-4">Sign Up</a>
            </div>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>