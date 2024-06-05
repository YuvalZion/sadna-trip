<?php
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

$success = true; // Flag to check if all items were successfully inserted

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    $items = $_POST['items'];

    foreach ($items as $item) {
        if (!empty($item)) {
            // SQL query to insert data into the User_item table
            $sql = "INSERT INTO User_item (email, item) VALUES ('$email', '$item')";

            if ($conn->query($sql) !== TRUE) {
                $success = false;
                break; // Exit loop if any insertion fails
            }
        }
    }
}

// Close connection
$conn->close();

if ($success) {
    // Redirect to good.php with a success message
    header("Location: good.php");
    exit();
} else {
    echo "Error: There was a problem inserting the items into the database.";
}
?>
