<?php
// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection settings
$servername = "localhost";
$username = "zlilma_admin_smy";
$password = "easy_trip123";
$dbname = "zlilma_Easy_Trip";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get trip number and email from query parameters, with error handling
$trip_num = $_GET['trip_num'] ?? null;
$email = $_GET['email'] ?? null;

if (!$trip_num) {
    die("trip_num is required.");
}

// get the hotel name
$hotel_query = $conn->prepare("SELECT hotel FROM Trips WHERE trip_num = ?");
if ($hotel_query) {
    $hotel_query->bind_param("i", $trip_num);
    $hotel_query->execute();
    $hotel_result = $hotel_query->get_result();
    $hotel = $hotel_result->fetch_assoc()['hotel'] ?? '';
    $hotel_query->close();
} else {
    die("Hotel query preparation failed: " . htmlspecialchars($conn->error));
}

// Fetch trip dates
$dates_query = $conn->prepare("SELECT date_start, date_end FROM Trips WHERE trip_num = ?");
if ($dates_query) {
    $dates_query->bind_param("i", $trip_num);
    $dates_query->execute();
    $dates_result = $dates_query->get_result();
    $dates = $dates_result->fetch_assoc();
    $date_start = new DateTime($dates['date_start']);
    $date_end = new DateTime($dates['date_end']);
    $trip_duration = $date_start->diff($date_end)->days + 1; // +1 to include the end date
    $dates_query->close();
} else {
    die("Dates query preparation failed: " . htmlspecialchars($conn->error));
}

// Fetch the destination
$dest_query = $conn->prepare("SELECT destination FROM Trips WHERE trip_num = ?");
if ($dest_query) {
    $dest_query->bind_param("i", $trip_num);
    $dest_query->execute();
    $dest_result = $dest_query->get_result();
    $destination = $dest_result->fetch_assoc()['destination'] ?? '';
    $dest_query->close();
} else {
    die("Destination query preparation failed: " . htmlspecialchars($conn->error));
}

// Fetch the attractions for the route
$route_query = $conn->prepare("SELECT Attraction_Number, day_trip FROM Routes_trip WHERE trip_num = ? ORDER BY day_trip");
if ($route_query) {
    $route_query->bind_param("i", $trip_num);
    $route_query->execute();
    $route_result = $route_query->get_result();

    $routes = [];
    while ($row = $route_result->fetch_assoc()) {
        $routes[$row['day_trip']][] = $row['Attraction_Number'];
    }
    $route_query->close();
} else {
    die("Route query preparation failed: " . htmlspecialchars($conn->error));
}

if (!empty($routes)) {
    $attraction_numbers = array_unique(array_merge(...array_values($routes)));
    if (!empty($attraction_numbers)) {
        $placeholders = implode(',', array_fill(0, count($attraction_numbers), '?'));
        $types = str_repeat('i', count($attraction_numbers));
        //getting attraction details
        $attractions_query = $conn->prepare("SELECT Attraction_Number, Attraction_Name, Average_Time, Latitude, Longitude FROM Attraction_Data WHERE Attraction_Number IN ($placeholders)");
        if ($attractions_query) {
            $attractions_query->bind_param($types, ...$attraction_numbers);
            $attractions_query->execute();
            $attractions_result = $attractions_query->get_result();

            $attractions = [];
            while ($row = $attractions_result->fetch_assoc()) {
                $attractions[$row['Attraction_Number']] = $row;
            }
            $attractions_query->close();
        } else {
            die("Attractions query preparation failed: " . htmlspecialchars($conn->error));
        }
    }
}

// Query to get trip details
$sql = "SELECT date_start, date_end, destination FROM Trips WHERE trip_num=$trip_num";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$start_date = $row['date_start'];
$end_date = $row['date_end'];
$destination = $row['destination'];

// Format the dates to dd/mm/yyyy
$start_date_formatted = (new DateTime($start_date))->format('d/m/Y');
$end_date_formatted = (new DateTime($end_date))->format('d/m/Y');

