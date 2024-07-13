<?php
function dd($vale){
            echo "<pre>";
            var_dump($vale);
            echo "</pre>";
            die();
        };
    
    //    dd($_SERVER);

    function abort($code = 404)
{
    http_response_code($code);
    require view("{$code}.php");
    die();
}

    function urlIs($value){
        return $_SERVER["REQUEST_URI"]===$value;
    };

    function authorise($condition, $status=Response::FORBIDDEN){
        if(!$condition){
           // response from Response.php
            abort($status); 
        }
    };

    function base_path($path){
        return BASE_PATH . $path;
    };

    function view($path){
        return base_path('view/'.$path);
    }


    function viewTwo($path, $attributes = [])
{
    extract($attributes);

    require base_path('view/' . $path);
}