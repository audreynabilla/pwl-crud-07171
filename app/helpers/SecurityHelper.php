<?php
function generateCSRFToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function getCSRFField()
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8') . '">';
}

function isLoggedIn()
{
    return isset($_SESSION['username']) && $_SESSION['username'] !== '';
}

function setFlash($message, $type = 'success')
{
    $_SESSION['pesan'] = $message;
    $_SESSION['tipe'] = $type;
}

function redirectTo($url)
{
    header('Location: ' . $url);
    exit;
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
