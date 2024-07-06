<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>דף בית - טיול באיזי</title>
    <link rel="stylesheet" href="home_page.css">
    <style>
        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
    </style>
    <script>
        // Function to navigate to different pages based on the button clicked
        function navigateToPage(buttonId) {
             // Retrieve the email value from the input field
            const email = document.getElementById('email').value;
            let url = '';
            // Check which button was clicked and set the corresponding URL
            if (buttonId === 'create-trip') {
                url = '../Process 2 - Create trip/trip.php?email=' + encodeURIComponent(email);
            } else if (buttonId === 'personal-profile') {
                url = 'user_details.php?email=' + encodeURIComponent(email);
            } else if (buttonId === 'my-trip') {
                url = 'my_trip.php?email=' + encodeURIComponent(email);
            } 
            // Redirect to the constructed URL
            window.location.href = url;
        }
    </script>
</head>
<body>
    <div id="form1" class="container">
        <div class="logo-image"> <img src="../images/logo.jpg" alt="Logo"></div>
         <h2> דף בית</h2>
        <div class="form-container">
           <!--  <div class="right-image"> <img src="logo.jpg" alt="Logo"</div>-->
            <!-- Hidden field to hold the email passed from the first form -->
            <input type="hidden" id="email" name="email" value="<?php echo htmlspecialchars($_GET['email'], ENT_QUOTES, 'UTF-8'); ?>"><br><br>
            <div class= "home_page_buttons">
                <div class="form-footer">
                    <button type="button" id="create-trip" onclick="navigateToPage(this.id)">יצירת טיול חדש</button>
                </div>
                
                <br><br> 
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
    </div>
</body>
</html>
