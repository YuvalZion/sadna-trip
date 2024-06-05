<?php
$servername = "localhost";
$username = "zlilma_admin_yz";
$password = "zlilyuval123";
$dbname = "zlilma_test";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$sql = "SELECT email FROM User_profile WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo 'exists';
} else {
    echo 'not exists';
}

$conn->close();
?>
