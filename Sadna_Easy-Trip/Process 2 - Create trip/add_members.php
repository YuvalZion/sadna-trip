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
        <h2>הוספת פרופילים לטיול</h2>
        <div class="form-container">
            <form id="profiles-form" action="submit_members.php" method="post">
                <input type="hidden" name="trip_num" value="<?php echo htmlspecialchars($_GET['trip_num']); ?>">
                <div id="profile-container">
                    <div class="additional-profile">
                        <label>שם מלא:</label>
                        <input type="text" name="friend_name[]" required>
                        <label>אימייל:</label>
                        <input type="email" name="friend_email[]" required>
                        <button type="button" onclick="removeProfile(this)">-</button>
                    </div>
                </div>
                <button type="button" onclick="addMoreProfile()">הוספת חבר</button>
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>"><br><br>
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
                <label>אימייל:</label>
                <input type="email" name="friend_email[]" required>
                <button type="button" onclick="removeProfile(this)">-</button>
            `;
            profileContainer.appendChild(newProfile);
        }

        function removeProfile(button) {
            const profile = button.parentElement;
            profile.remove();
        }

        document.getElementById('profiles-form').addEventListener('submit', function(event) {
            event.preventDefault();
            
            const email = document.querySelector('input[name="email"]').value;
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'check_email.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    if (xhr.responseText === 'exists') {
                        document.getElementById('profiles-form').submit();
                    } else {
                        alert('האימייל "' + email + '"לא קיים במערכת');
                    }
                } else {
                    alert('An error occurred while checking the email.');
                }
            };
            xhr.send('email=' + encodeURIComponent(email));
        });
    </script>
</body>
</html>
