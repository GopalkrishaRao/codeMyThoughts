<?php
use Core\App;
use Core\Database;

// $config = require base_path('config.php');
// $db = new Database($config['database']);

$db = App::resolve(Database::class);
$bannerHeading="Note";

$currentUserId=1;

    // check user exits or not
$note=$db->query('SELECT * FROM notes where id= :id', ['id'=>$_POST['id']])->findOrFail();
    authorise($note['user_id']===$currentUserId);

    // Send a delete request to DB
$db->query('DELETE FROM notes WHERE id=:id', [
        'id'=>$_GET['id']
    ]);

    // Redirect
    header('location: /notes');
    exit();
