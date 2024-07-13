<?php

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$currentUserId = 1;

$note = $db->query('select * from notes where id = :id', [
    'id' => $_GET['id']
])->findOrFail();

authorise($note['user_id'] === $currentUserId);

viewTwo("notes/edit.view.php", [
    'bannerHeading' => 'Edit Note',
    'errors' => [],
    'note' => $note
]);