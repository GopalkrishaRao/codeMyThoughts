    <?php
use Core\Database;


$config = require base_path('config.php');

$db = new Database($config['database']);

$bannerHeading="Note";
    $currentUserId=1;


    $note=$db->query('SELECT * FROM notes where id= :id', ['id'=>$_GET['id']])->findOrFail();


    // if($note['user_id']!==$currentUserId){
    //   // response from Response.php
    //   abort(Response::FORBIDDEN);
    // };

    authorise($note['user_id']===$currentUserId);


    require view('notes/show.view.php');
    
      ?>