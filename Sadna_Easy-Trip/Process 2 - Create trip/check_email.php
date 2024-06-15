<?php
$servername = "localhost";
$username = "zlilma_admin_smy";
$password = "easy_trip123";
$dbname = "zlilma_Easy_Trip";

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$email = $_POST['email'];

$stmt = $conn->prepare("SELECT COUNT(*) FROM User_profile WHERE email = :email");
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->execute();
$count = $stmt->fetchColumn();

echo $count > 0 ? 'exists' : 'not exists';

$conn = null;
?>
