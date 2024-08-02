<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_token'])) {
    header("Location: index.php");
    exit();
}

$sql = "SELECT * FROM member WHERE token = '{$_SESSION['user_token']}'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    $userinfo = mysqli_fetch_assoc($result);
} else {
    session_unset();
    session_destroy();
    setcookie('user_token', '', time() - 3600, "/");
    header("Location: index.php");
    exit();
}

// Check user role
if ($userinfo['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

echo "<h1>Welcome, " . $userinfo['name'] . "</h1>";
echo "<img src='" . $userinfo['profile_pic'] . "' alt='Profile Picture'>";
echo "<p>Email: " . $userinfo['email'] . "</p>";
echo "<a href='logout.php'>Logout</a>";
?>
