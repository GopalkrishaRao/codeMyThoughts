<?php

// Database connection
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "cmt_ibn2";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Placeholder user ID
$member_id = "IND-KA-0001-001";

date_default_timezone_set('Asia/Kolkata');
$currentDateTime = new DateTime();
$currentDate = $currentDateTime->format('Y-m-d'); 
$currentTime = $currentDateTime->format('H:i:s'); 

// Remark attendace 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sl_no'])) {
    // Sanitize and process the sl_no as a string
    $slNo = $conn->real_escape_string($_POST['sl_no']);

    // Prepare the SQL query to reset attendance
    $sql = "UPDATE attendance 
            SET attendance_status = 'Absent', 
                time_of_authentication = NULL, 
                attended_as = NULL 
            WHERE sl_no = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $slNo); 

        if ($stmt->execute()) {
            echo 'Attendance reset successfully';
        } else {
            echo 'Error: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        echo 'Error: ' . $conn->error;
    }

    $conn->close();
    exit; 
}

// Member details
$memberDetailsQuery = "
    SELECT 
        m.name, 
        m.email, 
        m.ph_number, 
        m.profile_pic, 
        m.chapter_id, 
        m.statu AS member_status, 
        c.statu AS chapter_status 
    FROM 
        member m
    LEFT JOIN 
        chapters c ON m.chapter_id = c.chapter_id
    WHERE 
        m.member_id = ?
";

// Prepare and execute the query
$memberDetailsStmt = $conn->prepare($memberDetailsQuery);
$memberDetailsStmt->bind_param("s", $member_id);
$memberDetailsStmt->execute();
$memberDetailsResult = $memberDetailsStmt->get_result();
$memberDetails = $memberDetailsResult->fetch_assoc();
$memberDetailsStmt->close();


// Prepare the query
$meetingsQuery = 
"SELECT 
    m.member_id,
    m.chapter_id,
    c.state,
    c.country,
    e.location AS chapter_location,
    e.latitude AS chapter_latitude,
    e.longitude AS chapter_longitude,
    e.event_id AS ch_ev_id,
    e.start_date AS ch_ev_st_date,
    e.end_date AS ch_ev_end_date,
    e.start_time AS ch_ev_st_time,
    e.end_time AS ch_ev_end_time,
    se.event_id AS st_ev_id,
    se.start_date AS st_ev_st_date,
    se.end_date AS st_ev_end_date,
    se.start_time AS st_ev_st_time,
    se.end_time AS st_ev_end_time,
    se.location AS state_location,
    se.latitude AS state_latitude,
    se.longitude AS state_longitude,
    ne.event_id AS nat_ev_id,
    ne.start_date AS nat_ev_st_date,
    ne.end_date AS nat_ev_end_date,
    ne.start_time AS nat_ev_st_time,
    ne.end_time AS nat_ev_end_time,
    ne.location AS national_location,
    ne.latitude AS national_latitude,
    ne.longitude AS national_longitude,
    ge.event_id AS glob_ev_id,
    ge.start_date AS glob_ev_st_date,
    ge.end_date AS glob_ev_end_date,
    ge.start_time AS glob_ev_st_time,
    ge.end_time AS glob_ev_end_time,
    ge.location AS global_location,
    ge.latitude AS global_latitude,
    ge.longitude AS global_longitude
FROM 
    member m
JOIN 
    chapters c ON m.chapter_id = c.chapter_id
LEFT JOIN 
    events e ON m.chapter_id = e.chapter_id AND DATE(e.start_date) = CURDATE()
LEFT JOIN 
    state_events se ON c.state = se.state AND se.start_date = CURDATE()
LEFT JOIN 
    national_events ne ON c.country = ne.country AND ne.start_date = CURDATE()
LEFT JOIN 
    global_events ge ON ge.start_date = CURDATE()
WHERE 
    m.member_id = ?";

$stmt = $conn->prepare($meetingsQuery);
$stmt->bind_param("s", $member_id); 
$stmt->execute();
$meetingsResult = $stmt->get_result();
$stmt->close();

$meetings = [];
while ($row = $meetingsResult->fetch_assoc()) {
    $meetings[] = $row;
}

