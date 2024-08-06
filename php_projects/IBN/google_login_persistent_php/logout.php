<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['user_token'])) {
    // Clear the token from the database
    $sql = "UPDATE member SET token = NULL WHERE token = '{$_SESSION['user_token']}'";
    if (!mysqli_query($conn, $sql)) {
        error_log('Error clearing token: ' . mysqli_error($conn));
    }
}

// Unset session and destroy it
unset($_SESSION['user_token']);
session_destroy();

// Delete the login token cookie
setcookie('user_token', '', time() - 3600, "/");

// Clear the Google access token from the client
$client->revokeToken();

header("Location: index.php");
exit();
?>
