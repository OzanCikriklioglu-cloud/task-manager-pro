<?php
// C:\xampp\htdocs\Task_Manager_PRO\public\logout.php
session_start();

// Tüm oturum verilerini temizle
$_SESSION = [];

// Eğer session çerezleri kullanılıyorsa, çerezi sil
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Oturumu tamamen yok et
session_destroy();

// Sayfa önbelleklemesini kapat (geri tuşunda da kontrol yapılmasını sağlar)
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

// Anasayfaya yönlendir
header('Location: index.php');
exit;
