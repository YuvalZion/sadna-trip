<?php
// Database connection settings
$servername = "localhost";
$username = "zlilma_admin_smy";
$password = "easy_trip123";
$dbname = "zlilma_Easy_Trip";

// Create and Check connection
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get email from POST data
$email = $_POST['email'];

// Prepare SQL statement to count rows with given email
$stmt = $conn->prepare("SELECT COUNT(*) FROM User_profile WHERE email = :email");
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->execute();
$count = $stmt->fetchColumn();

// Output 'exists' if email exists, otherwise 'not exists'
echo $count > 0 ? 'exists' : 'not exists';

$conn = null;
?>