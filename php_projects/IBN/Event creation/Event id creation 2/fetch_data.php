<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "CMT_IBN";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['country']) && isset($_GET['state'])) {
    // Fetch cities based on country and state
    $country = $_GET['country'];
    $state = $_GET['state'];
    $sql = "SELECT DISTINCT city FROM chapters WHERE country = '$country' AND state = '$state'";
    $result = $conn->query($sql);

    $cities = [];
    while($row = $result->fetch_assoc()) {
        $cities[] = $row['city'];
    }
    echo json_encode($cities);

} elseif (isset($_GET['country'])) {
    // Fetch states based on country
    $country = $_GET['country'];
    $sql = "SELECT DISTINCT state FROM chapters WHERE country = '$country'";
    $result = $conn->query($sql);

    $states = [];
    while($row = $result->fetch_assoc()) {
        $states[] = $row['state'];
    }
    echo json_encode($states);

} else {
    // Fetch unique list of countries
    $sql = "SELECT DISTINCT country FROM chapters";
    $result = $conn->query($sql);

    $countries = [];
    while($row = $result->fetch_assoc()) {
        $countries[] = $row['country'];
    }
    echo json_encode($countries);
}

$conn->close();
?>
