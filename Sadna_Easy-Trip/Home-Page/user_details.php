<?php
// Get email from query parameter
$email = $_GET['email'];

// Database connection settings
$servername = "localhost";
$username = "zlilma_admin_smy";
$password = "easy_trip123";
$dbname = "zlilma_Easy_Trip";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize input
$email = $conn->real_escape_string($email);

// Query to get user information
$sql = "SELECT user_name, phone, passport_expiration_date, date_birth FROM User_profile WHERE email='$email'";
$result = $conn->query($sql);
$user_info = $result->fetch_assoc();

$sql = "SELECT user_image FROM User_image WHERE email='$email'";
$result = $conn->query($sql);
$user_image = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="home_page.css">
</head>
<body>
    <div class="container">
        <h2>פרטי משתמש</h2>
        <div class="form-container">
            <p>תמונת משתמש:</p>
            <?php if ($user_image) {
                echo '<img class="user-image" src="data:image/jpeg;base64,' . base64_encode($user_image['user_image']) . '" alt="User Image" />';
            } else {
                echo '<p>אין תמונה זמינה</p>';
            } ?>
            <p>שם משתמש: <?php echo $user_info['user_name']; ?></p>
            <p>טלפון: <?php echo $user_info['phone']; ?></p>
            <p>תאריך תפוגת דרכון: <?php echo $user_info['passport_expiration_date']; ?></p>
            <p>תאריך לידה: <?php echo $user_info['date_birth']; ?></p>
            
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
