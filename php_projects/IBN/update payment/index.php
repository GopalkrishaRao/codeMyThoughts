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

$eventId = "NAT/IND/0000000001"; // Hardcoded event ID

// Function to get members and their payment status for a specific event
function getMembers($eventId) {
    global $conn;
    $sql = "SELECT m.member_id, m.name, a.payment_status 
            FROM member m 
            LEFT JOIN attendance a ON m.member_id = a.member_id 
            WHERE a.event_id = '$eventId'";
    return $conn->query($sql);
}

// Function to update payment status
function updatePaymentStatus($eventId, $memberId, $currentStatus) {
    global $conn;
    $newStatus = ($currentStatus === 'Due') ? 'Paid' : 'Due';
    $sql = "UPDATE attendance SET payment_status = '$newStatus' WHERE event_id = '$eventId' AND member_id = '$memberId'";
    if ($conn->query($sql) === TRUE) {
        return "Payment status updated to $newStatus for member ID $memberId";
    } else {
        return "Error updating payment status: " . $conn->error;
    }
}

// Handle payment status update request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = $_POST['event_id'];
    $memberId = $_POST['member_id'];
    $currentStatus = $_POST['current_status'];
    $message = updatePaymentStatus($eventId, $memberId, $currentStatus);
    echo "<script>alert('$message');</script>";
}

// Get members for the event
$members = getMembers($eventId);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Event Dashboard</title>
    <script>
        function updatePaymentStatus(eventId, memberId, currentStatus) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';

            const eventIdInput = document.createElement('input');
            eventIdInput.name = 'event_id';
            eventIdInput.value = eventId;

            const memberIdInput = document.createElement('input');
            memberIdInput.name = 'member_id';
            memberIdInput.value = memberId;

            const currentStatusInput = document.createElement('input');
            currentStatusInput.name = 'current_status';
            currentStatusInput.value = currentStatus;

            form.appendChild(eventIdInput);
            form.appendChild(memberIdInput);
            form.appendChild(currentStatusInput);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
</head>
<body>
    <h1>Event Dashboard</h1>
    <h2>Event ID: <?php echo $eventId; ?></h2>
    <?php
    if ($members->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Member ID</th><th>Name</th><th>Payment Status</th><th>Action</th></tr>";

        while ($row = $members->fetch_assoc()) {
            $memberId = $row['member_id'];
            $name = $row['name'];
            $paymentStatus = $row['payment_status'];

            echo "<tr>";
            echo "<td>$memberId</td>";
            echo "<td>$name</td>";
            echo "<td>$paymentStatus</td>";
            echo "<td><button onclick=\"updatePaymentStatus('$eventId', '$memberId', '$paymentStatus')\">" . ($paymentStatus === 'Due' ? 'Mark as Paid' : 'Mark as Due') . "</button></td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "No members found.";
    }
    $conn->close();
    ?>
</body>
</html>
