<?php
use Core\App;
use Core\Validator;
use Core\Database;

$db = App::resolve(Database::class);

// $config = require base_path('config.php');
// $db = new Database($config['database']);

$errors=[];

    if(! Validator::string($_POST['body'], 4, 100)){
        $errors['body']='A Body Requires minimum 4 max 100 charactes';
    };

    if(!empty($errors)){
        // custom code
        header('location: /notes');
        die();
    };
  
     if(empty($errors)){
        $db->query('INSERT INTO notes(body, user_id) VALUES(:body, :user_id)', [
            'body'=>$_POST['body'],
            'user_id'=>1
        ]);
        
        header('location: /notes');
        die();
    };
    