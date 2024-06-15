<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>טיול חדש - בחירת תתי קטגוריות</title>
    <link rel="stylesheet" href="trip.css">
</head>
<body>
    <div id="form4" class="container">
        <h2>בחירת תתי קטגוריות</h2>
        <div class="form-container">
            <form id="profiles-form" action="submit_sub_attraction.php" method="post" onsubmit="return validateForm()">
                <input type="hidden" name="trip_num" value="<?php echo htmlspecialchars($_GET['trip_num']); ?>">
                
                <div class="menu-container">
                    <div class="menu">
                        <button type="button" onclick="toggleSubForm('museum-types')">מוזיאונים וגלריות</button>
                        <button type="button" onclick="toggleSubForm('parks-types')">פארקים</button>
                        <button type="button" onclick="toggleSubForm('Water_activities-types')">פעילויות מים</button>
                        <button type="button" onclick="toggleSubForm('Terrain_nature-types')">טיולי שטח וטבע</button>
                        <button type="button" onclick="toggleSubForm('extreme-sports-types')">אקסטרים וספורט</button>
                        <button type="button" onclick="toggleSubForm('religion-culture-types')">דת ותרבות</button>
                        <button type="button" onclick="toggleSubForm('shopping-types')">קניות</button>
                        <button type="button" onclick="toggleSubForm('nightlife-types')">חיי לילה</button>
                        <button type="button" onclick="toggleSubForm('shows-types')">מופעים והצגות</button>
                        <button type="button" onclick="toggleSubForm('restaurant-types')">מסעדות</button>
                    </div>
                </div>
 
                <div class="sub-form-container">
                    <div id="museum-types" class="sub-form" style="display: none;">
                        <label>מוזיאונים וגלריות:</label>
                        <label><input type="checkbox" name="museums[]" value="History">היסטוריה</label>
                        <label><input type="checkbox" name="museums[]" value="Art">אומנות</label>
                        <label><input type="checkbox" name="museums[]" value="Architecture">אדריכלות</label>
                        <label><input type="checkbox" name="museums[]" value="Air and Space">אוויר וחלל</label>
                        <label><input type="checkbox" name="museums[]" value="Fashion">אופנה</label>
                        <label><input type="checkbox" name="museums[]" value="Children">ילדים</label>
                        <label><input type="checkbox" name="museums[]" value="Science">מדע</label>
                        <label><input type="checkbox" name="museums[]" value="Design">עיצוב</label>
                        <label><input type="checkbox" name="museums[]" value="Wax">שעווה</label>
                        <label><input type="checkbox" name="museums[]" value="Sports">ספורט</label>
                    </div>
                    
                    <div id="parks-types" class="sub-form" style="display: none;">
                        <label>פארקים:</label>
                        <label><input type="checkbox" name="parks[]" value="Amusement Park">פארק שעשועים</label>
                        <label><input type="checkbox" name="parks[]" value="Water Park">פארק מים</label>
                        <label><input type="checkbox" name="parks[]" value="Botanical Gardens">גנים בוטניים</label>
                        <label><input type="checkbox" name="parks[]" value="Zoo">גן חיות</label>
                        <label><input type="checkbox" name="parks[]" value="Nature and Gardens">טבע וגנים</label>
                        <label><input type="checkbox" name="parks[]" value="Adventure Park">פארק אתגרי</label>
                    </div>
                    
                    <div id="Water_activities-types" class="sub-form" style="display: none;">
                        <label>פעילויות מים:</label>
                        <label><input type="checkbox" name="Water_activities[]" value="Rafting">רפטינג</label>
                        <label><input type="checkbox" name="Water_activities[]" value="Streams">נחלים</label>
                        <label><input type="checkbox" name="Water_activities[]" value="Surfing">גלישה</label>
                        <label><input type="checkbox" name="Water_activities[]" value="Diving">צלילה</label>
                        <label><input type="checkbox" name="Water_activities[]" value="Beaches">חופים</label>
                        <label><input type="checkbox" name="Water_activities[]" value="Sailing">שיט</label>
                    </div>
                    
                    <div id="Terrain_nature-types" class="sub-form" style="display: none;">
                        <label>טיולי שטח וטבע:</label>
                        <label><input type="checkbox" name="Terrain_nature[]" value="Bicycle">טיולי אופניים</label>
                        <label><input type="checkbox" name="Terrain_nature[]" value="Jeeps">טיולי ג'יפים</label>
                        <label><input type="checkbox" name="Terrain_nature[]" value="Astronomy">טיולי אסטרונומיה</label>
                        <label><input type="checkbox" name="Terrain_nature[]" value="Hiking">טיול רגלי</label>
                        <label><input type="checkbox" name="Terrain_nature[]" value="Bird Watching">צפרות</label>
                        <label><input type="checkbox" name="Terrain_nature[]" value="Horseback Riding">רכיבה על סוסים</label>
                    </div>
                    
                    <div id="extreme-sports-types" class="sub-form" style="display: none;">
                        <label>אקסטרים וספורט:</label>
                        <label><input type="checkbox" name="sports[]" value="Skiing">סקי</label>
                        <label><input type="checkbox" name="sports[]" value="Skydiving">צניחה חופשית</label>
                        <label><input type="checkbox" name="sports[]" value="Hot Air Balloon">כדור פורח</label>
                        <label><input type="checkbox" name="sports[]" value="Mountain Climbing">טיפוס הרים</label>
                        <label><input type="checkbox" name="sports[]" value="Abseiling">סנפלינג</label>
                        <label><input type="checkbox" name="sports[]" value="Windsurfing">גלישת רוח</label>
                        <label><input type="checkbox" name="sports[]" value="Paintball">פיינטבול</label>
                        <label><input type="checkbox" name="sports[]" value="Karting">קארטינג</label>
                    </div>
                    
                    <div id="religion-culture-types" class="sub-form" style="display: none;">
                        <label>דת ותרבות:</label>
                        <label><input type="checkbox" name="religion-culture[]" value="Mosque">מסגדים</label>
                        <label><input type="checkbox" name="religion-culture[]" value="Church">כנסיות</label>
                        <label><input type="checkbox" name="religion-culture[]" value="Synagogue">בתי כנסת</label>
                        <label><input type="checkbox" name="religion-culture[]" value="Stadium">אצטדיונים</label>
                        <label><input type="checkbox" name="religion-culture[]" value="Historical Site">אתרים היסטוריים</label>
                        <label><input type="checkbox" name="religion-culture[]" value="Observation Point">נקודות תצפית</label>
                    </div>
                    
                    <div id="shopping-types" class="sub-form" style="display: none;">
                        <label>קניות:</label>
                        <label><input type="checkbox" name="shopping[]" value="Mall">קניונים</label>
                        <label><input type="checkbox" name="shopping[]" value="Market">שווקים</label>
                        <label><input type="checkbox" name="shopping[]" value="Shopping Center">מרכזי קניות</label>
                    </div>
                    
                    <div id="nightlife-types" class="sub-form" style="display: none;">
                        <label>חיי לילה:</label>
                        <label><input type="checkbox" name="nightlife[]" value="Dance Club">דאנס בר</label>
                        <label><input type="checkbox" name="nightlife[]" value="Bar">בר</label>
                        <label><input type="checkbox" name="nightlife[]" value="Casino">קזינו</label>
                    </div>
                    
                    <div id="shows-types" class="sub-form" style="display: none;">
                        <label>מופעים והצגות:</label>
                        <label><input type="checkbox" name="shows[]" value="Cinema">קולנוע</label>
                        <label><input type="checkbox" name="shows[]" value="Musical">מחזות זמר</label>
                        <label><input type="checkbox" name="shows[]" value="Play">הצגות</label>
                        <label><input type="checkbox" name="shows[]" value="Opera">אופרה</label>
                        <label><input type="checkbox" name="shows[]" value="Circus">קרקס</label>
                    </div>
                    
                    <div id="restaurant-types" class="sub-form" style="display: none;">
                        <label>סוגי מסעדות:</label>
                        <label><input type="checkbox" name="restaurants[]" value="Italian">איטלקי</label>
                        <label><input type="checkbox" name="restaurants[]" value="Chinese">סיני</label>
                        <label><input type="checkbox" name="restaurants[]" value="Thai">תאילנדי</label>
                        <label><input type="checkbox" name="restaurants[]" value="Meats">בשרי</label>
                        <label><input type="checkbox" name="restaurants[]" value="Cafes">בתי קפה</label>
                        <label><input type="checkbox" name="restaurants[]" value="Vegan">טבעוני</label>
                        <label><input type="checkbox" name="restaurants[]" value="Indian">הודי</label>
                        <label><input type="checkbox" name="restaurants[]" value="Mediterranean">ים תיכוני</label>
                        <label><input type="checkbox" name="restaurants[]" value="Fast_food">אוכל מהיר</label>
                        <label><input type="checkbox" name="restaurants[]" value="Sweets">מתוקים</label>
                        <label><input type="checkbox" name="restaurants[]" value="Fish">דגים</label>
                        <label><input type="checkbox" name="restaurants[]" value="Mexican">מקסיקני</label>
                    </div>
                </div>
                
                <div class="form-footer">
                    <button type="submit">יצירת טיול</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function toggleSubForm(category) {
            // Get all sub-forms
            var subForms = document.querySelectorAll('.sub-form');
        
            // Hide all sub-forms except the one with the given category
            subForms.forEach(function(subForm) {
                if (subForm.id === category) {
                    subForm.style.display = 'block';
                } else {
                    subForm.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
