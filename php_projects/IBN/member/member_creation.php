<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "CMT_IBN";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function generateMemberId($chapter_id) {
    global $conn;

    $chapter_id = $conn->real_escape_string($chapter_id);

    $sql = "SELECT numb_id FROM member WHERE chapter_id = '$chapter_id' ORDER BY numb_id DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_number = $row['numb_id'];
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }

    $formatted_number = str_pad($new_number, 3, '0', STR_PAD_LEFT);
    $full_member_id = "$chapter_id/$formatted_number";

    return [$new_number, $full_member_id];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $chapter = $_POST['chapter'];
    $name= $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $business_type = $conn->real_escape_string($_POST['business_type']);
    $industry = $conn->real_escape_string($_POST['industry']);
    $sector = $conn->real_escape_string($_POST['sector']);
    $role = $conn->real_escape_string($_POST['role']);

    // Check for unique email
    $sql = "SELECT email FROM member WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        die("Email already exists. Please use a different email.");
    }

    list($numb_id, $member_id) = generateMemberId($chapter);

    $stmt = $conn->prepare("INSERT INTO member (name, chapter_id, member_id, email, business_type, industry, sector, role, numb_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === FALSE) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ssssssssi",$name, $chapter, $member_id, $email, $business_type, $industry, $sector, $role, $numb_id);

    if ($stmt->execute()) {
        echo "New member created successfully with Member ID: $member_id";
    } else {
        die("Error executing statement: " . $stmt->error);
    }

    $stmt->close();
}

$conn->close();
?>