// calulate evnet start time
function getEventAttendanceStartTime($eventStartTime) {
    $dateTime = new DateTime($eventStartTime);
    $dateTime->modify('-30 minutes');
    return $dateTime->format('H:i:s');
}


function getAttendanceDetail($memberId, $eventId, $columnName, $conn) {
    $query = "SELECT $columnName FROM attendance WHERE member_id = ? AND event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $memberId, $eventId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row[$columnName];
    } else {
        if ($columnName === 'payment_status') {
            return "Due";
        } elseif ($columnName === 'attendance_status') {
            return "Absent";
        } else {
            return null;
        }
    }
}


$locatonRange = 20000;

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestPayload = file_get_contents("php://input");
    $data = json_decode($requestPayload, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['message' => 'Invalid JSON input']);
        exit;
    }

    $userLat = $data['lat'];
    $userLng = $data['lng'];
    $venueLat = $data['venueLat'];
    $venueLng = $data['venueLng'];
    $memberType = $data['memberType'];
    $slNo = $data['slNo'];

    $distance = calculateDistance($userLat, $userLng, $venueLat, $venueLng);

    $response = [];
    if ($distance <= $locatonRange) { 
        $updateQuery = "
            UPDATE attendance 
            SET time_of_authentication = NOW(), attended_as = ?,
            attendance_status = 'Present' 
            WHERE sl_no = ?
        ";

        if ($stmt = $conn->prepare($updateQuery)) {
            $stmt->bind_param("si", $memberType, $slNo);
            if ($stmt->execute()) {
                $response['message'] = "You are within the venue. Marked as present.";
            } else {
                $response['message'] = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['message'] = "Database error: " . $conn->error;
        }
    } else {
        $response['message'] = "You are not near the venue. Please come near the venue and try again.";
    }

    echo json_encode($response);
    exit;
}
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

   <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script>
       function reMarkAttendance(slNo) {
            if (confirm('Are you sure you want to reset the attendance?')) {
                // Create a new FormData object
                const formData = new FormData();
                formData.append('sl_no', slNo);

                // Use the Fetch API to send the AJAX request
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data.includes('successfully')) {
                        location.reload(); // Refresh the page after successful reset
                    } else {
                        alert('Something went wrong: ' + data);
                    }
                })
                .catch(error => {
                    alert('Something went wrong: ' + error);
                });
            }
        }
    </script>
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
     <div>
        <p id="fail_message">
            You are not near the venue. Please come near the venue and try again.
        </p>
        <p id="message"></p>
    </div>
    <div class="card">
        <div class="card_heading">
            <p class="heading">Todays Meetings</p>
            <br>
        </div>
    <?php if ($memberDetails['chapter_status']==='active' && $memberDetails['member_status'] === 'active') { ?>
<table>
    <thead>
        <tr>
            <th>Meeting Type</th>
            <th>Meeting ID</th>
            <th>Venue</th>
            <th>Attend As</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($meetings)) : ?>
            <?php 
             // Create an array to store unique meeting IDs
            $uniqueMeetings = array(
                'chapter' => array(),
                'state' => array(),
                'national' => array(),
                'global' => array()
            );

