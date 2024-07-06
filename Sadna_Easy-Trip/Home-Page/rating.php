<?php
// Database connection
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

// Retrieve the user's email and trip number from the request
$email = $_GET['email'];
$trip_num = $_GET['trip_num'];

// Query to get the attraction numbers for the trip by trip_num
$sql = "SELECT Attraction_Number FROM Routes_trip WHERE trip_num = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trip_num);
$stmt->execute();
$result = $stmt->get_result();

// Get the attraction names using the attraction numbers and existing ratings
$attractions = [];
while ($row = $result->fetch_assoc()) {
    $Attraction_Number  = $row['Attraction_Number'];
    $sql2 = "SELECT Attraction_Name FROM Attraction_Data WHERE Attraction_Number = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $Attraction_Number );
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    if ($row2 = $result2->fetch_assoc()) {
        $attractions[$Attraction_Number ] = $row2['Attraction_Name'];
    }
}

// Query to get trip details
$sql = "SELECT date_start, date_end, destination FROM Trips WHERE trip_num=$trip_num";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$start_date = $row['date_start'];
$end_date = $row['date_end'];
$destination = $row['destination'];

// Format the dates to dd/mm/yyyy
$start_date_formatted = (new DateTime($start_date))->format('d/m/Y');
$end_date_formatted = (new DateTime($end_date))->format('d/m/Y');

$stmt->close();

// Get existing ratings
$sql3 = "SELECT Attraction_Number, rate FROM Rating WHERE email = ? AND trip_num = ?";
$stmt3 = $conn->prepare($sql3);
$stmt3->bind_param("si", $email, $trip_num);
$stmt3->execute();
$result3 = $stmt3->get_result();
$existing_ratings = [];
while ($row3 = $result3->fetch_assoc()) {
    $existing_ratings[$row3['Attraction_Number']] = $row3['rate'];
}
$stmt3->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>דירוג אטרקציות של הטיול</title>
    <link rel="stylesheet" href="home_page.css">
    <script>
         // Function to rate an attraction based on the number of stars clicked
        function rateAttraction(stars, attractionNum) {
            // Loop through all 5 stars
            for (let i = 1; i <= 5; i++) {
                const star = document.getElementById(`star_${attractionNum}_${i}`);
                // Add 'selected' class to stars less than or equal to the clicked star
                if (i <= stars) {
                    star.classList.add('selected');
                } else {
                    // Remove 'selected' class from stars greater than the clicked star
                    star.classList.remove('selected');
                }
            }
            // Set the hidden input value to the number of stars clicked
            document.getElementById(`rate${attractionNum}`).value = stars;
        }
        
        // Function to load existing ratings when the page loads
        function loadRatings() {
            <?php foreach ($existing_ratings as $Attraction_Number => $rate): ?>
            // Call rateAttraction with existing ratings from PHP array
            rateAttraction(<?php echo $rate; ?>, <?php echo $Attraction_Number; ?>);
            <?php endforeach; ?>
        }
    </script>
    <!-- Design for the rating page-->
    <style>
         .form-container {
            padding: 30px;
            direction: ltr;
            margin-top:-20px;
        }
        h3{
            text-align: center;
        }
        h4{
            text-align: center;
            color: #ca5e17;
            font-size: 18px;
        }
        .attraction-rating {
            margin-bottom: 20px;
            padding: 10px;
            border-bottom: 1px solid #ccc;
            margin-right: 10px;
        }
        .attraction-rating label {
            display: inline-block;
            width: 350px;
            font-weight: bold;
        }
        .stars {
            display: inline-block;
        }
        .star {
            font-size: 2em;
            color: gray;
            cursor: pointer;
            display: inline-block;
        }
        .star.selected {
            color: gold;
        }
        .form-footer {
            display: flex;
            justify-content: flex-start;
            margin-top: 20px;
        }

    </style>
</head>
<body onload="loadRatings()">
    <div class="container">
        <div class="logo-image"> <img src="../images/logo.jpg" alt="Logo"></div>
        <h2>דירוג אטרקציות</h2>
        <h3>דירוג האטרקציות שביקרת בהן בטיול:</h3>
          <h4><?php echo htmlspecialchars($destination) . " " . htmlspecialchars($start_date_formatted) . " - " . htmlspecialchars($end_date_formatted); ?></h4>
        
        <div class="form-container">
    <form action="submit_rating.php" method="post">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <input type="hidden" name="trip_num" value="<?php echo htmlspecialchars($trip_num); ?>">

        <?php foreach ($attractions as $Attraction_Number => $Attraction_Name): ?>
            <div class="attraction-rating">
                <label for="attraction_<?php echo $Attraction_Number; ?>"><?php echo htmlspecialchars($Attraction_Name); ?></label>
                <div id="stars_<?php echo $Attraction_Number; ?>" class="stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star" id="star_<?php echo $Attraction_Number; ?>_<?php echo $i; ?>" onclick="rateAttraction(<?php echo $i; ?>, <?php echo $Attraction_Number; ?>)">★</span>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="rate[<?php echo $Attraction_Number; ?>]" id="rate<?php echo $Attraction_Number; ?>" value="0">
            </div>
            <?php endforeach; ?>
            <div class="form-footer">
                <button type="submit">שמירת דירוג</button>
            </div>
    </form>
    </div>
    </div>
</body>
</html>
