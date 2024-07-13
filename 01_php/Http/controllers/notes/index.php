    <?php

use Core\Database;

$config = require base_path('config.php');
$db = new Database($config['database']);

$bannerHeading="My Notes";


    $notes=$db->query('SELECT * FROM notes where user_id=1')->fetchAll();
    
    require view('notes/index.view.php');
    
      ?>