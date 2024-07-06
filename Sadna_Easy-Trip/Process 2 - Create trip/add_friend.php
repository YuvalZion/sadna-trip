<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>טיול חדש - קבוצת טיול</title>
    <link rel="stylesheet" href="trip.css">
</head>
<script>
        // Function to add a new profile input section
        function addMoreProfile() {
            // Get the container where new profiles will be added
            const profileContainer = document.getElementById('profile-container');
             // Create a new div element for the additional profile
            const newProfile = document.createElement('div');
            newProfile.className = 'additional-profile';
            // Define the HTML content for the new profile input section
            newProfile.innerHTML = `
                <label>שם מלא:</label>
                <input type="text" name="friend_name[]">
                <label>גיל:</label>
                <input type="number" name="friend_age[]">
                <button type="button" onclick="removeProfile(this)">-</button>
            `;
             // Append the new profile section to the profile container
            profileContainer.appendChild(newProfile);
        }
        // Function to remove a profile input section
        function removeProfile(button) {
            // Get the parent element of the button (which is the profile section)
            const profile = button.parentElement;
             // Remove the profile section from the DOM
            profile.remove();
        }
</script>

<body>
    <div id="form2" class="container">
        <div class="logo-image"> <img src="../images/logo.jpg" alt="Logo"></div>
        <h2>יצירת קבוצת טיול</h2>
        <h3>הוספת חברים שלא במערכת</h3>
        <div class="form-container">
            <form id="profiles-form" action="submit_friend.php" method="post">
                <input type="hidden" name="trip_num" value="<?php echo htmlspecialchars($_GET['trip_num']); ?>">
                
                <div id="profile-container">
                    <div class="additional-profile">
                        <label>שם מלא:</label>
                        <input type="text" name="friend_name[]">
                        <label>גיל:</label>
                        <input type="number" name="friend_age[]">
                        <button type="button" onclick="removeProfile(this)">-</button>
                    </div>
                </div>
                <button type="button" onclick="addMoreProfile()">הוספת חבר נוסף</button>
                <div class="form-footer">
                    <button type="submit">המשך</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>