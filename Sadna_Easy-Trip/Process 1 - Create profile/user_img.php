<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>הרשמה - הוספת תמונה</title>
    <link rel="stylesheet" href="user_profile.css">
    <script>
        // Function to display the selected photo as a preview
        function displaySelectedPhoto(event) {
            // Get the preview image element
            const preview = document.getElementById('preview');
            // Make the preview image element visible
            preview.style.display = 'block';
            // Set the source of the preview image to the selected file
            preview.src = URL.createObjectURL(event.target.files[0]);
            // Revoke the object URL after the image loads to free up memory
            preview.onload = () => URL.revokeObjectURL(preview.src);
        }
    </script>
</head>
<body>
    <div id="form2" class="container">
        <div class="logo-image"> <img src="../images/logo.jpg" alt="Logo"></div>
        <h2>הוספת תמונת פרופיל</h2>
        <div class="form-container">
            <!-- Form element with method POST, action to submit_img.php, and enctype for file upload -->
            <form id="picture-form" method="post" action="submit_img.php" enctype="multipart/form-data">
                <!-- File input for selecting a picture -->
                <input type="file" id="picture" name="picture" accept="image/*" onchange="displaySelectedPhoto(event)">
                
                <div class="preview-container">
                    <!-- Preview image element, initially hidden -->
                    <img id="preview" src="#" alt="תמונה מקדימה" style="display: none;">
                </div>
                
                <!-- Hidden field to hold the email passed from the first form -->
                <input type="hidden" id="email" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>"><br><br>
                
                <!-- Submit button -->
                <div class="form-footer">
                    <button type="submit">המשך</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