//  Chapter Meetings
foreach ($meetings as $meeting) {
    if (!empty($meeting['ch_ev_id']) && !in_array($meeting['ch_ev_id'], $uniqueMeetings['chapter'])) {
        // Retrieve details using the refactored function
        $paymentStatus = getAttendanceDetail($member_id, $meeting['ch_ev_id'], 'payment_status', $conn);
        $attendanceStatus = getAttendanceDetail($member_id, $meeting['ch_ev_id'], 'attendance_status', $conn);
        $chapter_sl_no = getAttendanceDetail($member_id, $meeting['ch_ev_id'], 'sl_no', $conn);
        $chapterAttendaceStartTime=getEventAttendanceStartTime($meeting['ch_ev_st_time']);

        echo "<tr>
                <td>Chapter Meeting</td>
                <td>" . htmlspecialchars($meeting['ch_ev_id']) . "</td>
                <td>" . htmlspecialchars($meeting['chapter_location']) . "</td>
                <td>";
        if ($paymentStatus === 'Due') {
            echo "<p>Please pay fees</p>";
        } else {
            if($attendanceStatus==="Absent"){
                if($chapterAttendaceStartTime<=$currentTime){
                    echo "<button onclick=\"attendAs('Member',  " . htmlspecialchars($chapter_sl_no) . "," . htmlspecialchars($meeting['chapter_latitude']) . ", " . htmlspecialchars($meeting['chapter_longitude']) . ");\">Member</button>
                        <button onclick=\"attendAs('Substitute',  " . htmlspecialchars($chapter_sl_no) . "," . htmlspecialchars($meeting['chapter_latitude']) . ", " . htmlspecialchars($meeting['chapter_longitude']) . ");\">Substitute</button>";
                }else{
                    echo "<p> Attendence System will Start From $chapterAttendaceStartTime";
                }
             } else {
                echo "<p class='success_message'> Attendance marked 
           <button onclick=\"reMarkAttendance(" . htmlspecialchars($chapter_sl_no) . ");\">Re-mark attendance</button>
        </p>";
             }
        }
        echo "</td>
            </tr>";
        $uniqueMeetings['chapter'][] = $meeting['ch_ev_id'];
    }
}
   
// State meeting
   foreach ($meetings as $meeting) {
    if (!empty($meeting['st_ev_id']) && !in_array($meeting['st_ev_id'], $uniqueMeetings['state'])) {
        // Retrieve details using the refactored function
        $paymentStatus = getAttendanceDetail($member_id, $meeting['st_ev_id'], 'payment_status', $conn);
        $attendanceStatus = getAttendanceDetail($member_id, $meeting['st_ev_id'], 'attendance_status', $conn);
        $state_sl_no = getAttendanceDetail($member_id, $meeting['st_ev_id'], 'sl_no', $conn);
         $stateAttendaceStartTime=getEventAttendanceStartTime($meeting['st_ev_st_time']);


        echo "<tr>
                <td>State Meeting</td>
                <td>" . htmlspecialchars($meeting['st_ev_id']) . "</td>
                <td>" . htmlspecialchars($meeting['state_location']) . "</td>
                <td>";
        if ($paymentStatus === 'Due') {
            echo "<p>Please pay fees</p>";
        } else {
            if($attendanceStatus==="Absent"){
                if($stateAttendaceStartTime<=$currentTime){
                    echo "<button onclick=\"attendAs('Member',  " . htmlspecialchars($state_sl_no) . "," . htmlspecialchars($meeting['state_latitude']) . ", " . htmlspecialchars($meeting['state_longitude']) . ");\">Member</button>
                    <button onclick=\"attendAs('Substitute',  " . htmlspecialchars($state_sl_no) . "," . htmlspecialchars($meeting['state_latitude']) . ", " . htmlspecialchars($meeting['state_longitude']) . ");\">Substitute</button>";

                }else{
                    echo "<p> Attendence System will Start From $stateAttendaceStartTime";
                }    
            } else {
                 echo "<p class='success_message'> Attendance marked 
           <button onclick=\"reMarkAttendance(" . htmlspecialchars($state_sl_no) . ");\">Re-mark attendance</button>
        </p>";
            }
        }
        echo "</td>
            </tr>";
        $uniqueMeetings['state'][] = $meeting['st_ev_id'];
    }
}

