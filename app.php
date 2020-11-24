<?php
use Controllers\AbstractController;
use Controllers\AppController;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Pimple\Container;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

const SRC_DIR = __DIR__ . '/src/';

// Autoloader
require 'vendor/autoload.php';
spl_autoload_register(function ($class) {
    include SRC_DIR .  str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
});

// Container
$container = new Container();
$container['config'] = require SRC_DIR . 'config.php';
$container['db'] = function($c) {
    $db = $c['config']['database'];
    $url = 'mysql:host=' . $db['host'] . ';dbname=' . $db['name'] . ';charset=utf8mb4';
    return new PDO($url, $db['user'], $db['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
};
$container['twig'] = function ($c) {
    $loader = new FilesystemLoader(SRC_DIR . 'views');
    $twig = new Environment($loader, [
        'cache' => __DIR__ . '/cache/views',
        'auto_reload' => true,
        'debug' => $c['config']['debug'],
    ]);
    $twig->addGlobal('app', $c);
    if ($c['config']['debug']) {
        $twig->addExtension(new DebugExtension());
    }
    return $twig;
};

if ($container['config']['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// Router
$dispatcher = \FastRoute\simpleDispatcher(function(RouteCollector $r) {
    $r->addRoute('GET', '/', [AppController::class, 'index']);
    $r->addRoute('GET', '/about[/]', [AppController::class, 'about']);
});
$uri = $_SERVER['REQUEST_URI'];
$pos = strpos($uri, '?');
if ($pos !== false) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);
$routeInfo = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $uri);
switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo $container['twig']->render('404.twig');
        break;
    case Dispatcher::FOUND:
        [$class, $method] = $routeInfo[1];
        /** @var AbstractController $instance */
        $instance = new $class;
        $instance->setContainer($container);
        call_user_func_array([$instance, $method], [$routeInfo[2]]);
        break;
}
