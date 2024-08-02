<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chapter Creation Form</title>
</head>
<body>
    <h1>Chapter Creation Form</h1>
    <form id="chapterForm" action="chapter_creation.php" method="POST" onsubmit="return confirmSubmission()">
        <label for="country">Country:</label>
        <select id="country" name="country" onchange="populateStates()" required>
            <option value="" disabled selected>Select a country</option>
        </select>
        <span id="countryCode"></span><br><br>

        <label for="state">State:</label>
        <select id="state" name="state" onchange="populateCities()" required>
            <option value="" disabled selected>Select a state</option>
        </select>
        <span id="stateCode"></span><br><br>

        <label for="city">City:</label>
        <select id="city" name="city" required>
            <option value="" disabled selected>Select a city</option>
        </select>
        <br><br>

        <label for="city_tier">City Tier:</label>
        <input type="radio" id="T1" name="city_tier" value="T1" required>
        <label for="T1">T1</label>
        <input type="radio" id="T2" name="city_tier" value="T2">
        <label for="T2">T2</label>
        <input type="radio" id="T3" name="city_tier" value="T3">
        <label for="T3">T3</label><br><br>

        <label for="area">Area:</label>
        <input type="text" id="area" name="area" required><br><br>

        <label for="admin">Admin:</label>
        <input type="text" id="admin" name="admin" required><br><br>

        <label for="chapter_name">Chapter Name:</label>
        <input type="text" id="chapter_name" name="chapter_name" required><br><br>
        
        <input type="submit" value="Submit">
    </form>

    <script>
        let countries;

        fetch('data.json')
            .then(response => response.json())
            .then(data => {
                countries = data;
                const countrySelect = document.getElementById('country');
                for (const country of countries) {
                    const option = document.createElement('option');
                    option.value = country.iso3;
                    option.textContent = country.name;
                    countrySelect.appendChild(option);
                }
            });

        function populateStates() {
            const countrySelect = document.getElementById('country');
            const stateSelect = document.getElementById('state');
            const country = countrySelect.value;

            stateSelect.innerHTML = '<option value="" disabled selected>Select a state</option>';
            for (const countryData of countries) {
                if (countryData.iso3 === country) {
                    for (const state of countryData.states) {
                        const option = document.createElement('option');
                        option.value = state.state_code;
                        option.textContent = state.state_name;
                        stateSelect.appendChild(option);
                    }
                }
            }
            populateCities(); // Reset city dropdown when country changes
        }

        function populateCities() {
            const countrySelect = document.getElementById('country');
            const stateSelect = document.getElementById('state');
            const citySelect = document.getElementById('city');
            const country = countrySelect.value;
            const state = stateSelect.value;

            citySelect.innerHTML = '<option value="" disabled selected>Select a city</option>';
            for (const countryData of countries) {
                if (countryData.iso3 === country) {
                    for (const stateData of countryData.states) {
                        if (stateData.state_code === state) {
                            for (const city of stateData.cities) {
                                const option = document.createElement('option');
                                option.value = city;
                                option.textContent = city;
                                citySelect.appendChild(option);
                            }
                        }
                    }
                }
            }
        }

        function confirmSubmission() {
            const country = document.getElementById('country').value;
            const state = document.getElementById('state').value;
            const city = document.getElementById('city').value;
            const chapter_id = `${country}/${state}/XXXX`; // Placeholder for unique code generation

            return confirm(`Do you want to create this chapter with the key: ${chapter_id} in ${city}?`);
        }
    </script>
</body>
</html>
