<?php
session_start();
require_once 'config.php';

try {
    if (isset($_GET['code'])) {
        // Authenticate code from Google OAuth Flow
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

        // Check if token retrieval was successful
        if (isset($token['access_token'])) {
            $client->setAccessToken($token['access_token']);

            // Get profile info
            $google_oauth = new Google_Service_Oauth2($client);
            $google_account_info = $google_oauth->userinfo->get();
            $userinfo = [
                'email' => $google_account_info['email'],
                'first_name' => $google_account_info['givenName'],
                'last_name' => $google_account_info['familyName'],
                'full_name' => $google_account_info['name'],
                'picture' => $google_account_info['picture']
            ];

            // Check if user exists in the member table
            $sql = "SELECT * FROM member WHERE email ='{$userinfo['email']}'";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                // User exists
                $memberinfo = mysqli_fetch_assoc($result);

                // Update name and profile picture if empty
                if (empty($memberinfo['name'])) {
                    $sql = "UPDATE member SET name = '{$userinfo['full_name']}' WHERE email = '{$userinfo['email']}'";
                    if (!mysqli_query($conn, $sql)) {
                        throw new Exception('Error updating name: ' . mysqli_error($conn));
                    }
                }
                if (empty($memberinfo['profile_pic'])) {
                    $sql = "UPDATE member SET profile_pic = '{$userinfo['picture']}' WHERE email = '{$userinfo['email']}'";
                    if (!mysqli_query($conn, $sql)) {
                        throw new Exception('Error updating profile picture: ' . mysqli_error($conn));
                    }
                }

                // Update token in the database
                $sql = "UPDATE member SET token = '{$token['id_token']}' WHERE email = '{$userinfo['email']}'";
                if (!mysqli_query($conn, $sql)) {
                    throw new Exception('Error updating token: ' . mysqli_error($conn));
                }

                // Save user data into session
                $_SESSION['user_token'] = $token['id_token'];

                // Set a persistent cookie (valid for 30 days)
                setcookie('user_token', $token['id_token'], time() + (86400 * 30), "/"); // 86400 = 1 day

                // Redirect based on role
                if ($memberinfo['role'] == 'member') {
                    header("Location: member_dashboard.php");
                    exit();
                } elseif ($memberinfo['role'] == 'admin') {
                    header("Location: admin_panel.php");
                    exit();
                }
            } else {
                // User does not exist in the member table
                echo "Please register first or contact admin to add you.";
                exit();
            }
        } else {
            // Log the error details
            error_log('Error fetching access token: ' . json_encode($token));
            throw new Exception('Failed to retrieve access token.');
        }
    } else {
        if (!isset($_SESSION['user_token'])) {
            header("Location: index.php");
            exit();
        }

        // Check if user exists in the member table
        $sql = "SELECT * FROM member WHERE token ='{$_SESSION['user_token']}'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            // User exists
            $userinfo = mysqli_fetch_assoc($result);
        } else {
            // Invalid session token, redirect to login
            header("Location: index.php");
            exit();
        }
    }
} catch (Exception $e) {
    // Log the error and display a user-friendly message
    error_log($e->getMessage(), 3, 'errors.log');
    echo "An error occurred during authentication. Please try again.";
    exit();
}
?>
