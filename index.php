<?php
session_start();

$config = json_decode(file_get_contents('config/config.json'), true);

$isCaptchaVerified = isset($_COOKIE['z_captcha_verified']) && $_COOKIE['z_captcha_verified'] === 'true';

if (!$isCaptchaVerified) {
    $originalUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $returnUrl = urlencode($originalUrl);
    header("Location: frontend/index.php?return=" . $returnUrl);
    exit();
}

$cookieDuration = $config['security']['verification_cookie_duration'];

if (time() > (isset($_COOKIE['z_captcha_verified_time']) ? $_COOKIE['z_captcha_verified_time'] : 0) + $cookieDuration) {
    $originalUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $returnUrl = urlencode($originalUrl);
    setcookie('z_captcha_verified', '', time() - 3600, '/', '', true, true);
    header("Location: frontend/index.php?return=" . $returnUrl);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Protected Content</title>
</head>
<body>
    <h1>Welcome to Protected Content</h1>
    <p>This page is only accessible after successful Z-Captcha verification.</p>
</body>
</html>