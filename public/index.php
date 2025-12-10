<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/helpers.php';

use App\Router;
use App\Controllers\UserController;

$router = new Router();

// Routes
$router->get('/', function () {
    echo "<h1>PHP Native Reference</h1><p>Endpoints: /users</p>";
});

// Basic REST-ish endpoints for users
$router->get('/users', [UserController::class, 'index']);
$router->get('/users/{id}', [UserController::class, 'show']);
$router->post('/users', [UserController::class, 'store']);

// dispatch
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
