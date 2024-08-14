<?php
session_start();

// Check if user is already authenticated
if (!isset($_GET['name']) || !isset($_GET['photo']) || !isset($_GET['token'])) {
    header('Location: index.php'); // Redirect to login if no user data is found
    exit();
}

// Store user data in session for later use
$_SESSION['user_name'] = $_GET['name'];
$_SESSION['user_photo'] = $_GET['photo'];
$_SESSION['user_token'] = $_GET['token'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <img src="<?php echo htmlspecialchars($_SESSION['user_photo']); ?>" alt="Profile Picture" style="width:100px;height:100px;">
    <p>Your Token: <?php echo htmlspecialchars($_SESSION['user_token']); ?></p>

    <form method="post" action="logout.php">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
