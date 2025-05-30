<?php
// login.php
session_start();

$errors = [
    'email' => '',
    'password' => '',
    'general' => ''
];

$input = [
    'email' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $input['email'] = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Email validation
    if ($input['email'] === '') {
        $errors['email'] = 'Email is required.';
    }

    // Password validation
    if ($password === '') {
        $errors['password'] = 'Password is required.';
    }

    // Proceed if no field errors
    if (empty($errors['email']) && empty($errors['password'])) {
        try {
            require 'C:\xampp\htdocs\Task_Manager_PRO\config\connection.php';
            $pdo = new PDO($dsn, $dbUser, $dbPass, $options);

            // Get user data
            $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE email = :e');
            $stmt->execute([':e' => $input['email']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['loggedin'] = true;
                
                header('Location: taskmanager.php');
                exit;
            } else {
                $errors['general'] = 'Invalid email or password combination.';
            }
        } catch (PDOException $ex) {
            $errors['general'] = 'An error occurred during login. Please try again.';
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Comic+Neue:wght@400;700&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Login - Retro</title>
    <link rel="stylesheet" href="/Task_Manager_PRO/public/Appear/style.css">
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
  <form method="POST" style="width: 300px;">
    <h3 class="mb-4">Login</h3>
    
    <?php if ($errors['general']): ?>
      <div class="alert alert-danger mb-3"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>

    <div class="form-group">
      <label for="email">Email</label>
      <input 
      autocomplete="off"
        type="email" 
        id="email" 
        name="email" 
        class="form-control <?= $errors['email'] ? 'is-invalid' : '' ?>" 
        value="<?= htmlspecialchars($input['email']) ?>">
      <?php if ($errors['email']): ?>
        <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input 
      autocomplete="off"
        type="password" 
        id="password" 
        name="password" 
        class="form-control <?= $errors['password'] ? 'is-invalid' : '' ?>">
      <?php if ($errors['password']): ?>
        <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
      <?php endif; ?>
    </div>

    <button 
      type="submit" 
      name="login" 
      class="btn btn-primary btn-block">
      Login
    </button>
    
    <div class="text-center mt-3">
      Don't have an account? <a href="register.php">Register here</a>
    </div>
  </form>
  <div class="text-center mt-4">
  <a href="/Task_Manager_PRO/public/index.php" class="btn btn-secondary">← Go Back</a>
</div>
</body>
</html>