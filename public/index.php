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
                <a href="/Task_Manager_PRO/app/views/auth/login.php" class="btn btn-primary btn-lg px-4" >Login</a>
                <a href="/Task_Manager_PRO/app/views/auth/register.php" class="btn btn-outline-primary btn-lg px-4">Sign Up</a>
            </div>
        </div>
    </div>

    <script>
        const canvas = document.getElementById("matrix-canvas");
        const ctx = canvas.getContext("2d");

        canvas.height = window.innerHeight;
        canvas.width = window.innerWidth;

        const letters = "154879623=$#£ABCDEFGHIJKLMNOPQRs̴̢̲̙̝̝̞͉͂̏̃d̵̡̦̝̱̖̙͊̂̎̓͘a̷̛̦͚̯͎̼̝͍̤͑̈̿̚ͅs̵̛̫̹̤̳̮̤̟̀̈́͊ḋ̴̡̛̻̬̱͍̣̊͒̅̍̃͜͝ͅVWXYZ>$<|a̶̡̫̺̙̹̱̬͌̆][%̵͎͊̓͑͗̓̌͝&̸̡̻̋̏͐̑͠";
        const fontSize = 21;
        const columns = canvas.width / fontSize;
        const drops = Array.from({ length: columns }, () => Math.random() * canvas.height);

        function draw() {
            ctx.fillStyle = "rgba(0, 0, 0, 0.05)";
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            ctx.fillStyle = "#0F0"; // Neon yeşil
            ctx.font = fontSize + "px monospace";

            for (let i = 0; i < drops.length; i++) {
                const text = letters[Math.floor(Math.random() * letters.length)];
                ctx.fillText(text, i * fontSize, drops[i] * fontSize);

                if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
                    drops[i] = 0;
                }
                drops[i]++;
            }
        }

        setInterval(draw, 60); // Yavaş akış
    </script>


    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>