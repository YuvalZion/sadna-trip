<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>הרשמה - פריטים לצ'ק ליסט</title>
    <link rel="stylesheet" href="user_profile.css">
    <script>
        // Function to add a new item input field
        function addMoreItem() {
            var itemsContainer = document.getElementById('items-container');
            // Create a new div element for the additional item
            var newDiv = document.createElement('div');
            newDiv.setAttribute('class', 'additional-text'); 
            // Create a label element for the item
            var newLabel = document.createElement('label');
            newLabel.textContent = 'פריט'; 
            // Create an input element for the item
            var newInput = document.createElement('input');
            newInput.setAttribute('type', 'text'); 
            newInput.setAttribute('name', 'items[]'); 
            // Create a button to remove the item
            var removeButton = document.createElement('button');
            removeButton.setAttribute('type', 'button'); 
            removeButton.setAttribute('onclick', 'removeItem(this)'); // Call removeItem function on click
            removeButton.textContent = '-'; 
            // Append label, input, and remove button to the new div
            newDiv.appendChild(newLabel);
            newDiv.appendChild(newInput);
            newDiv.appendChild(removeButton);
            // Append the new div to the items container
            itemsContainer.appendChild(newDiv);

            // Scroll the window to show the newly added item
            window.scrollBy(0, newDiv.clientHeight);
        }

        // Function to remove an item
        function removeItem(button) {
            button.parentNode.remove(); // Remove the parent div of the button 
        }
    </script>
</head>

<body>
    <div id="form5" class="container">
        <div class="logo-image"> <img src="../images/logo.jpg" alt="Logo"></div>
        <h2>הוספת פריטים</h2>
        <h4>האם יש פריטים שהיית רוצה שייכנסו אל הצ'ק ליסט? כגון תרופות קבועות, חיסונים שיש לבצע וכדומה</h4>
        <div class="form-container">
            <form id="about-form" method="post" action="submit_item.php">
                <div id="items-container">
                    <!-- Label and input for the first item -->
                    <label for="item">פריט</label>
                    <input type="text" id="item" name="items[]">
                </div>
                <!-- Button to add more items -->
                <button type="button" onclick="addMoreItem()">+</button>
                
                <!-- Hidden field to hold the email passed from the previous form -->
                <input type="hidden" id="email" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>"><br><br>
                
                <!-- Form submission button -->
                <div class="form-footer">
                    <button type="submit">יצירת פרופיל</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
