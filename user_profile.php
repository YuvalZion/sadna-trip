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
$user_name = $_POST['fullname'];
$phone = $_POST['phone'];
$passport_expiration_date = $_POST['passport_exp'];
$date_birth = $_POST['dob'];
$user_password = $_POST['password'];

// Server-side validations
if (strtotime($date_birth) >= time()) {
    die("Invalid Date of Birth. It must be before today.");
}

if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/', $user_password)) {
    die("Invalid Password. It must contain at least 6 characters, including one letter and one number.");
}

$sql = "SELECT email FROM User_profile WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '<script>alert("The email already exists in the system. Please enter another email."); window.history.back();</script>';
    $conn->close();
    exit();
}

$sql = "INSERT INTO User_profile (email, user_name, phone, passport_expiration_date, date_birth, user_password) 
        VALUES ('$email', '$user_name', '$phone', '$passport_expiration_date', '$date_birth', '$user_password')";

if ($conn->query($sql) === TRUE) {
    header("Location: user_img.php?email=$email");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
