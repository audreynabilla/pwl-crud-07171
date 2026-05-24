<?php
session_start();

require_once __DIR__ . '/../app/helpers/SecurityHelper.php';
$conn = require __DIR__ . '/../config/database.php';

$url = $_GET['url'] ?? '';
$url = trim(rtrim($url, '/'), '/');

if ($url === '') {
    $url = 'produk/index';
}

$params = explode('/', $url);
$controllerName = !empty($params[0]) ? strtolower($params[0]) : 'produk';
$method = $params[1] ?? 'index';
$arguments = array_slice($params, 2);

$publicRoutes = [
    'auth/login',
    'auth/register',
];

$currentRoute = $controllerName . '/' . $method;
if (!isLoggedIn() && !in_array($currentRoute, $publicRoutes, true)) {
    redirectTo('index.php?url=auth/login');
}

$className = ucfirst($controllerName) . 'Controller';
$controllerFile = __DIR__ . '/../app/controllers/' . $className . '.php';

if (!is_file($controllerFile)) {
    http_response_code(404);
    echo '404 - Controller tidak ditemukan.';
    exit;
}

require_once $controllerFile;

if (!class_exists($className)) {
    http_response_code(404);
    echo '404 - Class controller tidak ditemukan.';
    exit;
}

$controller = new $className($conn);

if (!method_exists($controller, $method)) {
    http_response_code(404);
    echo '404 - Method tidak ditemukan.';
    exit;
}

call_user_func_array([$controller, $method], $arguments);
