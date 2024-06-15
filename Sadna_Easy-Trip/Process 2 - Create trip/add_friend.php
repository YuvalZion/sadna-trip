<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>טיול חדש - קבוצת טיול</title>
    <link rel="stylesheet" href="trip.css">
</head>
<body>
    <div id="form2" class="container">
        <h2>הוספת חברים שלא במערכת </h2>
        <div class="form-container">
            <form id="profiles-form" action="submit_friend.php" method="post" onsubmit="return validateForm()">
                
                <input type="hidden" name="trip_num" value="<?php echo htmlspecialchars($_GET['trip_num']); ?>">
                
                <div id="profile-container">
                    <div class="additional-profile">
                        <label>שם מלא:</label>
                        <input type="text" name="friend_name[]" required>
                        <label>גיל:</label>
                        <input type="number" name="friend_age[]" required>
                        <button type="button" onclick="removeProfile(this)">-</button>
                    </div>
                </div>
                <button type="button" onclick="addMoreProfile()">הוספת חבר</button>
                <div class="form-footer">
                    <input id="sub" type="submit" value="Submit">
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function addMoreProfile() {
            const profileContainer = document.getElementById('profile-container');
            const newProfile = document.createElement('div');
            newProfile.className = 'additional-profile';
            newProfile.innerHTML = `
                <label>שם מלא:</label>
                <input type="text" name="friend_name[]" required>
                <label>גיל:</label>
                <input type="number" name="friend_age[]" required>
                <button type="button" onclick="removeProfile(this)">-</button>
            `;
            profileContainer.appendChild(newProfile);
        }

        function removeProfile(button) {
            const profile = button.parentElement;
            profile.remove();
        }
    </script>
</body>
</html>
