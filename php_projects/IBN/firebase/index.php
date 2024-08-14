<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firebase Google Login</title>
</head>
<body>
    <h1>Firebase Google Login</h1>
    <button id="google-login">Login with Google</button>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.5/firebase-app.js";
        import { getAuth, GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/10.12.5/firebase-auth.js";

        const firebaseConfig = {
            apiKey: "AIzaSyBezP8bePI_8ITFq7BWEMAtaCdyLAmhv1w",
            authDomain: "ibnauth.firebaseapp.com",
            projectId: "ibnauth",
            storageBucket: "ibnauth.appspot.com",
            messagingSenderId: "136127356485",
            appId: "1:136127356485:web:582dce30a7a9ad73650d2c",
            measurementId: "G-RM4X057984"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const provider = new GoogleAuthProvider();

        document.getElementById('google-login').addEventListener('click', () => {
            signInWithPopup(auth, provider)
                .then((result) => {
                    // Check if result and result.credential exist
                    if (result.user) {
                        const user = result.user;
                        const credential = GoogleAuthProvider.credentialFromResult(result);

                        if (credential) {
                            const token = credential.accessToken;

                            console.log("Access Token:", token);
                            console.log("User Info:", user);

                            // Redirect to welcome.php with user info
                            const redirectUrl = `./welcome.php?name=${encodeURIComponent(user.displayName)}&photo=${encodeURIComponent(user.photoURL)}&token=${encodeURIComponent(token)}`;
                            window.location.href = redirectUrl;
                        } else {
                            console.error('Credential or access token is undefined');
                        }
                    } else {
                        console.error('User information is undefined');
                    }
                })
                .catch((error) => {
                    console.error("Error during login:", error);
                });
        });
    </script>
</body>
</html>
