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

$trip_num = $_GET['trip_num'];

// Prepare and bind
$stmt = $conn->prepare("SELECT email FROM Trips WHERE trip_num = ?");
$stmt->bind_param("i", $trip_num);
$stmt->execute();
$stmt->bind_result($email);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>טיול נוצר בהצלחה</title>
    <link rel="stylesheet" href="trip.css">
    <script>
        function navigateToPage(buttonId) {
            var email = <?php echo json_encode($email); ?>;
            var homeLink = document.getElementById('home-link');
            
            let url = '';
            if (buttonId === 'home-link') {
                url = '../Home-Page/home_page.php?email=' + encodeURIComponent(email);
            }
            window.location.href = url;
        }
    </script>
</head>
<body>
 
    <div class="container">
        <div class="logo-image"> <img src="../images/logo.jpg" alt="Logo"></div>
        <h2>התהליך הושלם</h2>
        <h3>הטיול נוצר בהצלחה</h3>
        <div class="success-image"> 
            <img src="../images/success_trip.png" alt="success">
        </div>
        <div class="form-container">
            
            <div class="form-footer">
                <button type="button" id="home-link" onclick="navigateToPage(this.id)">מעבר לדף הבית</button>
            </div>
            
        
    </div>

</body>
</html>