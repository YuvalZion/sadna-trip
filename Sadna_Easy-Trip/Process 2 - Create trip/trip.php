<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>טיול חדש - פרטי יעד</title>
    <link rel="stylesheet" href="trip.css">
    <script>
        function validateForm() {
            // Get the data captured from the form
            var startDate = new Date(document.getElementById('start_date').value);
            var endDate = new Date(document.getElementById('end_date').value);
            var today = new Date();
            var budget = document.getElementById('budget').value;
            var trip_destination = document.getElementById('trip_destination').value;
            var airline = document.getElementById('airline').value;
            
            // Check if a trip destination is selected
            if (trip_destination == "select_d") {
                alert("יש לבחור יעד מרשימת היעדים");
                return false;
            }
             // Check if the start date is after the current date
            if (startDate < today) {
                alert("תאריך התחלה חייב להיות גדול מהתאריך הנוכחי");
                return false;
            }
            // Check if the start date is before the end date
            if (startDate >= endDate) {
                alert("תאריך התחלה חייב להיות קטן מתאריך סיום");
                return false;
            }
            // Calculate the difference in time between the start date and end date
            var timeDiff = endDate.getTime() - startDate.getTime();
            // Convert the time difference to days
            var dayDiff = timeDiff / (1000 * 3600 * 24);
            // Check if the trip duration is more than 4 days
            if (dayDiff > 4) {
                alert("הטיול לא יכול להיות ארוך יותר מ-4 ימים");
                return false;
            }
            // Check if an airline is selected
            if (airline == "select_a") {
                alert("יש לבחור חברת תעופה מהרשימה");
                return false;
            }
             // Check if the budget is greater than 0
            if (budget <= 0) {
                alert("התקציב חייב להיות גדול מ-0");
                return false;
            }
            // If all validations pass, allow the form to be submitted
            return true;
        }
    </script>
</head>
<body>
    <div id="form1" class="container">
        <div class="logo-image"> <img src="../images/logo.jpg" alt="Logo"></div>
        <h2>פרטי טיול </h2>
        <div class="form-container">
            <form id="user-form" action="submit_trip.php" method="post" onsubmit="return validateForm()">
                <label for="target">יעד:</label>
                <select id="trip_destination" name="trip_destination" required>
                    <option value="select_d">בחר/י</option>
                    <option value="Barcelona">ברצלונה, ספרד</option>
                    <option value="London">לונדון, בריטניה</option>
                    <option value="New York">ניו-יורק, ארה"ב</option>
                    <option value="Canberra">קנברה, אוסטרליה</option>
                </select>
                <label for="start_date">תאריך התחלה:</label>
                <input type="date" id="start_date" name="start_date" required>
                <label for="end_date">תאריך סיום:</label>
                <input type="date" id="end_date" name="end_date" required>
               
                <label for="budget">תקציב לטיול (בדולרים):</label>
                <input type="number" id="budget" name="budget" required>

                <h4>פרטי טיסה:</h4>
                
                <label for="airline">חברת תעופה:</label>
                <select id="airline" name="airline" required>
                    <option value="select_a">בחר/י</option>
                    <option value="Elal">אל על</option>
                    <option value="Arkia">ארקיע</option>
                    <option value="Israir">ישראייר</option>
                    <option value="other">אחר</option>
                </select>
                
                <label for="outbound_flight"> שעת טיסה - הלוך:</label>
                <input type="time" id="outbound_flight" name="outbound_flight" required>
                <label for="return_flight"> שעת טיסה - חזור:</label>
                <input type="time" id="return_flight" name="return_flight" required>
                
                <!-- Hidden field to hold the email passed from the first form -->
                <input type="hidden" id="email" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>"><br><br>
                
                <div class="form-footer">
                    <button type="submit">המשך</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>