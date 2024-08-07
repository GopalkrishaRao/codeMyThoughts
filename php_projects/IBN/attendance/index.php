<?php
// Database connection
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "CMT_IBN";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$locatonRange = 20;

// GPS Distance Calculation Function
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371000; 
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c;

    return $distance;
}

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestPayload = file_get_contents("php://input");
    $data = json_decode($requestPayload, true);

    $userLat = $data['lat'];
    $userLng = $data['lng'];
    $venueLat = $data['venueLat'];
    $venueLng = $data['venueLng'];
    $memberType = $data['memberType'];
    $slNo = $data['slNo'];

    // Calculate the distance between the user and the venue
    $distance = calculateDistance($userLat, $userLng, $venueLat, $venueLng);

    $response = [];
    if ($distance <= $locatonRange) { 
        // Update attendance record
        $updateQuery = "
            UPDATE attendance 
            SET time_of_authentication = NOW(), attended_as = ?,
            attendance_status = 'Present' 
            WHERE sl_no = ?
        ";

        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("si", $memberType, $slNo);
        $updateStmt->execute();
        $updateStmt->close();
        
        $response['message'] = "You are within the venue. Marked as present.";
        
    } else {
        $response['message'] = "You are not near the venue. Please come near the venue and try again.";
    
    }

    // Return the response as JSON
    echo json_encode($response);
    exit;
}

// Placeholder user ID
$member_id = "AFG/BDG/0001/001";

// Fetch today's meetings for the user
$todaysDate = date('Y-m-d');

// Check chapter status
$chapterStatusQuery = "
    SELECT c.chapter_status 
    FROM chapters c
    JOIN member m ON c.chapter_id = m.chapter_id
    WHERE m.member_id = ?
";

$chapterStatusStmt = $conn->prepare($chapterStatusQuery);
$chapterStatusStmt->bind_param("s", $member_id);
$chapterStatusStmt->execute();
$chapterStatusResult = $chapterStatusStmt->get_result();
$chapterStatus = $chapterStatusResult->fetch_assoc()['chapter_status'];
$chapterStatusStmt->close();

