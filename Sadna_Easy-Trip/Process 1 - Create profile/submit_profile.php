<?php
// Database connection details
$servername = "localhost";
$username = "zlilma_admin_smy";
$password = "easy_trip123";
$dbname = "zlilma_Easy_Trip";

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    // If there is a connection error, terminate the script and display the error message
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data from POST request
$email = $_POST['email'];
$user_name = $_POST['fullname'];
$phone = $_POST['phone'];
$passport_expiration_date = $_POST['passport_exp'];
$date_birth = $_POST['dob'];
$user_password = $_POST['password'];

// Prepare an SQL statement to insert the form data into the User_profile table
$sql = "INSERT INTO User_profile (email, user_name, phone, passport_expiration_date, date_birth, user_password) 
        VALUES ('$email', '$user_name', '$phone', '$passport_expiration_date', '$date_birth', '$user_password')";

// Execute the SQL statement and check if the insertion was successful
if ($conn->query($sql) === TRUE) {
    // If insertion was successful, redirect the user to the user_img.php page with the email as a parameter
    header("Location: user_img.php?email=$email");
    exit();
} else {
    // If there was an error during insertion, display the error message
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the database connection
$conn->close();
?>