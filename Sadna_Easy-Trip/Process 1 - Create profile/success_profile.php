<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>הרשמה בוצעה</title>
    <link rel="stylesheet" href="user_profile.css">
    <script>
        function navigateToPage(buttonId) {
            const email = document.getElementById('email').value;
            let url = '';
            if (buttonId === 'home-page') {
                url = '../Home-Page/home_page.php?email=' + encodeURIComponent(email);
            }
            window.location.href = url;
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="logo-image"> <img src="../images/logo.jpg" alt="Logo"></div>
        <h2>הרשמה בוצעה</h2>
        <h3>הפרופיל נוצר בהצלחה</h3>
        <h4>כעת ניתן ליצור טיולים חדשים בהתאמה אישית</h4>
        <div class="success-image"> 
            <img src="../images/success.jpg" alt="success">
        </div>
        <div class="form-container">
            <!-- Hidden field to hold the email passed from the first form -->
            <input type="hidden" id="email" name="email" value="<?php echo htmlspecialchars($_GET['email'], ENT_QUOTES, 'UTF-8'); ?>"><br><br>
            <div class="form-footer">
                <button type="button" id="home-page" onclick="navigateToPage(this.id)">מעבר לדף הבית</button>
            </div>
        </div>
    </div>
    
    
</body>
</html>