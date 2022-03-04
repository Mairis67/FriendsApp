<?php

session_start();

use App\Controllers\ArticleController;
use App\Controllers\AuthController;
use App\Controllers\UsersController;
use App\Redirect;
use App\View;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once 'vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    // Users
    $r->addRoute('GET', '/users', [UsersController::class, 'index']);
    $r->addRoute('GET', '/users/{id:\d+}', [UsersController::class, 'show']);

    // Register
    $r->addRoute('POST', '/users', [UsersController::class, 'register']);
    $r->addRoute('GET', '/users/register', [UsersController::class, 'showRegister']);

    // Login
    $r->addRoute('GET', '/users/home/{id:\d+}', [AuthController::class, 'home']);
    $r->addRoute('GET', '/users/login', [AuthController::class, 'showLogin']);
    $r->addRoute('POST', '/user/home', [AuthController::class, 'login']);


    // Articles
    $r->addRoute('GET', '/articles', [ArticleController::class, 'index']);
    $r->addRoute('GET', '/articles/{id:\d+}', [ArticleController::class, 'show']);

    // Create Article
    $r->addRoute('POST', '/articles', [ArticleController::class, 'store']);
    $r->addRoute('GET', '/articles/create', [ArticleController::class, 'create']);

    // Delete Article
    $r->addRoute('POST', '/articles/{id:\d+}/delete', [ArticleController::class, 'delete']);

    // Edit Article
    $r->addRoute('GET', '/articles/{id:\d+}/edit', [ArticleController::class, 'edit']);
    $r->addRoute('POST', '/articles/{id:\d+}', [ArticleController::class, 'update']);

    // Article Like
    $r->addRoute('POST', '/articles/{id:\d+}/like', [ArticleController::class, 'like']);
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        var_dump('404 Not Found');
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        var_dump('405 Method Not Allowed');
        break;
    case FastRoute\Dispatcher::FOUND:
        $controller = $routeInfo[1][0];
        $method = $routeInfo[1][1];

        $vars = $routeInfo[2];

        /** @var View $response */
        $response = (new $controller)->$method($vars);

        $loader = new FilesystemLoader('app/Views');
        $twig = new Environment($loader);

        if ($response instanceof View) {
            echo $twig->render($response->getPath() . '.twig', $response->getVars());
        }

        if($response instanceof Redirect) {
            header('Location: ' . $response->getLocation());
            exit;
        }
        break;
}

if(isset($_SESSION['errors'])) {
    unset($_SESSION['errors']);
}

if(isset($_SESSION['inputs'])) {
    unset($_SESSION['inputs']);
}