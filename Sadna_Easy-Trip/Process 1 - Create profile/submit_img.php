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
    // If there is a connection error, terminate the script and display the error message
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request method is POST and if the email is set
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Check if the user chose to skip the image upload or if no file was uploaded
    if (isset($_POST['skip-image']) || (isset($_FILES['picture']) && $_FILES['picture']['error'] == UPLOAD_ERR_NO_FILE)) {
        // If so, redirect directly to the Attraction_rate.php page with the email parameter
        header("Location: Attraction_rate.php?email=" . urlencode($email));
        exit();
    }

    // Check if a file was uploaded
    if (isset($_FILES['picture'])) {
        $image = $_FILES['picture']['tmp_name'];
        
        // Check if the uploaded file is a valid image
        $valid_mime_types = ['image/jpeg', 'image/png', 'image/bmp', 'image/tiff', 'image/jpg'];
        $check = getimagesize($image);
        if ($check !== false && in_array($check['mime'], $valid_mime_types)) {
            // Read the image content
            $imgData = file_get_contents($image);

            // Prepare and bind the SQL statement to insert the image data into the User_image table
            $stmt = $conn->prepare("INSERT INTO User_image (user_image, email) VALUES (?, ?) ON DUPLICATE KEY UPDATE user_image = VALUES(user_image)");
            $null = NULL;
            $stmt->bind_param("bs", $null, $email);

            // Send the image data
            $stmt->send_long_data(0, $imgData);
            if ($stmt->execute()) {
                // If the image was uploaded successfully, redirect to the Attraction_rate.php page with the email parameter
                header("Location: Attraction_rate.php?email=" . urlencode($email));
                exit();
            } else {
                // If there was an error uploading the image, display the error message
                echo "Error uploading image: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            // If the file is not a valid image, display an alert and redirect back to the image upload page
            echo "<script>alert('הקובץ גדול מידי, בחר תמונה אחרת או המשך');window.location.href='user_img.php?email=" . urlencode($email) . "';</script>";
            exit();
        }
    }
}

// Close the database connection
$conn->close();
?>