$conn->close();
?>
<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>הצגת המסלול</title>
    <link rel="stylesheet" href="home_page.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA4yYlTLqQIJXb6Wn8uEKo4tUT23mR9ckw&libraries=places&callback=initMap" async defer></script>
    <style>
        button {
            background-color: #04274de8;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            transition: background-color 0.3s;
            margin-top: 10px;
            margin-right: 5px;
            width: 80px;
            text-align: center;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div id="form1" class="container">
        <div class="logo-image">
            <img src="../images/logo.jpg" alt="Logo">
        </div>
        <h2>הצגת המסלול</h2>
        <h3><?php echo htmlspecialchars($destination) . " " . htmlspecialchars($start_date_formatted) . " - " . htmlspecialchars($end_date_formatted); ?></h3>
        <div class="form-container">
            <div id="map"></div>
            <div id="buttons"></div>
            <div id="info">
                <div id="route-details"></div>
            </div>
            <div class="form-footer">
                <a href="my_trip.php?email=<?php echo urlencode($email); ?>" class="home-button">חזרה לטיולים שלי</a>
            </div>
            <script>
                //getting values of variables from PHP file
                let map, directionsService, directionsRenderer;
                let routes = <?php echo json_encode($routes); ?>;
                let attractions = <?php echo json_encode($attractions); ?>;
                let hotel = "<?php echo $hotel; ?>";
                let hotelLocation = null;
                let destination_City = <?php echo json_encode($destination); ?>;
                const startDate = new Date(<?php echo json_encode($date_start->format('Y-m-d')); ?>);
                const endDate = new Date(<?php echo json_encode($date_end->format('Y-m-d')); ?>);
                const totalDays = <?php echo json_encode($trip_duration); ?>;

                let cities = {
                    "Canberra": { lat: -35.2809, lng: 149.1300, country: 'au' },
                    "New York": { lat: 40.7128, lng: -74.0060, country: 'us' },
                    "Barcelona": { lat: 41.3851, lng: 2.1734, country: 'es' },
                    "London": { lat: 51.5074, lng: -0.1278, country: 'gb' }
                };

                let city = cities[destination_City];

                // initialize the map
                function initMap() {
                    updateMap(destination_City);
                }

                //update the map based on the city name
                function updateMap(destination_City) {
                    // Check if the city is supported
                    if (!cities[destination_City]) {
                        console.error("City not supported");
                        return;
                    }

                    // Get city data from the cities object
                    let city = cities[destination_City];

                    // Create a new map centered on the city
                    map = new google.maps.Map(document.getElementById('map'), {
                        center: { lat: city.lat, lng: city.lng },
                        zoom: 13,
                    });

                    // Create a marker for the city
                    marker = new google.maps.Marker({
                        map: map,
                        position: { lat: city.lat, lng: city.lng },
                        visible: false,
                    });

                    directionsService = new google.maps.DirectionsService();
                    directionsRenderer = new google.maps.DirectionsRenderer();
                    directionsRenderer.setMap(map);

                    geocodeHotel();
                }

                // geocode the hotel location
                function geocodeHotel() {
                    const geocoder = new google.maps.Geocoder();
                    geocoder.geocode({ 'address': hotel }, function(results, status) {
                        if (status === 'OK') {
                            hotelLocation = results[0].geometry.location;
                            createButtons();
                        } else {
                            console.error('Geocode was not successful for the following reason: ' + status);
                            alert('Geocode was not successful for the following reason: ' + status);
                        }
                    });
                }

                //create buttons based on saved tours in the trip
                function createButtons() {
                    const buttonsDiv = document.getElementById('buttons');
                    buttonsDiv.innerHTML = '';
                    for (let day = 0; day < totalDays; day++) {
                        let currentDate = new Date(startDate);
                        currentDate.setDate(currentDate.getDate() + day);
                        let formattedDate = `${currentDate.getDate()}.${currentDate.getMonth() + 1}.${currentDate.getFullYear()}`;
                        let button = document.createElement('button');
                        button.innerHTML = formattedDate;
                        button.classList.add('button'); 
                        button.onclick = () => plotRoute(day + 1); // Correct day calculation for plotting route
                        buttonsDiv.appendChild(button);
                    }
                }

                //plot the route for a given day
                function plotRoute(day) {
                    if (!routes[day]) {
                        console.error('No route found for day', day);
                        return;
                    }

                    let waypoints = routes[day].map(attractionNumber => ({
                        location: new google.maps.LatLng(attractions[attractionNumber].Latitude, attractions[attractionNumber].Longitude),
                        stopover: true
                    }));

                    let request = {
                        origin: hotelLocation,
                        destination: hotelLocation,
                        waypoints: waypoints,
                        travelMode: google.maps.TravelMode.DRIVING
                    };

                    directionsService.route(request, function(result, status) {
                        if (status == 'OK') {
                            directionsRenderer.setDirections(result);
                            displayRouteInfo(result, day);
                        } else {
                            console.error('Directions request failed due to ' + status);
                            alert('Directions request failed due to ' + status);
                        }
                    });
                }

                //display route information
                function displayRouteInfo(result, day) {
                    const routeDetailsDiv = document.getElementById('route-details');
                    routeDetailsDiv.innerHTML = '';

                    const route = result.routes[0];
                    let totalDistance = 0;
                    let totalDuration = 0;
                    const attractionsList = routes[day].map(attractionNumber => ({
                        name: attractions[attractionNumber].Attraction_Name,
                        avgTime: attractions[attractionNumber].Average_Time * 60
                    }));

                    for (let i = 0; i < route.legs.length; i++) {
                        totalDistance += route.legs[i].distance.value;
                        totalDuration += route.legs[i].duration.value;
                    }

                    totalDistance = (totalDistance / 1000).toFixed(2); // Convert to km
                    totalDuration = (totalDuration / 60).toFixed(2); // Convert to minutes

                    const infoHtml = `
                    <h3>פרטי היום ה-${day} לטיול:</h3>
                    <h4>האטרקציות:</h4>
                    <ul style="list-style-type: none;">${attractionsList.map((attraction, index) => `<li>${index + 1}. ${attraction.name} (משך בילוי משוער: ${attraction.avgTime} דקות)</li>`).join('')}</ul>
                    `;
                    routeDetailsDiv.innerHTML = infoHtml;
                    routeDetailsDiv.style.display = 'block';
                }

                window.onload = initMap;
            </script>
        </div>
    </div>
</body>
</html>
