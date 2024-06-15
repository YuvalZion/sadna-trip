<?php
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['picture']) && isset($_POST['email'])) {
    $email = $_POST['email'];
    $image = $_FILES['picture']['tmp_name'];
    
    // Check if file is an image
    $check = getimagesize($image);
    if ($check !== false) {
        $imgData = file_get_contents($image);

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO User_image (user_image, email) VALUES (?, ?) ON DUPLICATE KEY UPDATE user_image = VALUES(user_image)");
        $stmt->bind_param("bs", $null, $email); // "b" for blob

        $stmt->send_long_data(0, $imgData);
        if ($stmt->execute()) {
            // Image uploaded successfully, now redirect to add_item.php with email
            header("Location: Attraction_rate.php?email=" . urlencode($email));
            exit();
        } else {
            echo "Error uploading image: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "File is not an image.";
    }
}

// Close connection
$conn->close();
?>
