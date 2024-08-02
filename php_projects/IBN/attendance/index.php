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
$locatonRange = 200;

// GPS Distance Calculation Function
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371000; // Radius of the earth in meters

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
            SET time_of_authentication = NOW(), attended_as = ? 
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

// Placeholder user ID (in a real application, this would come from session or login system)
$userId = "AFG/BDG/0001/002";

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
$chapterStatusStmt->bind_param("s", $userId);
$chapterStatusStmt->execute();
$chapterStatusResult = $chapterStatusStmt->get_result();
$chapterStatus = $chapterStatusResult->fetch_assoc()['chapter_status'];
$chapterStatusStmt->close();

if ($chapterStatus !== 'active') {
    die("Your chapter is inactive. Please contact your admin.");
}

// Check member status
$memberStatusQuery = "
    SELECT member_status 
    FROM member 
    WHERE member_id = ?
";

$memberStatusStmt = $conn->prepare($memberStatusQuery);
$memberStatusStmt->bind_param("s", $userId);
$memberStatusStmt->execute();
$memberStatusResult = $memberStatusStmt->get_result();
$memberStatus = $memberStatusResult->fetch_assoc()['member_status'];
$memberStatusStmt->close();

if ($memberStatus !== 'active') {
    die("You are an inactive member. Please contact your admin to make it active.");
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
        a.sl_no
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
$stmt->bind_param("ss", $userId, $todaysDate);
$stmt->execute();
$meetingsResult = $stmt->get_result();
$stmt->close();

$meetings = [];
while ($row = $meetingsResult->fetch_assoc()) {
    // Calculate attendance start time by subtracting 30 minutes from the meeting start time
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Meetings</title>
</head>
<body>
    <h1>Today's Meetings</h1>

   <!-- Today's Meetings -->
<div id="todayMeetings">
    <h2>Meetings Scheduled for Today</h2>

    <?php if (!empty($meetings)) { ?>
        <ul>
            <?php foreach ($meetings as $meeting) { ?>


<!-- showing and hiding meeting based on end time added if statemet -->
                <?php if ($currentTime <= $meeting['end_time']) { // Check if the current time is less than or equal to the meeting end time ?>
                    <li>
                        <img src="<?php echo htmlspecialchars($meeting['profile_pic']); ?>" alt="Profile Picture" width="100"><br>
                        <strong>Name:</strong> <?php echo htmlspecialchars($meeting['name']); ?><br>
                        <strong>Member ID:</strong> <?php echo htmlspecialchars($meeting['member_id']); ?><br>
                        <strong>Chapter Name:</strong> <?php echo htmlspecialchars($meeting['chapter_name']); ?><br>
                        <strong>Event Type:</strong> <?php echo htmlspecialchars($meeting['event_type']); ?><br>
                        <strong>Meeting Type:</strong> <?php echo htmlspecialchars($meeting['meeting_type']); ?><br>
                        <strong>Date:</strong> <?php echo htmlspecialchars($meeting['start_date']); ?><br>
                        <strong>Start Time:</strong> <?php echo htmlspecialchars($meeting['start_time']); ?><br>
                        <strong>End Time:</strong> <?php echo htmlspecialchars($meeting['end_time']); ?><br>
                        <strong>Venue:</strong> <?php echo htmlspecialchars($meeting['venue']); ?><br>
                        <strong>Payment Status:</strong> <?php echo htmlspecialchars($meeting['payment_status']); ?><br>
                        <?php if ($meeting['payment_status'] === 'Due') { ?>
                            <p>Please pay fees and contact your admin to attend.</p>
                        <?php } else { ?>



                     <!-- if statement for shoing button before half an hour -->
                            <?php
                            $attendanceStartTime = $meeting['attendance_start_time'];
                            ?>
                            <?php if ($attendanceStartTime <= $currentTime) { ?>
                                <h2>Attend as</h2>
                                <button onclick="attendAs('member', '<?php echo $meeting['sl_no']; ?>', <?php echo $meeting['latitude']; ?>, <?php echo $meeting['longitude']; ?>)">Member</button>
                                <button onclick="attendAs('substitute', '<?php echo $meeting['sl_no']; ?>', <?php echo $meeting['latitude']; ?>, <?php echo $meeting['longitude']; ?>)">Substitute</button>
                            <?php } else { ?>
                                <h2>Attendance system will start from <?php echo htmlspecialchars($attendanceStartTime); ?></h2>
                            <?php } ?>

                                <!-- end of if -->


                        <?php } ?>
                    </li>
                <?php } ?>
            <?php } ?> 

        </ul>
    <?php } else { ?>
        <p>No meetings scheduled for today.</p>
    <?php } ?>
</div>

    <p id="message"></p>

    <script>
        function attendAs(memberType, slNo, venueLat, venueLng) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    fetch('index.php', { // Replace 'your_php_file.php' with the actual filename
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ lat, lng, venueLat, venueLng, memberType, slNo })
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('message').innerText = data.message;
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
