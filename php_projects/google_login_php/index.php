<?php
session_start();
require_once './config.php';

if (!isset($_SESSION['user_token']) && isset($_COOKIE['user_token'])) {
    // Retrieve the token from the cookie
    $token = $_COOKIE['user_token'];

    // Check the token in the database
    $sql = "SELECT * FROM users WHERE token = '$token'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        // Valid token, restore the session
        $_SESSION['user_token'] = $token;
    } else {
        // Invalid token, delete the cookie
        setcookie('user_token', '', time() - 3600, "/");
    }
}

if (isset($_SESSION['user_token'])) {
    header("Location: welcome.php");
    exit();
} else {
    echo "<a href='" . $client->createAuthUrl() . "'>Google Login</a>";
}
