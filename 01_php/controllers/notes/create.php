<?php
$bannerHeading="Create New Note";
require base_path('Core/Validator.php');

$config = require base_path('config.php');
// use Core\Database;

// $db = new Database($config['database']);

// $errors=[];
// if($_SERVER['REQUEST_METHOD']==='POST'){

//     if(! Validator::string($_POST['body'], 4, 100)){
//         $errors['body']='A Body Requires minimum 4 max 100 charactes';
//     };

  
//      if(empty($errors)){
//         $db->query('INSERT INTO notes(body, user_id) VALUES(:body, :user_id)', [
//             'body'=>$_POST['body'],
//             'user_id'=>1
//         ]);
//     };
    
// };

require view('notes/create.view.php');