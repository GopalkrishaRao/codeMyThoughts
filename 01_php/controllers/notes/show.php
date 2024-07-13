<?php

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);
// $config = require base_path('config.php');
// $db = new Database($config['database']);

$bannerHeading="Note";

$currentUserId=1;

$note=$db->query('SELECT * FROM notes where id= :id', ['id'=>$_GET['id']])->findOrFail();
    
    
    authorise($note['user_id']===$currentUserId);



    require view('notes/show.view.php');
    
      ?>