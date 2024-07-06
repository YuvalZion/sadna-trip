<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>טיול חדש - קבוצת טיול</title>
    <link rel="stylesheet" href="trip.css">
    <script>
        // Function to add more profile input fields
        function addMoreProfile() {
            const profileContainer = document.getElementById('profile-container');
            const newProfile = document.createElement('div');
            newProfile.className = 'additional-profile';
            newProfile.innerHTML = `
                <label>שם מלא:</label>
                <input type="text" name="friend_name[]">
                <label>אימייל:</label>
                <input type="email" name="friend_email[]">
                <button type="button" onclick="removeProfile(this)">-</button>
            `;
            // Append the new profile to the container
            profileContainer.appendChild(newProfile);
        }
        
        // Function to remove a profile input field
        function removeProfile(button) {
            const profile = button.parentElement;
            profile.remove();
        }
        // Event listener for form submission
        document.getElementById('profiles-form').addEventListener('submit', function(event) {
            event.preventDefault();
            // Get the user's email
            const userEmail = document.getElementById('user-email').value;
            // Get all friend emails
            const emails = document.querySelectorAll('input[name="friend_email[]"]');
            // Set to store unique emails
            let emailSet = new Set();
            // Flag to check if user's email exists in friend emails
            let emailExists = false;
            // Flag to check for duplicate emails
            let duplicateEmail = false;
            
            // Check if user's email is in friend emails and for duplicate emails
            emails.forEach(emailInput => {
                if (emailInput.value === userEmail) {
                    emailExists = true;
                }
                if (emailInput.value) {
                    if (emailSet.has(emailInput.value)) {
                        duplicateEmail = true;
                    } else {
                        emailSet.add(emailInput.value);
                    }
                }
            });
            
            // Show alert if user's email is in friend emails
            if (emailExists) {
                alert('הינך חלק מטיול זה, יש למלא כתובת מייל אחרת');
                return;
            }
            
            // Show alert if there are duplicate emails
            if (duplicateEmail) {
                alert('הזנת את אותו האימייל מספר פעמים. כתוב את האימייל פעם אחת בלבד');
                return;
            }
            
            // Create and send an AJAX request to check if the email exists in the system
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'check_email.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    if (xhr.responseText === 'exists') {
                        document.getElementById('profiles-form').submit();
                    } else {
                        alert('האימייל "' + userEmail + '" לא קיים במערכת');
                    }
                } else {
                    alert('An error occurred while checking the email.');
                }
            };
            // Send the user's email for validation
            xhr.send('email=' + encodeURIComponent(userEmail));
        });
    </script>
</head>
<body>
    <div id="form2" class="container">
        <div class="logo-image"> <img src="../images/logo.jpg" alt="Logo"></div>
        <h2>יצירת קבוצת טיול</h2>
        <h3>הוספת חברים אשר רשומים למערכת</h3>
        <div class="form-container">
            <form id="profiles-form" action="submit_members.php" method="post">
                <input type="hidden" name="trip_num" value="<?php echo htmlspecialchars($_GET['trip_num']); ?>">
                
                <div id="profile-container">
                    <div class="additional-profile">
                        <label>שם מלא:</label>
                        <input type="text" name="friend_name[]">
                        <label>אימייל:</label>
                        <input type="email" name="friend_email[]">
                        <button type="button" onclick="removeProfile(this)">-</button>
                        
                    </div>
                </div>
                <button type="button" onclick="addMoreProfile()">הוספת חבר נוסף</button>
                
                <input type="hidden" id="user-email" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>"><br><br>
                <div class="form-footer">
                    <button type="submit">המשך</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>