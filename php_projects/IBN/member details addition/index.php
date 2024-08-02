<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "CMT_IBN";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$member_id = 'AFG/BDG/0001/001'; // Replace this with the actual member_id you want to fetch for testing

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])) {
    $role = $_POST['role'];
    $user_type = isset($_POST['user_type']) ? $_POST['user_type'] : 'member';
    $name = $_POST['name'];
    $alt_email = $_POST['alt_email'];
    $ph_number = $_POST['ph_number'];
    $dob = !empty($_POST['dob']) ? $_POST['dob'] : NULL;
    $profile_pic = $_POST['profile_pic'];
    $anniversary = !empty($_POST['anniversary']) ? $_POST['anniversary'] : NULL;
    $husband_wife_name = $_POST['husband_wife_name'];
    $gender = $_POST['gender'];
    $business_type = $_POST['business_type'];
    $industry = $_POST['industry'];
    $sector = $_POST['sector'];

    $update_fields = [];
    if (!empty($role)) $update_fields[] = "role='$role'";
    if (!empty($user_type)) $update_fields[] = "user_type='$user_type'";
    if (!empty($name)) $update_fields[] = "name='$name'";
    if (!empty($alt_email)) $update_fields[] = "alt_email='$alt_email'";
    if (!empty($ph_number)) $update_fields[] = "ph_number='$ph_number'";
    if (!empty($dob)) $update_fields[] = "dob='$dob'";
    if (!empty($profile_pic)) $update_fields[] = "profile_pic='$profile_pic'";
    if (!empty($anniversary)) $update_fields[] = "anniversary='$anniversary'";
    if (!empty($husband_wife_name)) $update_fields[] = "husband_wife_name='$husband_wife_name'";
    if (!empty($gender)) $update_fields[] = "gender='$gender'";
    if (!empty($business_type)) $update_fields[] = "business_type='$business_type'";
    if (!empty($industry)) $update_fields[] = "industry='$industry'";
    if (!empty($sector)) $update_fields[] = "sector='$sector'";

    if (!empty($update_fields)) {
        $update_sql = "UPDATE member SET " . implode(", ", $update_fields) . " WHERE member_id='$member_id'";
        if ($conn->query($update_sql) === TRUE) {
            echo "Record updated successfully";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
}

$sql = "SELECT * FROM member WHERE member_id='$member_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "0 results for member ID: $member_id";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member Details</title>
</head>
<body>

<h2>Member Details</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <label for="member_id">Member ID:</label><br>
    <input type="text" id="member_id" name="member_id" value="<?php echo $row['member_id']; ?>" readonly><br><br>

    <label for="chapter_id">Chapter ID:</label><br>
    <input type="text" id="chapter_id" name="chapter_id" value="<?php echo $row['chapter_id']; ?>" readonly><br><br>

    <label for="role">Role:</label><br>
    <input type="radio" id="chapter_admin" name="role" value="chapter_admin" <?php echo ($row['role'] == 'chapter_admin') ? 'checked' : ''; ?>> Chapter Admin
    <input type="radio" id="member" name="role" value="member" <?php echo ($row['role'] == 'member') ? 'checked' : ''; ?>> Member<br><br>

    <label for="user_type">User Type:</label><br>
    <input type="radio" id="user_type_member" name="user_type" value="member" <?php echo (empty($row['user_type']) || $row['user_type'] == 'member') ? 'checked' : ''; ?>> Member
    <input type="radio" id="user_type_president" name="user_type" value="president" <?php echo ($row['user_type'] == 'president') ? 'checked' : ''; ?>> President
    <input type="radio" id="user_type_vice_president" name="user_type" value="vice_president" <?php echo ($row['user_type'] == 'vice_president') ? 'checked' : ''; ?>> Vice-President
    <input type="radio" id="user_type_secretary" name="user_type" value="secretary" <?php echo ($row['user_type'] == 'secretary') ? 'checked' : ''; ?>> Secretary<br><br>

    <label for="name">Name:</label><br>
    <input type="text" id="name" name="name" value="<?php echo $row['name']; ?>"><br><br>

    <label for="email">Email:</label><br>
    <input type="text" id="email" name="email" value="<?php echo $row['email']; ?>" readonly><br><br>

    <label for="alt_email">Alternate Email:</label><br>
    <input type="text" id="alt_email" name="alt_email" value="<?php echo $row['alt_email']; ?>"><br><br>

    <label for="ph_number">Phone Number:</label><br>
    <input type="text" id="ph_number" name="ph_number" value="<?php echo $row['ph_number']; ?>"><br><br>

    <label for="dob">Date of Birth:</label><br>
    <input type="date" id="dob" name="dob" value="<?php echo $row['dob']; ?>"><br><br>

    <label for="profile_pic">Profile Picture URL:</label><br>
    <input type="text" id="profile_pic" name="profile_pic" value="<?php echo $row['profile_pic']; ?>"><br><br>

    <label for="anniversary">Anniversary:</label><br>
    <input type="date" id="anniversary" name="anniversary" value="<?php echo $row['anniversary']; ?>"><br><br>

    <label for="husband_wife_name">Husband/Wife Name:</label><br>
    <input type="text" id="husband_wife_name" name="husband_wife_name" value="<?php echo $row['husband_wife_name']; ?>"><br><br>

    <label for="gender">Gender:</label><br>
    <select id="gender" name="gender">
        <option value="Male" <?php echo ($row['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
        <option value="Female" <?php echo ($row['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
        <option value="Other" <?php echo ($row['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
    </select><br><br>

    <label for="business_type">Business Type:</label><br>
    <input type="text" id="business_type" name="business_type" value="<?php echo $row['business_type']; ?>"><br><br>

    <label for="industry">Industry:</label><br>
    <input type="text" id="industry" name="industry" value="<?php echo $row['industry']; ?>"><br><br>

    <label for="sector">Sector:</label><br>
    <input type="text" id="sector" name="sector" value="<?php echo $row['sector']; ?>"><br><br>

    <input type="submit" name="save" value="Save">
</form>

</body>
</html>
