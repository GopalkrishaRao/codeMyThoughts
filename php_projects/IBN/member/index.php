<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Creation Form</title>
</head>
<body>
    <h1>Member Creation Form</h1>
    <form id="memberForm" action="member_creation.php" method="POST" onsubmit="return confirmSubmission()">

      <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>
        
        <label for="chapter">Chapter:</label>
        <select id="chapter" name="chapter" onchange="populateChapterName()" required>
            <option value="" disabled selected>Select a chapter</option>
        </select>
        <span id="chapterName"></span><br><br>

        <label for="email">Gmail id:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="business_type">Business Type:</label>
        <textarea id="business_type" name="business_type" required></textarea><br><br>

        <label for="industry">Industry:</label>
        <textarea id="industry" name="industry" required></textarea><br><br>

        <label for="sector">Sector:</label>
        <textarea id="sector" name="sector" required></textarea><br><br>

        <label for="role">Role:</label>
        <!-- <input type="radio" id="super_admin" name="role" value="super_admin" required>
        <label for="super_admin">Super Admin</label> -->
        <input type="radio" id="admin" name="role" value="admin">
        <label for="admin">Admin</label>
        <input type="radio" id="member" name="role" value="member" checked>
        <label for="member">Member</label><br><br>

        <input type="submit" value="Submit">
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch('fetch_chapters.php')
                .then(response => response.json())
                .then(data => {
                    const chapterSelect = document.getElementById('chapter');
                    for (const chapter of data) {
                        const option = document.createElement('option');
                        option.value = chapter.chapter_id;
                        option.textContent = chapter.chapter_id;
                        chapterSelect.appendChild(option);
                    }
                });
        });

        function populateChapterName() {
            const chapterSelect = document.getElementById('chapter');
            const chapterNameSpan = document.getElementById('chapterName');
            const selectedChapterId = chapterSelect.value;

            fetch('fetch_chapters.php')
                .then(response => response.json())
                .then(data => {
                    for (const chapter of data) {
                        if (chapter.chapter_id === selectedChapterId) {
                            chapterNameSpan.textContent = chapter.chapter_name;
                            break;
                        }
                    }
                });
        }

        function confirmSubmission() {
            const chapter = document.getElementById('chapter').value;
            const email = document.getElementById('email').value;
            const business_type = document.getElementById('business_type').value;
            const industry = document.getElementById('industry').value;
            const sector = document.getElementById('sector').value;
            const role = document.querySelector('input[name="role"]:checked').value;

            return confirm(`Do you want to create this member with the email: ${email} in chapter: ${chapter}?`);
        }
    </script>
</body>
</html>