if ($chapterStatus !== 'active') {
    die("
    oops!! Your chapter is inactive Contact the Admin
    ");
}

// Member details
$memberDetailsQuery = "
    SELECT name, email, ph_number, profile_pic, chapter_id, member_status 
    FROM member 
    WHERE member_id = ?
";

// Prepare and execute the query
$memberDetailsStmt = $conn->prepare($memberDetailsQuery);
$memberDetailsStmt->bind_param("s", $member_id);
$memberDetailsStmt->execute();
$memberDetailsResult = $memberDetailsStmt->get_result();
$memberDetails = $memberDetailsResult->fetch_assoc();
$memberDetailsStmt->close();

// Check member status
if ($memberDetails['member_status'] !== 'active') {
die("
   oops!! You are an inactive member. Please contact your admin
    ");
}

$meetingsQuery = "
    SELECT 
        e.event_type, 
        e.meeting_type, 
        e.start_date, 
        e.start_time, 
        e.end_time, 
        e.venue,
        e.latitude,
        e.longitude,
        m.profile_pic,
        m.name,
        m.member_id,
        c.chapter_name,
        a.payment_status,
        a.sl_no,
        a.attendance_status
    FROM 
        events e
    JOIN 
        attendance a ON e.event_id = a.event_id
    JOIN 
        member m ON a.member_id = m.member_id
    JOIN 
        chapters c ON m.chapter_id = c.chapter_id
    WHERE 
        a.member_id = ? 
        AND e.start_date = ?
";

$stmt = $conn->prepare($meetingsQuery);
$stmt->bind_param("ss", $member_id, $todaysDate);
$stmt->execute();
$meetingsResult = $stmt->get_result();
$stmt->close();

$meetings = [];

while ($row = $meetingsResult->fetch_assoc()) {
    // Calculate attendance start time
    $meetingStartTime = new DateTime($row['start_time']);
    $attendanceStartTime = $meetingStartTime->sub(new DateInterval('PT30M'))->format('H:i:s');
    $row['attendance_start_time'] = $attendanceStartTime;
    $meetings[] = $row;
}

//Code to get current time 
$timezone = new DateTimeZone('Asia/Kolkata');
$currentDateTime = new DateTime('now', $timezone);
$currentTime = $currentDateTime->format('H:i:s');
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="styles.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <title>User Profile</title>
</head>
<body>
<!-- new code -->
<div class="profile_header">
      <div class="header_images">
         <div class="profile_image">
            <img src="<?php echo htmlspecialchars($memberDetails['profile_pic']); ?>" alt="Profile Picture" 
            />
         </div>
         <div class="logo">
            <img src="logo.png" alt="company logo"/>
         </div>
      </div>
        <div class="header_text">
            <p class="name">
                <?php echo htmlspecialchars($memberDetails['name'])?>
            </p>
            <p class="role">Frontend Engineer</p>
            <p class="company">Amazon</p>
        </div>
</div>

     <div class="member_id">
      <?= $member_id?>
     </div>

     <div class="card social_media">
      <div class="card_heading">
         <p> Social Media</p>
         
      </div>
      <div class="card_content">
         <i class="fa-brands fa-instagram"></i>
         <i class="fa-brands fa-facebook-f"></i>
      </div>
     </div>

     <div class="card business_info">
      <div class="card_heading">
         <p>Business Information</p>
         
      </div>
      <div class="card_content">
         <i class="fa-solid fa-building"></i>
        <div class="card_content_list">
         <p class="sub_heading">Product/Service</p>
         <p>Website Design</p>
        </div>
      </div>
      <div class="card_content">
         <i class="fa-solid fa-building"></i>
        <div class="card_content_list">
         <p class="sub_heading">Industry</p>
         <p>Computer hardware & Solutions</p>
        </div>
      </div>
      <div class="card_content">
         <i class="fa-solid fa-building"></i>
        <div class="card_content_list">
         <p class="sub_heading">Company</p>
         <p>Amazon</p>
        </div>
      </div>
     </div>

     <div class="card personal_bio">
      <div class="card_heading">
         <p>Personal Bio</p>
         
      </div>
      <div class="personal_details">
         <div class="card_content">
            <p class="heading">Name:</p>
            <p>
                <?php echo htmlspecialchars($memberDetails['name'])?>
            </p>
         </div>
         <div class="card_content">
            <p class="heading">Blood Group:</p>
            <p>A+ve</p>
         </div>

         <div class="card_content">
            <p class="heading">Birthday:</p>
            <p>20 Jun</p>
         </div>

         <div class="card_content">
            <p class="heading">My Bio:</p>
            <p>Tell about yourself</p>
         </div>
      </div>
     </div>
    
    <?php if (!empty($meetings)) { ?>
    
    <div class="card meetings">
        <div class="card_heading">
                <p>Your Meetings</p>
        </div>
        <div class="card_content ">
         <!--Render this div for message (meeting_content)-->
            <?php foreach ($meetings as $meeting) { ?>
                <?php if ($currentTime <= $meeting['end_time']) { 
                // Check if the current time is less than or equal to the meeting end time ?>
                <div class="meeting_content">
                    <div>
                        <p><b><?php echo htmlspecialchars($meeting['event_type']); ?> Meeting</b></p>
                        <p><?php echo htmlspecialchars($meeting['start_date']); ?></p>

                        <p><?php echo htmlspecialchars($meeting['payment_status']); ?></p>
                    </div>
                <?php if (htmlspecialchars($meeting['payment_status'])==="Paid") { ?>   
                

                <?php $attendanceStartTime = $meeting['attendance_start_time']; ?>
                <?php if ($attendanceStartTime <= $currentTime) { ?>
                    <!-- hide attendace button if already put attendace -->
                    <?php if (htmlspecialchars($meeting['attendance_status'])==="Absent") { ?>
                        <div>
                            <button 
                                onclick="attendAs('member', 
                                <?php echo $meeting['sl_no']; ?>, 
                                <?php echo $meeting['latitude']; ?>, 
                                <?php echo $meeting['longitude']; ?>)"
                                >
                                Member
                            </button>
                            <button 
                                onclick="attendAs('substitute',
                                <?php echo $meeting['sl_no']; ?>, 
                                <?php echo $meeting['latitude']; ?>, 
                                <?php echo $meeting['longitude']; ?>)">
                                Substitute
                            </button>
                        </div>
                    <?php } else { ?>
                        <p class="success_message"><i class="fa-solid fa-check"></i>Attendence marked</p>
                     <?php } ?>
                <?php } else { ?>
                    <p>Attendance system will start from <?php echo htmlspecialchars($attendanceStartTime); ?></p>
                <?php } ?>
                

                <?php } else { ?>
                    <p class="failure_message">
                        Your payment is Due
                      </p>
                <?php } ?>
                    
                      <p id="attendace_fail" class="failure_message">
                        Your are not near the location. Please come near the location and Try Again
                      </p>
           
                </div>
                <!-- end time check -->
            <?php } ?>


            <!-- end foreach -->
        <?php } ?> 
        </div>

    </div>
    
    <?php } else { ?>
        <div class="card meetings">
        <div class="card_heading">
                <p>No meetings scheduled for today.</p>
        </div>
    </div>
    <?php } ?>
<!-- end new ui -->

    <p id="message"></p>
    <script>
        function attendAs(memberType, slNo, venueLat, venueLng) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    fetch('index.php', {
                        // Replace 'your_php_file.php' with the actual filename
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ lat, lng, venueLat, venueLng, memberType, slNo })
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('message').innerText = data.message;
                         if (data.message.includes("Marked as present")) {
                        location.reload();
                }
                if(data.message.includes("You are not near the venue")){
                            alert('test');
                            document.getElementById('attendace_fail').style.display = "block";

                        }
                    })
                    .catch(error => {
                        document.getElementById('message').innerText = "Error: " + error.message;
                    });
                }, showError);
            } else {
                document.getElementById('message').innerText = "Geolocation is not supported by this browser.";
            }
        }
        function showError(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    document.getElementById('message').innerText = "User denied the request for Geolocation.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    document.getElementById('message').innerText = "Location information is unavailable.";
                    break;
                case error.TIMEOUT:
                    document.getElementById('message').innerText = "The request to get user location timed out.";
                    break;
                case error.UNKNOWN_ERROR:
                    document.getElementById('message').innerText = "An unknown error occurred.";
                    break;
            }
        }
    </script>
</body>
</html>