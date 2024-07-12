<?php
$routes=require '../routes.php';

function abort($statusCode=404){
    http_response_code($statusCode);
    require view("{$statusCode}.php");
    die();
};

// function routeToController($uri, $routes){
//     if (array_key_exists($uri, $routes)) {
//         require $routes[$uri];
//     } else {
//         abort();
//     };
// };
function routeToController($uri, $routes) {
    if (array_key_exists($uri, $routes)) {
        if (file_exists(base_path( $routes[$uri]))) {
            require base_path( $routes[$uri]);
        } else {
            echo "File not found: " . __DIR__ . '/' . $routes[$uri];
            abort();
        }
    } else {
        abort();
    }
}
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

routeToController($uri, $routes);