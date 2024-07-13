<?php

use Core\Session;
use Core\ValidationException;

session_start();

const BASE_PATH = __DIR__ . '/../';

// var_dump(BASE_PATH);

require BASE_PATH . 'Core/functions.php';
// require base_path('Database.php');
require base_path('Core/Rresponse.php');

spl_autoload_register(function($class){
   $class= str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require base_path($class.'.php');
});

require base_path('bootstrap.php');
// require base_path('Core/router.php');

$router = new \Core\Router();
$routes = require base_path('routes.php');
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

// $router->route($uri, $method);

try {
    $router->route($uri, $method);
} catch (ValidationException $exception) {
    Session::flash('errors', $exception->errors);
    Session::flash('old', $exception->old);

    return redirect($router->previousUrl());
}
Session::unflash();

