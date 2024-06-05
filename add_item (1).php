<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> הרשמה - פריטים לצ'ק ליסט  </title>
    <link rel="stylesheet" href="user_profile.css">
</head>
<body>
    <div id="form5" class="container">
        <h2> האם יש פריטים שהיית רוצה <br>שייכנסו אל הצ'ק ליסט?  </h2>
        <div class="form-container">
            <form id="about-form" method="post" action="submit_item.php">
                <label for="about">כגון תרופות קבועות, חיסונים שיש לבצע וכדומה</label>
                <div id="items-container">
                    <label for="item">פריט</label>
                    <input type="text" id="item" name="items[]" required>
                </div>
                <button type="button" onclick="addMoreItem()">+</button>
                
                <!-- Hidden field to hold the email passed from the previous form -->
                <input type="hidden" id="email" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>"><br><br>
                
                <div class="form-footer">
                    <button type="submit">סיום</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function addMoreItem() {
            var itemsContainer = document.getElementById('items-container');

            var newDiv = document.createElement('div');
            newDiv.setAttribute('class', 'additional-text');
            var newLabel = document.createElement('label');
            newLabel.textContent = 'פריט';
            var newInput = document.createElement('input');
            newInput.setAttribute('type', 'text');
            newInput.setAttribute('name', 'items[]');
            newInput.setAttribute('required', 'required'); // Make the dynamically added field required
            var removeButton = document.createElement('button');
            removeButton.setAttribute('type', 'button');
            removeButton.setAttribute('onclick', 'removeItem(this)');
            removeButton.textContent = '-';
            newDiv.appendChild(newLabel);
            newDiv.appendChild(newInput);
            newDiv.appendChild(removeButton);
            itemsContainer.appendChild(newDiv);

            // Increase the height of the page to accommodate the new text box
            window.scrollBy(0, newDiv.clientHeight);
        }

        function removeItem(button) {
            button.parentNode.remove();
        }
    </script>
</body>
</html>
