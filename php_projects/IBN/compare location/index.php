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

// Placeholder user ID (in a real application, this would come from session or login system)
$userId = "AFG/BDG/0001/002";

// Range in meters. It will be selected by Admin/super_admin as per venue. Default is 10m
$locationRange = 20;

// Fetch user details
$userQuery = "SELECT member_id, email, name, profile_pic, chapter_id FROM member WHERE member_id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("s", $userId);
$stmt->execute();
$userResult = $stmt->get_result()->fetch_assoc();
$stmt->close();

// EVENT DETAILS
$eventDetailsQuery = "
    SELECT 
        e.event_type,
        e.start_date,
        e.end_date,
        e.start_time,
        e.end_time
    FROM 
        attendance a
    JOIN 
        events e ON a.event_id = e.event_id
    WHERE 
        a.member_id = ?
";

// Prepare and execute the query
$stmt = $conn->prepare($eventDetailsQuery);
$stmt->bind_param("s", $userId);
$stmt->execute();
$eventDetailsResult = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Extract event details
$eventType = $eventDetailsResult['event_type'] ?? 'N/A';
$startDate = $eventDetailsResult['start_date'] ?? 'N/A';
$endDate = $eventDetailsResult['end_date'] ?? 'N/A';
$startTime = $eventDetailsResult['start_time'] ?? 'N/A';
$endTime = $eventDetailsResult['end_time'] ?? 'N/A';

// Output or use the event details as needed
echo "Event Type: " . htmlspecialchars($eventType) . "<br>";
echo "Start Date: " . htmlspecialchars($startDate) . "<br>";
echo "End Date: " . htmlspecialchars($endDate) . "<br>";
echo "Start Time: " . htmlspecialchars($startTime) . "<br>";
echo "End Time: " . htmlspecialchars($endTime) . "<br>";

// Fetch chapter name
if ($userResult) {
    $chapterId = $userResult['chapter_id'];

    $chapterQuery = "SELECT chapter_name FROM chapters WHERE chapter_id = ?";
    $stmt = $conn->prepare($chapterQuery);
    $stmt->bind_param("s", $chapterId);
    $stmt->execute();
    $chapterResult = $stmt->get_result()->fetch_assoc();
    $stmt->close();
} else {
    die("User not found");
}

// Fetch payment status and latest sl_no
$paymentQuery = "SELECT payment_status, sl_no FROM attendance WHERE member_id=? ORDER BY time_of_authentication DESC LIMIT 1";
$stmt = $conn->prepare($paymentQuery);
$stmt->bind_param("s", $userId);
$stmt->execute();
$paymentResult = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch today's events
$today = date('Y-m-d');
$eventsTodayQuery = "
    SELECT 
        e.event_name
    FROM 
        attendance a
    JOIN 
        events e ON a.event_id = e.event_id
    WHERE 
        a.member_id = ? AND e.start_date = ?
";

// Prepare and execute the query
$stmt = $conn->prepare($eventsTodayQuery);
$stmt->bind_param("ss", $userId, $today);
$stmt->execute();
$eventsTodayResult = $stmt->get_result();
$eventsToday = [];
while ($row = $eventsTodayResult->fetch_assoc()) {
    $eventsToday[] = htmlspecialchars($row['event_name']);
}
$stmt->close();

// Handle POST request for attendance
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');

    $requestPayload = file_get_contents("php://input");
    $data = json_decode($requestPayload, true);

    if (!$data) {
        echo json_encode(['message' => 'Invalid request payload.']);
        exit;
    }

    $userLat = $data['lat'];
    $userLng = $data['lng'];
    $venueLat = $data['venueLat'];
    $venueLng = $data['venueLng'];
    $memberType = $data['memberType'];

    if ($paymentResult['payment_status'] === 'Due') {
        echo json_encode(['message' => 'Payment due. Please pay and contact your admin.']);
        exit;
    }

    // Calculate the distance between the user and the venue
    $distance = calculateDistance($userLat, $userLng, $venueLat, $venueLng);

    if ($distance <= $locationRange) {
        // Update attendance record
        $currentDateTime = date('Y-m-d H:i:s');
        $attendanceQuery = "UPDATE attendance SET time_of_authentication = ?, attended_as = ? WHERE sl_no = ?";
        $stmt = $conn->prepare($attendanceQuery);
        $stmt->bind_param("ssi", $currentDateTime, $memberType, $paymentResult['sl_no']);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        if ($affectedRows > 0) {
            $message = "Attendance updated. Attending as " . ($memberType === 'member' ? "member." : "substitute.");
        } else {
            $message = "Attendance record not found or already updated.";
        }
        
        echo json_encode(['message' => $message]);
    } else {
        echo json_encode(['message' => 'You are not near the venue. Please come near the venue and try again.']);
    }
    exit;
}

// Function to calculate distance
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPS Attendance System</title>
</head>
<body>
    <h1>GPS Attendance System</h1>

    <!-- User Card -->
    <div id="userCard">
        <img src="<?php echo htmlspecialchars($userResult['profile_pic']); ?>" alt="Profile Picture" width="100">
        <p>Name: <?php echo htmlspecialchars($userResult['name']); ?></p>
        <p>Email: <?php echo htmlspecialchars($userResult['email']); ?></p>
        <p>Member ID: <?php echo htmlspecialchars($userResult['member_id']); ?></p>
        <p>Chapter Name: <?php echo htmlspecialchars($chapterResult['chapter_name']); ?></p>
    </div>

    <!-- Event Details -->
    <div id="eventDetails">
        <h2>Event Details</h2>
        <p>Date: <?php echo htmlspecialchars($eventResult['start_date']); ?></p>
        <p>Time: <?php echo htmlspecialchars($eventResult['start_time']); ?></p>
        <p>Venue: <?php echo htmlspecialchars($eventResult['venue']); ?></p>
        <p>Meeting Type: <?php echo htmlspecialchars($eventResult['meeting_type']); ?></p>
        <p>Payment Status: <?php echo htmlspecialchars($paymentResult['payment_status']); ?></p>
    </div>

    <!-- Today's Events -->
    <div id="eventsToday">
        <h2>Today's Events</h2>
        <?php if (!empty($eventsToday)): ?>
            <ul>
                <?php foreach ($eventsToday as $eventName): ?>
                    <li><?php echo htmlspecialchars($eventName); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No events scheduled for today.</p>
        <?php endif; ?>
    </div>

    <!-- Login Buttons -->
    <?php
    if ($paymentResult['payment_status'] === "Due") {
        echo "<div>Please pay Entry Fee and contact admin</div>";
    } else {
        echo ('<div id="loginButtons">
            <h2>Attend As</h2>
            <button onclick="loginAs(\'member\')">Member</button>
            <button onclick="loginAs(\'substitute\')">Substitute</button>
        </div>');
    }
    ?>
   
    <p id="message"></p>

    <script>
        function loginAs(memberType) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    // Add your venue latitude and longitude here
                    const venueLat = 13.0041514;  // Example venue latitude
                    const venueLng = 77.5522484;  // Example venue longitude

                    fetch('index.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ lat, lng, venueLat, venueLng, memberType })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
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

<?php
$conn->close();
?>
