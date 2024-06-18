<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>טיול חדש - פרטי יעד</title>
    <link rel="stylesheet" href="trip.css">
    <script>
        function validateForm() {
            var startDate = new Date(document.getElementById('start_date').value);
            var endDate = new Date(document.getElementById('end_date').value);
            var today = new Date();
            var budget = document.getElementById('budget').value;
            var trip_destination = document.getElementById('trip_destination').value;
            var trip_type = document.getElementById('trip_type').value;

            if (trip_destination == "select_d") {
                alert("יש לבחור יעד מרשימת היעדים");
                return false;
            }
            
            if (startDate < today) {
                alert("תאריך התחלה חייב להיות גדול מהתאריך הנוכחי");
                return false;
            }

            if (startDate >= endDate) {
                alert("תאריך התחלה חייב להיות קטן מתאריך סיום");
                return false;
            }

            if (budget <= 0) {
                alert("התקציב חייב להיות גדול מ-0");
                return false;
            }
            
            if (trip_type == "select_t") {
                alert("יש לבחור אופי טיול רלוונטי מהרשימה");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <div id="form1" class="container">
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
                <label for="hotel">מיקום לינה:</label>
                <input type="text" id="hotel" name="hotel" required>

                <h3>פרטי טיסה</h3>
                <label for="outbound_flight">זמן נחיתה:</label>
                <input type="time" id="outbound_flight" name="outbound_flight" required>
                <label for="return_flight">זמן טיסה חוזרת:</label>
                <input type="time" id="return_flight" name="return_flight" required>
                <label for="airline">חברת תעופה:</label>
                <input type="text" id="airline" name="airline" required>

                <label for="budget">תקציב לטיול (בדולרים):</label>
                <input type="number" id="budget" name="budget" required>
                <label for="trip_type">אופי הטיול:</label>
                <select id="trip_type" name="trip_type" required>
                    <option value="select_t">בחר/י</option>
                    <option value="work_trip">נסיעת עבודה</option>
                    <option value="family_trip">טיול משפחתי</option>
                    <option value="romantic">רומנטי</option>
                    <option value="culinary">קולינרי</option>
                    <option value="parks_amusements">פארקים ושעשועים</option>
                    <option value="scenery">נופים</option>
                    <option value="relaxation">בטן גב</option>
                    <option value="sports">ספורט</option>
                </select>
                
                <!-- Hidden field to hold the email passed from the first form -->
                <input type="hidden" id="email" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>"><br><br>
                
                <div>
                    <button type="submit">המשך</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
