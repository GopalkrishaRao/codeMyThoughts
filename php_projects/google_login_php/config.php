<?php
require_once 'vendor/autoload.php';

// Include the autoloader if not already included
require __DIR__ . '/vendor/autoload.php';

// Load the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Retrieve the values from the .env file
$clientID = $_ENV['GOOGLE_CLIENT_ID'];
$clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'];

$redirectUri = 'http://localhost:8888/welcome.php';

// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

// Connect to database
$hostname = "127.0.0.1";
$username = "root";
$password = "";
$database = "02_PhpGoogleLogin";

$conn = mysqli_connect($hostname, $username, $password, $database);