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

// Function to generate a unique event ID
function generateUniqueEventId($eventType, $chapterId = null) {
    global $conn;

    if ($eventType === 'chapter') {
        $chapterId = $conn->real_escape_string($chapterId);

        $sql = "SELECT numb_id FROM events WHERE chapter_id = '$chapterId' ORDER BY numb_id DESC LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastNumber = $row['numb_id'];
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $formattedNumber = str_pad($newNumber, 10, '0', STR_PAD_LEFT);
        $fullEventId = "$chapterId/$formattedNumber";
    } else {
        $sql = "SELECT numb_id FROM events WHERE event_id LIKE 'NAT/IND/%' ORDER BY numb_id DESC LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastNumber = $row['numb_id'];
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $formattedNumber = str_pad($newNumber, 10, '0', STR_PAD_LEFT);
        $fullEventId = "NAT/IND/$formattedNumber";
    }

    return [$newNumber, $fullEventId];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eventType = $_POST['event_type'];
    $chapterId = $eventType === 'chapter' ? $_POST['chapter'] : null;
    $mode = $conn->real_escape_string($_POST['mode']);
    $meetingType = $conn->real_escape_string($_POST['meeting_type']);
    $agenda = $conn->real_escape_string($_POST['agenda']);
    $description = $conn->real_escape_string($_POST['description']);
    $location = $conn->real_escape_string($_POST['location']);
    $startDate = $conn->real_escape_string($_POST['start_date']);
    $endDate = $conn->real_escape_string($_POST['end_date']);
    $startTime = $conn->real_escape_string($_POST['start_time']);
    $endTime = $conn->real_escape_string($_POST['end_time']);
    $venue = $conn->real_escape_string($_POST['venue']);
    $scheduled_by = $conn->real_escape_string($_POST['scheduled_by']); // Get from form input

    list($numbId, $eventId) = generateUniqueEventId($eventType, $chapterId);

    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare("INSERT INTO events (chapter_id, event_id, meeting_type, agenda, description, location, start_date, end_date, start_time, end_time, venue, scheduled_by, numb_id, mode, event_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === FALSE) {
        die("Error preparing statement: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("sssssssssssssss", $chapterId, $eventId, $meetingType, $agenda, $description, $location, $startDate, $endDate, $startTime, $endTime, $venue, $scheduled_by, $numbId, $mode, $eventType);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New event created successfully with Unique Event ID: $eventId";




        // Newly add code
         if ($eventType === 'national') {
        // National event: select all members
            $sql = "SELECT member_id FROM member";
        } else {
        // Chapter event: select members of the specified chapter
            $chapterId = $conn->real_escape_string($chapterId);
            $sql = "SELECT member_id FROM member WHERE chapter_id = '$chapterId'";
        }
        $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Prepare the statement to insert into attendance table
        $attendanceStmt = $conn->prepare("INSERT INTO attendance (event_id, member_id, payment_status) VALUES (?, ?, 'Due')");
        if ($attendanceStmt === FALSE) {
            die("Error preparing attendance statement: " . $conn->error);
        }

        // Bind parameters
        while ($row = $result->fetch_assoc()) {
            $memberId = $row['member_id'];
            $attendanceStmt->bind_param("ss", $eventId, $memberId);
            if (!$attendanceStmt->execute()) {
                die("Error executing attendance statement: " . $attendanceStmt->error);
            }
        }

        $attendanceStmt->close();
    }
// end of new querry



    } else {
        die("Error executing statement: " . $stmt->error);
    }

    $stmt->close();
}