// Natioanl meeting
   foreach ($meetings as $meeting) {
    if (!empty($meeting['nat_ev_id']) && !in_array($meeting['nat_ev_id'], $uniqueMeetings['national'])) {
        // Retrieve details using the refactored function
        $paymentStatus = getAttendanceDetail($member_id, $meeting['nat_ev_id'], 'payment_status', $conn);
        $attendanceStatus = getAttendanceDetail($member_id, $meeting['nat_ev_id'], 'attendance_status', $conn);
        $nat_sl_no = getAttendanceDetail($member_id, $meeting['nat_ev_id'], 'sl_no', $conn);
         $natAttendaceStartTime=getEventAttendanceStartTime($meeting['nat_ev_st_time']);


        echo "<tr>
                <td>National Meeting</td>
                <td>" . htmlspecialchars($meeting['nat_ev_id']) . "</td>
                <td>" . htmlspecialchars($meeting['national_location']) . "</td>
                <td>";
        if ($paymentStatus === 'Due') {
            echo "<p>Please pay fees</p>";
        } else {
            if($attendanceStatus==="Absent"){
                if($natAttendaceStartTime<=$currentTime){
                    echo "<button onclick=\"attendAs('Member',  " . htmlspecialchars($nat_sl_no) . "," . htmlspecialchars($meeting['national_latitude']) . ", " . htmlspecialchars($meeting['national_longitude']) . ");\">Member</button>
                        <button onclick=\"attendAs('Substitute',  " . htmlspecialchars($nat_sl_no) . "," . htmlspecialchars($meeting['national_latitude']) . ", " . htmlspecialchars($meeting['national_longitude']) . ");\">Substitute</button>";
                }else{
                    echo "<p> Attendence System will Start From $natAttendaceStartTime";
                }
            } else {
                  echo "<p class='success_message'> Attendance marked 
           <button onclick=\"reMarkAttendance(" . htmlspecialchars($nat_sl_no) . ");\">Re-mark attendance</button>
        </p>";
            }
        }
        echo "</td>
            </tr>";
        $uniqueMeetings['national'][] = $meeting['nat_ev_id'];
    }
}


// Global meeting
   foreach ($meetings as $meeting) {
    if (!empty($meeting['glob_ev_id']) && !in_array($meeting['glob_ev_id'], $uniqueMeetings['global'])) {
        // Retrieve details using the refactored function
        $paymentStatus = getAttendanceDetail($member_id, $meeting['glob_ev_id'], 'payment_status', $conn);
        $attendanceStatus = getAttendanceDetail($member_id, $meeting['glob_ev_id'], 'attendance_status', $conn);
        $global_sl_no = getAttendanceDetail($member_id, $meeting['glob_ev_id'], 'sl_no', $conn);
         $globalAttendaceStartTime=getEventAttendanceStartTime($meeting['glob_ev_st_time']);

        echo "<tr>
                <td>Global Meeting</td>
                <td>" . htmlspecialchars($meeting['glob_ev_id']) . "</td>
                <td>" . htmlspecialchars($meeting['global_location']) . "</td>
                <td>";
        if ($paymentStatus === 'Due') {
            echo "<p>Please pay fees</p>";
        } else {
            if($attendanceStatus==="Absent"){
                if($globalAttendaceStartTime<=$currentTime){
                    echo "<button onclick=\"attendAs('Member',  " . htmlspecialchars($global_sl_no) . "," . htmlspecialchars($meeting['global_latitude']) . ", " . htmlspecialchars($meeting['global_longitude']) . ");\">Member</button>
                        <button onclick=\"attendAs('Substitute',  " . htmlspecialchars($global_sl_no) . "," . htmlspecialchars($meeting['global_latitude']) . ", " . htmlspecialchars($meeting['global_longitude']) . ");\">Substitute</button>";
                }else{
                    echo "<p> Attendence System will Start From $globalAttendaceStartTime";
                }
            } else {
                 echo "<p class='success_message'> Attendance marked 
           <button onclick=\"reMarkAttendance(" . htmlspecialchars($global_sl_no) . ");\">Re-mark attendance</button>
        </p>";
             }
        }
        echo "</td>
            </tr>";
        $uniqueMeetings['global'][] = $meeting['glob_ev_id'];
    }
}

         ?>
        <?php else : ?>
            <tr>
                <td colspan="10">No meetings found for this member.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php  } else {
    if($memberDetails['chapter_status']!=='active'){
        echo "<p> Your are chapter is inactive  </p>";
    }
    if($memberDetails['member_status']!=='active'){
        echo "<p> Your are an inactive member </p>";
    }
}?>
    </div>   
 
   <script>
    function attendAs(memberType, slNo, venueLat, venueLng) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                fetch('index.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ lat, lng, venueLat, venueLng, memberType, slNo })
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('message').innerText = data.message;
                    if (data.message.includes("You are not near the venue")) {
                        alert('failed');
                        var failMessageElement = document.getElementById('fail_message');
                        if (failMessageElement) {
                            failMessageElement.style.display = "block";
                        }
                    }
                    else{
                        alert('success');
                        location.reload();
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