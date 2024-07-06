<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>הרשמה - דירוג סוגי אטרקציות</title>
    <link rel="stylesheet" href="user_profile.css">
    <script>
        // Function to update the displayed rating value based on the range input
        function updateRating(rangeId, spanId) {
            // Get the range input element and the corresponding span element
            var range = document.getElementById(rangeId);
            var span = document.getElementById(spanId);
            // Update the text content of the span element to the current value of the range input
            span.textContent = range.value;
        }
    </script>
</head>
<body>
    <div id="form3" class="container">
        <div class="logo-image"> <img src="../images/logo.jpg" alt="Logo"></div>
        <h2>דירוג אטרקציות</h2>
        <h3>בדף זה יש לתת דירוג לסוגי האטרקציות לפי העדפותיך</h3>
        <div class="form-container">
            <form action="submit_attraction.php" method="post">
                <!-- Container for each attraction rating input -->
                <div class="attraction">
                    <label for="museums">מוזיאונים וגלריות:</label>
                    <!-- Range input for rating museums -->
                    <input type="range" id="museums" name="museums" min="1" max="10" value="5" oninput="updateRating('museums', 'museums-rating')">
                    <!-- Span to display the current rating value -->
                    <span id="museums-rating">5</span>
                </div>
                <div class="attraction">
                    <label for="restaurants">מסעדות:</label>
                    <input type="range" id="restaurants" name="restaurants" min="1" max="10" value="5" oninput="updateRating('restaurants', 'restaurants-rating')">
                    <span id="restaurants-rating">5</span>
                </div>
                <div class="attraction">
                    <label for="parks">פארקים:</label>
                    <input type="range" id="parks" name="parks" min="1" max="10" value="5" oninput="updateRating('parks', 'parks-rating')">
                    <span id="parks-rating">5</span>
                </div>
                <div class="attraction">
                    <label for="water_activities">פעילויות מים:</label>
                    <input type="range" id="water_activities" name="water_activities" min="1" max="10" value="5" oninput="updateRating('water_activities', 'water_activities-rating')">
                    <span id="water_activities-rating">5</span>
                </div>
                <div class="attraction">
                    <label for="nature_trips">טיולי שטח וטבע:</label>
                    <input type="range" id="nature_trips" name="nature_trips" min="1" max="10" value="5" oninput="updateRating('nature_trips', 'nature_trips-rating')">
                    <span id="nature_trips-rating">5</span>
                </div>
                <div class="attraction">
                    <label for="extreme_sports">אקסטרים וספורט:</label>
                    <input type="range" id="extreme_sports" name="extreme_sports" min="1" max="10" value="5" oninput="updateRating('extreme_sports', 'extreme_sports-rating')">
                    <span id="extreme_sports-rating">5</span>
                </div>
                <div class="attraction">
                    <label for="religion_culture">דת ותרבות:</label>
                    <input type="range" id="religion_culture" name="religion_culture" min="1" max="10" value="5" oninput="updateRating('religion_culture', 'religion_culture-rating')">
                    <span id="religion_culture-rating">5</span>
                </div>
                <div class="attraction">
                    <label for="shopping">קניות:</label>
                    <input type="range" id="shopping" name="shopping" min="1" max="10" value="5" oninput="updateRating('shopping', 'shopping-rating')">
                    <span id="shopping-rating">5</span>
                </div>
                <div class="attraction">
                    <label for="night_life">חיי לילה:</label>
                    <input type="range" id="night_life" name="night_life" min="1" max="10" value="5" oninput="updateRating('night_life', 'night_life-rating')">
                    <span id="night_life-rating">5</span>
                </div>
                <div class="attraction">
                    <label for="shows_and_plays">מופעים והצגות:</label>
                    <input type="range" id="shows_and_plays" name="shows_and_plays" min="1" max="10" value="5" oninput="updateRating('shows_and_plays', 'shows_and_plays-rating')">
                    <span id="shows_and_plays-rating">5</span>
                </div>

                <!-- Hidden field to hold the email passed from the previous form -->
                <input type="hidden" id="email" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>"><br>
                
                <!-- Submit button -->
                <div class="form-footer">
                    <button type="submit">המשך</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>