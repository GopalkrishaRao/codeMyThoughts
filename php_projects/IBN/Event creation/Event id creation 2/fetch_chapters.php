<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "CMT_IBN";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['country']) && isset($_GET['state']) && isset($_GET['city'])) {
    $country = $_GET['country'];
    $state = $_GET['state'];
    $city = $_GET['city'];
    $sql = "SELECT chapter_id, chapter_name FROM chapters WHERE country = '$country' AND state = '$state' AND city = '$city'";
    $result = $conn->query($sql);

    $chapters = [];
    while ($row = $result->fetch_assoc()) {
        $chapters[] = $row;
    }
    echo json_encode($chapters);
} elseif (isset($_GET['chapter_id'])) {
    $chapter_id = $_GET['chapter_id'];
    $sql = "SELECT chapter_name FROM chapters WHERE chapter_id = '$chapter_id'";
    $result = $conn->query($sql);

    $chapter = $result->fetch_assoc();
    echo json_encode($chapter);
}

$conn->close();
?>
