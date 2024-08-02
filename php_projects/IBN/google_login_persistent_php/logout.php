<?php
session_start();
unset($_SESSION['user_token']);
session_destroy();

// Delete the login token cookie
setcookie('user_token', '', time() - 3600, "/");

header("Location: index.php");
exit();
?>
