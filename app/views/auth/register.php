<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [
    'username' => '',
    'email'    => '',
    'password' => '',
    'general'  => ''
];

$input = [
    'username' => '',
    'email'    => ''
];

$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $errors['general'] = 'Invalid action. Please try again';
    } else {
        // Validate username
        $input['username'] = trim($_POST['username'] ?? '');
        if ($input['username'] === '') {
            $errors['username'] = 'Username is required.';
        } elseif (mb_strlen($input['username']) < 5) {
            $errors['username'] = 'Username should contain at least 5 characters.';
        }

        // Validate email
        $input['email'] = trim($_POST['email'] ?? '');
        if ($input['email'] === '') {
            $errors['email'] = 'E-mail is required.';
        } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid E-mail.';
        }

        // Validate password
        $passwordRaw = $_POST['password'] ?? '';
        if ($passwordRaw === '') {
            $errors['password'] = 'Password is required.';
        } elseif (mb_strlen($passwordRaw) < 8) {
            $errors['password'] = 'Password should contain at least 8 characters.';
        }

        if (empty(array_filter($errors))) {
            try {
                // DÜZELTİLMİŞ DOSYA YOLU
                require dirname(__DIR__, 3) . '/config/connection.php';
                
                $pdo = new PDO($dsn, $dbUser, $dbPass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);

                // Check for existing email
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
                $stmt->execute([$input['email']]);
                if ($stmt->fetchColumn() > 0) {
                    $errors['email'] = 'This E-mail is already taken.';
                } else {
                    // Insert new user
                    $stmt = $pdo->prepare(
                        "INSERT INTO users (username, email, password) VALUES (:u, :e, :p)"
                    );
                    $stmt->execute([
                        ':u' => $input['username'],
                        ':e' => $input['email'],
                        ':p' => password_hash($passwordRaw, PASSWORD_DEFAULT)
                    ]);

                    // Add default categories
                    $newUserId = $pdo->lastInsertId();
                    $defaultCategories = ['Work', 'School', 'Shopping', 'Personal'];
                    $stmtCat = $pdo->prepare("INSERT INTO categories (user_id, name) VALUES (:uid, :name)");
                    foreach ($defaultCategories as $catName) {
                        $stmtCat->execute([
                            ':uid'  => $newUserId,
                            ':name' => $catName
                        ]);
                    }

                    $successMessage = 'Registration successful!';
                    $input = ['username' => '', 'email' => '']; // Clear form
                }
            } catch (\PDOException $e) {
                $errors['general'] = 'Database error: ' . $e->getMessage();
            } catch (\Exception $e) {
                $errors['general'] = 'Error: ' . $e->getMessage();
            }
        }
    }
}

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>

<!doctype html>
<html>
<head>
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Comic+Neue:wght@400;700&display=swap" rel="stylesheet">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register</title>
  <link rel="stylesheet" href="/Task_Manager_PRO/public/Appear/style.css">
</head>
<body>
<div class="container py-5">
  <div class="card p-4 mx-auto" style="max-width: 400px;">
    <?php if ($flash): ?>
      <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>
    
    <?php if ($successMessage): ?>
      <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>
    
    <?php if ($errors['general']): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

      <div class="form-group">
        <label>Username</label>
        <input
        autocomplete="off"
          type="text"
          name="username"
          class="form-control <?= $errors['username'] ? 'is-invalid' : '' ?>"
          value="<?= htmlspecialchars($input['username']) ?>">
        <div class="invalid-feedback"><?= htmlspecialchars($errors['username']) ?></div>
      </div>

      <div class="form-group">
        <label>E-mail</label>
        <input
        autocomplete="off"
          type="email"
          name="email"
          class="form-control <?= $errors['email'] ? 'is-invalid' : '' ?>"
          value="<?= htmlspecialchars($input['email']) ?>">
        <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
      </div>

      <div class="form-group">
        <label>Password</label>
        <input
        autocomplete="off"
          type="password"
          name="password"
          class="form-control <?= $errors['password'] ? 'is-invalid' : '' ?>">
        <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
      </div>

      <button type="submit" name="register" class="btn btn-primary btn-block" style="margin-bottom: 10px; margin-top: 10px">
        Register
      </button>
    </form>
  </div>
</div>
<div class="text-center mt-4">
  <a href="/Task_Manager_PRO/public/index.php" class="btn btn-secondary">← Go Back</a>
</div>
</body>
</html>