// Data fetching logic
if (isset($_GET['fetch'])) {
    if ($_GET['fetch'] == 'data') {
        if (isset($_GET['country']) && isset($_GET['state'])) {
            // Fetch cities based on country and state
            $country = $_GET['country'];
            $state = $_GET['state'];
            $sql = "SELECT DISTINCT city FROM chapters WHERE country = '$country' AND state = '$state'";
            $result = $conn->query($sql);

            $cities = [];
            while ($row = $result->fetch_assoc()) {
                $cities[] = $row['city'];
            }
            echo json_encode($cities);

        } elseif (isset($_GET['country'])) {
            // Fetch states based on country
            $country = $_GET['country'];
            $sql = "SELECT DISTINCT state FROM chapters WHERE country = '$country'";
            $result = $conn->query($sql);

            $states = [];
            while ($row = $result->fetch_assoc()) {
                $states[] = $row['state'];
            }
            echo json_encode($states);

        } else {
            // Fetch unique list of countries
            $sql = "SELECT DISTINCT country FROM chapters";
            $result = $conn->query($sql);

            $countries = [];
            while ($row = $result->fetch_assoc()) {
                $countries[] = $row['country'];
            }
            echo json_encode($countries);
        }
    } elseif ($_GET['fetch'] == 'chapters') {
        if (isset($_GET['country']) && isset($_GET['state']) && isset($_GET['city'])) {
            $country = $_GET['country'];
            $state = $_GET['state'];
            $city = $_GET['city'];
            $sql = "SELECT chapter_id, chapter_name FROM chapters WHERE country = '$country' AND state = '$state' AND city = '$city'";
            $result = $conn->query($sql);

            $chapters = [];
            while ($row = $result->fetch_assoc()) {
                $chapters[] = $row;
            }
            echo json_encode($chapters);
        } elseif (isset($_GET['chapter_id'])) {
            $chapter_id = $_GET['chapter_id'];
            $sql = "SELECT chapter_name FROM chapters WHERE chapter_id = '$chapter_id'";
            $result = $conn->query($sql);

            $chapter = $result->fetch_assoc();
            echo json_encode($chapter);
        }
    }
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Creation Form</title>
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <h1>Event Creation Form</h1>
    <form id="eventForm" action="" method="POST" onsubmit="return confirmSubmission()">
        <label for="event_type">Event Type:</label>
        <select id="event_type" name="event_type" onchange="toggleEventFields()" required>
            <option value="" disabled selected>Select event type</option>
            <option value="chapter">Chapter Event</option>
            <option value="national">National Event</option>
        </select><br><br>

        <div id="chapterFields" class="hidden">
            <label for="country">Country:</label>
            <select id="country" name="country" onchange="populateStates()">
                <option value="" disabled selected>Select a country</option>
            </select><br><br>

            <label for="state">State:</label>
            <select id="state" name="state" onchange="populateCities()">
                <option value="" disabled selected>Select a state</option>
            </select><br><br>

            <label for="city">City:</label>
            <select id="city" name="city" onchange="populateChapters()">
                <option value="" disabled selected>Select a city</option>
            </select><br><br>

            <label for="chapter">Chapter:</label>
            <select id="chapter" name="chapter" onchange="populateChapterName()">
                <option value="" disabled selected>Select a chapter</option>
            </select>
            <span id="chapterName"></span><br><br>
        </div>

        <label for="mode">Event Mode:</label>
        <select id="mode" name="mode" required>
            <option value="" disabled selected>Select mode</option>
            <option value="online">Online</option>
            <option value="offline">Offline</option>
        </select><br><br>

        <label for="meeting_type">Meeting Type:</label>
        <select id="meeting_type" name="meeting_type" required>
            <option value="" disabled selected>Select meeting type</option>
            <option value="business">Business Meeting</option>
            <option value="casual">Casual Meeting</option>
            <option value="social">Social Meeting</option>
        </select><br><br>

        <label for="agenda">Agenda:</label>
        <textarea id="agenda" name="agenda" required></textarea><br><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea><br><br>

        <label for="location">Location:</label>
        <input type="text" id="location" name="location" required><br><br>

        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" required><br><br>

        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" required><br><br>

        <label for="start_time">Start Time:</label>
        <input type="time" id="start_time" name="start_time" required><br><br>

        <label for="end_time">End Time:</label>
        <input type="time" id="end_time" name="end_time" required><br><br>

        <label for="venue">Venue:</label>
        <input type="text" id="venue" name="venue"><br><br>

        <input type="hidden" name="scheduled_by" value="super_admin"> <!-- Replace with actual user ID -->

        <button type="submit">Create Event</button>
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            populateCountries();
        });

        function toggleEventFields() {
            const eventType = document.getElementById('event_type').value;
            const chapterFields = document.getElementById('chapterFields');
            if (eventType === 'chapter') {
                chapterFields.classList.remove('hidden');
            } else {
                chapterFields.classList.add('hidden');
            }
        }

        function populateCountries() {
            fetch('index.php?fetch=data')
                .then(response => response.json())
                .then(data => {
                    const countrySelect = document.getElementById('country');
                    countrySelect.innerHTML = '<option value="" disabled selected>Select a country</option>';
                    data.forEach(country => {
                        countrySelect.innerHTML += `<option value="${country}">${country}</option>`;
                    });
                });
        }

        function populateStates() {
            const country = document.getElementById('country').value;
            fetch(`index.php?fetch=data&country=${country}`)
                .then(response => response.json())
                .then(data => {
                    const stateSelect = document.getElementById('state');
                    stateSelect.innerHTML = '<option value="" disabled selected>Select a state</option>';
                    data.forEach(state => {
                        stateSelect.innerHTML += `<option value="${state}">${state}</option>`;
                    });
                });
        }

        function populateCities() {
            const country = document.getElementById('country').value;
            const state = document.getElementById('state').value;
            fetch(`index.php?fetch=data&country=${country}&state=${state}`)
                .then(response => response.json())
                .then(data => {
                    const citySelect = document.getElementById('city');
                    citySelect.innerHTML = '<option value="" disabled selected>Select a city</option>';
                    data.forEach(city => {
                        citySelect.innerHTML += `<option value="${city}">${city}</option>`;
                    });
                });
        }

        function populateChapters() {
            const country = document.getElementById('country').value;
            const state = document.getElementById('state').value;
            const city = document.getElementById('city').value;
            fetch(`index.php?fetch=chapters&country=${country}&state=${state}&city=${city}`)
                .then(response => response.json())
                .then(data => {
                    const chapterSelect = document.getElementById('chapter');
                    chapterSelect.innerHTML = '<option value="" disabled selected>Select a chapter</option>';
                    data.forEach(chapter => {
                        chapterSelect.innerHTML += `<option value="${chapter.chapter_id}">${chapter.chapter_name}</option>`;
                    });
                });
        }

        function populateChapterName() {
            const chapterId = document.getElementById('chapter').value;
            fetch(`index.php?fetch=chapters&chapter_id=${chapterId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('chapterName').innerText = `Chapter Name: ${data.chapter_name}`;
                });
        }

        function confirmSubmission() {
            return confirm('Are you sure you want to submit the form?');
        }
    </script>
</body>
</html>
