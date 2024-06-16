<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>דף בית - טיול באיזי</title>
    <link rel="stylesheet" href="home_page.css">
    <script>
        function navigateToPage(buttonId) {
            const email = document.getElementById('email').value;
            
            let url = '';

            if (buttonId === 'create-trip') {
                url = '../Process 2 - Create trip/trip.php?email=' + encodeURIComponent(email);
            } else if (buttonId === 'personal-profile') {
                url = 'user_details.php?email=' + encodeURIComponent(email);
            } else if (buttonId === 'my-trip') {
                url = 'my_trip.php?email=' + encodeURIComponent(email);
            } 

            window.location.href = url;
        }
    </script>
</head>
<body>
    <div id="form1" class="container">
        <h2>דף בית</h2>
        <div class="form-container">
            <!-- Hidden field to hold the email passed from the first form -->
            <input type="hidden" id="email" name="email" value="<?php echo htmlspecialchars($_GET['email'], ENT_QUOTES, 'UTF-8'); ?>"><br><br>

            <div class="form-footer">
                <button type="button" id="create-trip" onclick="navigateToPage(this.id)">יצירת טיול חדש</button>
            </div>
            
            <br><br> <!-- Added some space between the buttons for better layout -->
            <div class="form-footer">
                <button type="button" id="my-trip" onclick="navigateToPage(this.id)">הטיולים שלי</button>
            </div>
            
            <br><br>
            <div class="form-footer">
                <button type="button" id="personal-profile" onclick="navigateToPage(this.id)">פרופיל אישי</button>
            </div>
            <br><br>
            
        </div>
    </div>
</body>
</html>
