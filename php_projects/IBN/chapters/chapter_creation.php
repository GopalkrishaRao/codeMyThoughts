<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "CMT_IBN";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function generateUniqueCode($country, $state) {
    global $conn;

    $country = $conn->real_escape_string($country);
    $state = $conn->real_escape_string($state);
    
    $country_code = strtoupper(substr($country, 0, 3));
    $state_code = strtoupper(substr($state, 0, 3));
    
    $sql = "SELECT numb_id FROM chapters WHERE country = '$country' AND state = '$state' ORDER BY numb_id DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_number = $row['numb_id'];
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }

    $formatted_number = str_pad($new_number, 4, '0', STR_PAD_LEFT);
    $full_chapter_id = "$country_code/$state_code/$formatted_number";

    return [$new_number, $full_chapter_id];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // $date = date('Y-m-d');  
    $country = $_POST['country'];
    $state = $_POST['state'];
    $city = $conn->real_escape_string($_POST['city']);
    $city_tier = $conn->real_escape_string($_POST['city_tier']);
    $area = $conn->real_escape_string($_POST['area']);
    $admin = $conn->real_escape_string($_POST['admin']);
    $chapter_name = $conn->real_escape_string($_POST['chapter_name']);

    list($numb_id, $chapter_id) = generateUniqueCode($country, $state);

    // Insert country and state names directly
    $stmt = $conn->prepare("INSERT INTO chapters (country, state, city, city_tier, area, admin, numb_id, chapter_id, chapter_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === FALSE) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ssssssiss", $country, $state, $city, $city_tier, $area, $admin, $numb_id, $chapter_id, $chapter_name);

    if ($stmt->execute()) {
        echo "New chapter created successfully with Unique Code: $chapter_id";
    } else {
        die("Error executing statement: " . $stmt->error);
    }

    $stmt->close();
}

$conn->close();
?>
