<?php
// Get email from query parameter
$email = $_GET['email'];

// Database connection settings
$servername = "localhost";
$username = "zlilma_admin_yz";
$password = "zlilyuval123";
$dbname = "zlilma_test";

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
    <style>
        .container {
            width: 300px;
            margin: 0 auto;
            text-align: center;
        }
        .user-info {
            text-align: left;
            margin-top: 20px;
        }
        .user-image {
            width: 400px;
            height: 200px;
        }
        button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>התחברות בוצעה בהצלחה</h2>
        <div id="userInfo" class="user-info">
            <p>שם משתמש: <?php echo $user_info['user_name']; ?></p>
            <p>טלפון: <?php echo $user_info['phone']; ?></p>
            <p>תאריך תפוגת דרכון: <?php echo $user_info['passport_expiration_date']; ?></p>
            <p>תאריך לידה: <?php echo $user_info['date_birth']; ?></p>
            <p>תמונת משתמש:</p>
            <?php if ($user_image) {
                echo '<img class="user-image" src="data:image/jpeg;base64,' . base64_encode($user_image['user_image']) . '" alt="User Image" />';
            } else {
                echo '<p>אין תמונה זמינה</p>';
            } ?>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
