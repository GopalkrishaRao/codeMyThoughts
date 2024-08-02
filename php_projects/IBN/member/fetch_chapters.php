<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "CMT_IBN";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT chapter_id, chapter_name FROM chapters";
$result = $conn->query($sql);

$chapters = [];
while($row = $result->fetch_assoc()) {
    $chapters[] = $row;
}

echo json_encode($chapters);

$conn->close();
?>
