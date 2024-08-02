<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "CMT_IBN";
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
        <textarea id="location" name="location" required></textarea><br><br>

        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" required><br><br>

        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" required><br><br>

        <label for="start_time">Start Time:</label>
        <input type="time" id="start_time" name="start_time" required><br><br>

        <label for="end_time">End Time:</label>
        <input type="time" id="end_time" name="end_time" required><br><br>

        <label for="venue">Venue:</label>
        <input type="text" id="venue" name="venue" required><br><br>

        <input type="hidden" id="scheduled_by" name="scheduled_by" value="super admin">

        <input type="submit" value="Submit">
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch('fetch_data.php')
                .then(response => response.json())
                .then(data => {
                    const countrySelect = document.getElementById('country');
                    for (const country of data) {
                        const option = document.createElement('option');
                        option.value = country;
                        option.textContent = country;
                        countrySelect.appendChild(option);
                    }
                });
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

        function populateStates() {
            const country = document.getElementById('country').value;
            fetch(`fetch_data.php?country=${country}`)
                .then(response => response.json())
                .then(data => {
                    const stateSelect = document.getElementById('state');
                    stateSelect.innerHTML = '<option value="" disabled selected>Select a state</option>';
                    for (const state of data) {
                        const option = document.createElement('option');
                        option.value = state;
                        option.textContent = state;
                        stateSelect.appendChild(option);
                    }
                    document.getElementById('city').innerHTML = '<option value="" disabled selected>Select a city</option>';
                    document.getElementById('chapter').innerHTML = '<option value="" disabled selected>Select a chapter</option>';
                });
        }

        function populateCities() {
            const country = document.getElementById('country').value;
            const state = document.getElementById('state').value;
            fetch(`fetch_data.php?country=${country}&state=${state}`)
                .then(response => response.json())
                .then(data => {
                    const citySelect = document.getElementById('city');
                    citySelect.innerHTML = '<option value="" disabled selected>Select a city</option>';
                    for (const city of data) {
                        const option = document.createElement('option');
                        option.value = city;
                        option.textContent = city;
                        citySelect.appendChild(option);
                    }
                    document.getElementById('chapter').innerHTML = '<option value="" disabled selected>Select a chapter</option>';
                });
        }

        function populateChapters() {
            const country = document.getElementById('country').value;
            const state = document.getElementById('state').value;
            const city = document.getElementById('city').value;
            fetch(`fetch_chapters.php?country=${country}&state=${state}&city=${city}`)
                .then(response => response.json())
                .then(data => {
                    const chapterSelect = document.getElementById('chapter');
                    chapterSelect.innerHTML = '<option value="" disabled selected>Select a chapter</option>';
                    for (const chapter of data) {
                        const option = document.createElement('option');
                        option.value = chapter.chapter_id;
                        option.textContent = chapter.chapter_id;
                        chapterSelect.appendChild(option);
                    }
                });
        }

        function populateChapterName() {
            const chapterSelect = document.getElementById('chapter');
            const chapterNameSpan = document.getElementById('chapterName');
            const selectedChapterId = chapterSelect.value;

            fetch('fetch_chapters.php?chapter_id=' + selectedChapterId)
                .then(response => response.json())
                .then(data => {
                    chapterNameSpan.textContent = data.chapter_name;
                });
        }

        function confirmSubmission() {
            const eventType = document.getElementById('event_type').value;
            const mode = document.getElementById('mode').value;
            const meeting_type = document.getElementById('meeting_type').value;
            const agenda = document.getElementById('agenda').value;
            const description = document.getElementById('description').value;
            const location = document.getElementById('location').value;
            const start_date = document.getElementById('start_date').value;
            const end_date = document.getElementById('end_date').value;
            const start_time = document.getElementById('start_time').value;
            const end_time = document.getElementById('end_time').value;
            const venue = document.getElementById('venue').value;

            if (eventType === 'chapter') {
                const chapter = document.getElementById('chapter').value;
                return confirm(`Do you want to create this chapter event with the chapter: ${chapter}?`);
            } else {
                return confirm(`Do you want to create this national event?`);
            }
        }
    </script>
</body>
</html>
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
// var_dump($scheduled_by, $eventType);

    // Prepare the SQL statement with placeholders
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
    } else {
        die("Error executing statement: " . $stmt->error);
    }

    $stmt->close();
}

$conn->close();
?>
