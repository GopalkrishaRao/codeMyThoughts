<?php
$bannerHeading="Create New Note";
require base_path('Core/Validator.php');

$config = require base_path('config.php');
use Core\Database;
use Core\Validator;

$db = new Database($config['database']);


if($_SERVER['REQUEST_METHOD']==='POST'){

    $errors=[];

    // $validator=new Validator();
    // for a static class above call is not required


    // prevent empty body submission
    // if($validator->string($_POST['body'])){
    //     $errors['body']='A Body is Required';
    // };

    // to call static method use method::type

    if(! Validator::string($_POST['body'], 4, 100)){
        $errors['body']='A Body Requires minimum 4 max 100 charactes';
    };

  
     if(empty($errors)){
        $db->query('INSERT INTO notes(body, user_id) VALUES(:body, :user_id)', [
            'body'=>$_POST['body'],
            'user_id'=>1
        ]);
    };
    
};

require view('notes/create.view.php');