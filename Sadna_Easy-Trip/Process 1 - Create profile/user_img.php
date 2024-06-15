<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>הרשמה - הוספת תמונה</title>
    <link rel="stylesheet" href="user_profile.css">
</head>
<body>
    <div id="form2" class="container">
        <h2>הוספת תמונה</h2>
        <div class="form-container">
            <form id="picture-form" method="post" action="submit_img.php" enctype="multipart/form-data" onsubmit="return validateImage()">
                <input type="file" id="picture" name="picture" accept="image/*" capture="environment" onchange="displaySelectedPhoto(event)">
                <div class="preview-container">
                    <img id="preview" src="#" alt="תמונה מקדימה" style="display: none;">
                </div>
                <!-- Hidden field to hold the email passed from the first form -->
                <input type="hidden" id="email" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>"><br><br>
                
                <div class="form-footer">
                    <button type="submit">המשך</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function displaySelectedPhoto(event) {
            const preview = document.getElementById('preview');
            preview.style.display = 'block';
            preview.src = URL.createObjectURL(event.target.files[0]);
            preview.onload = () => URL.revokeObjectURL(preview.src);
        }

        function validateImage() {
            const pictureInput = document.getElementById('picture');
            if (pictureInput.files.length === 0) {
                alert("יש לבחור תמונה.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
