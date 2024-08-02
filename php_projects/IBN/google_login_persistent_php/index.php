<?php
session_start();
require_once './config.php';

if (!isset($_SESSION['user_token']) && isset($_COOKIE['user_token'])) {
    // Retrieve the token from the cookie
    $token = $_COOKIE['user_token'];

    // Check the token in the database
    $sql = "SELECT * FROM member WHERE token = '$token'";
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
    $sql = "SELECT * FROM member WHERE token = '{$_SESSION['user_token']}'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $userinfo = mysqli_fetch_assoc($result);
        if ($userinfo['role'] == 'member') {
            header("Location: member_dashboard.php");
        } elseif ($userinfo['role'] == 'admin') {
            header("Location: admin_panel.php");
        }
        exit();
    } else {
        session_unset();
        session_destroy();
        setcookie('user_token', '', time() - 3600, "/");
        echo "<a href='" . $client->createAuthUrl() . "'>Google Login</a>";
    }
} else {
    echo "<a href='" . $client->createAuthUrl() . "'>Google Login</a>";
}
?>
