<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/helpers.php';

use App\Router;
use App\Controllers\UserController;
use App\Controllers\ExamplesController;
use App\Http\Request;
use App\Http\Response;

// ضبط CORS بسيط لدعم طلبات المتصفح
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$router = new Router();
$request = new Request();

// Routes
$router->get('/', function (Request $req) {
    echo "<h1>PHP Native Reference</h1><p>Endpoints: /users</p>";
});

// Basic REST-ish endpoints for users
$router->get('/users', [UserController::class, 'index']);
$router->get('/users/{id}', [UserController::class, 'show']);
$router->post('/users', [UserController::class, 'store']);
$router->put('/users/{id}', [UserController::class, 'update']);
$router->delete('/users/{id}', [UserController::class, 'destroy']);

// أمثلة PHP 8.3 (مرجع سريع)
$router->get('/examples/features', [ExamplesController::class, 'features']);
$router->post('/examples/json-validate', [ExamplesController::class, 'validateJson']);
$router->get('/examples/random', [ExamplesController::class, 'random']);
$router->get('/examples/enum', [ExamplesController::class, 'enum']);
$router->get('/examples/intl', [ExamplesController::class, 'intl']);
$router->get('/examples/intl-date', [ExamplesController::class, 'intlDate']);
$router->get('/examples/streams', [ExamplesController::class, 'streams']);
$router->get('/examples/streams-file', [ExamplesController::class, 'streamsFile']);
$router->get('/examples/fiber', [ExamplesController::class, 'fiber']);
$router->get('/examples/fiber-steps', [ExamplesController::class, 'fiberSteps']);
$router->get('/examples/attribute', [ExamplesController::class, 'attribute']);
$router->get('/examples/opcache', [ExamplesController::class, 'opcache']);

// dispatch
$router->dispatch($request);
