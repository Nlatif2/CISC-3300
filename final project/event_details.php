<?php
session_start();
require_once 'config/db_connect.php';

$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT e.*, u.username as creator_name FROM events e JOIN users u ON e.created_by = u.id WHERE e.id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$event = mysqli_fetch_assoc($result);

if (!$event) {
    die("Event not found");
}

// Get RSVP count
$rsvp_sql = "SELECT COUNT(*) as rsvp_count FROM rsvps WHERE event_id = ?";
$rsvp_stmt = mysqli_prepare($conn, $rsvp_sql);
mysqli_stmt_bind_param($rsvp_stmt, "i", $event_id);
mysqli_stmt_execute($rsvp_stmt);
$rsvp_result = mysqli_stmt_get_result($rsvp_stmt);
$rsvp_count = mysqli_fetch_assoc($rsvp_result)['rsvp_count'];

mysqli_stmt_close($stmt);
mysqli_stmt_close($rsvp_stmt);

$user_rsvp = false;
if (isset($_SESSION['user_id'])) {
    $check_rsvp_sql = "SELECT * FROM rsvps WHERE event_id = ? AND user_id = ?";
    $check_rsvp_stmt = mysqli_prepare($conn, $check_rsvp_sql);
    mysqli_stmt_bind_param($check_rsvp_stmt, "ii", $event_id, $_SESSION['user_id']);
    mysqli_stmt_execute($check_rsvp_stmt);
    $check_rsvp_result = mysqli_stmt_get_result($check_rsvp_stmt);
    $user_rsvp = mysqli_num_rows($check_rsvp_result) > 0;
    mysqli_stmt_close($check_rsvp_stmt);
}

// Fetch attendees if the user is the creator of the event
$attendees = [];
if ($event['created_by'] == $_SESSION['user_id']) {
    $attendees_sql = "SELECT u.username, u.email FROM rsvps r JOIN users u ON r.user_id = u.id WHERE r.event_id = ?";
    $attendees_stmt = mysqli_prepare($conn, $attendees_sql);
    mysqli_stmt_bind_param($attendees_stmt, "i", $event_id);
    mysqli_stmt_execute($attendees_stmt);
    $attendees_result = mysqli_stmt_get_result($attendees_stmt);

    while ($row = mysqli_fetch_assoc($attendees_result)) {
        $attendees[] = $row;
    }
    mysqli_stmt_close($attendees_stmt);
}

function getCategoryName($categoryId) {
    $categories = [
        0 => 'Music',
        1 => 'Tech',
        2 => 'Food',
        3 => 'Art',
        4 => 'Occasion',
        5 => 'Sports'
    ];
    return isset($categories[$categoryId]) ? $categories[$categoryId] : 'Unknown';
}

// Geocode the event location
$address = urlencode($event['location']);
$geocode_url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=";
$response = file_get_contents($geocode_url);
$response_data = json_decode($response);

if ($response_data->status == 'OK') {
    $latitude = $response_data->results[0]->geometry->location->lat;
    $longitude = $response_data->results[0]->geometry->location->lng;
} else {
    $latitude = 40.7128;
    $longitude = -74.0060;
}

// Get current URL for sharing
$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['title']); ?> - Event Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/event_details.css">
    <link rel="stylesheet" href="css/fonts.css">
    <script src="js/event_details.js">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css' rel='stylesheet' />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js'></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg" id="navbarID">
    <a id="webTitle" class="navbar-brand" href="index.php">Event Manager</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php" data-text="Home">
                    <i class="fas fa-home fa-lg"></i>
                    <span class="nav-text">Home</span>
                </a>
            </li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="create_event.php" data-text="Create Event">
                        <i class="fas fa-plus fa-lg"></i>
                        <span class="nav-text">Create Event</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php" data-text="Profile">
                        <i class="fas fa-user fa-lg"></i>
                        <span class="nav-text">Profile</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php" data-text="Logout">
                        <i class="fas fa-sign-out-alt fa-lg"></i>
                        <span class="nav-text">Logout</span>
                    </a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="login.php" data-text="Login">
                        <i class="fas fa-sign-in-alt fa-lg"></i>
                        <span class="nav-text">Login</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.php" data-text="Register">
                        <i class="fas fa-user-plus fa-lg"></i>
                        <span class="nav-text">Register</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<main class="container mt-4">
    <div class="event-details">
        <div class="event_information">
            <h1 class="event_title"><?php echo htmlspecialchars($event['title']); ?></h1>
            <p class="event-description lead"><?php echo htmlspecialchars($event['description']); ?></p>
            <p class="EvDet"><strong>Date:</strong> <?php echo date('F d, Y', strtotime($event['date'])); ?></p>
            <p class="EvDet"><strong>Time:</strong> <?php echo date('g:i A', strtotime($event['time'])); ?></p>
            <p class="EvDet"><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
            <p class="EvDet"><strong>Category:</strong> <?php echo htmlspecialchars(getCategoryName($event['category'])); ?></p>
            <p class="EvDet"><strong>Max Attendees:</strong> <?php echo $event['max_attendees']; ?></p>
            <p class="EvDet"><strong>Current RSVPs:</strong> <?php echo $rsvp_count; ?></p>
            <p class="EvDet"><strong>Created by:</strong> <?php echo htmlspecialchars($event['creator_name']); ?></p>
        </div>

        <div class="event_img"> 
            <?php if (!empty($event['image_path'])): ?>
                <img src="<?php echo htmlspecialchars($event['image_path']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" style='max-width: 100%; height: auto;' class='mb-3'>
            <?php else: ?>
                <p>No image available for this event.</p>
            <?php endif; ?>
        </div>
    </div>
            <!-- Map integration -->
            <div id="map" style="height: 400px; width: 100%;" class="mb-3"></div>
<div class="rsvp_share">
<!-- RSVP Button -->
<?php if ($event['created_by'] != $_SESSION['user_id']): ?>
    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($user_rsvp): ?>
            <button class="btn btn-success mt-3" disabled>You're already RSVP'd</button>
        <?php else: ?>
            <button id="rsvp-btn" class="btn btn-primary mt-3">RSVP</button>
        <?php endif; ?>
    <?php else: ?>
        <a href="login.php" class='btn btn-primary mt-3'>Login to RSVP</a>
    <?php endif; ?>
<?php endif; ?>
     <div class="share-buttons mt-3">
            <h5>Share this event:</h5>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($current_url); ?>" target="_blank" class="btn btn-primary btn-sm">
                <i class="fab fa-facebook-f"></i> Facebook
            </a>
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($current_url); ?>&text=<?php echo urlencode($event['title']); ?>" target="_blank" class="btn btn-dark btn-sm">
                <i class="fab fa-x-twitter"></i> X (Formerly Twitter)
            </a>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($current_url); ?>&title=<?php echo urlencode($event['title']); ?>" target="_blank" class="btn btn-secondary btn-sm">
                <i class="fab fa-linkedin-in"></i> LinkedIn
            </a>
        </div>

        <?php if ($event['created_by'] == $_SESSION['user_id']): ?>
            <h3>Attendees:</h3>
            <?php if (!empty($attendees)): ?>
                <ul class='list-group'>
                    <?php foreach ($attendees as $attendee): ?>
                        <li class='list-group-item'><?php echo htmlspecialchars($attendee['username']); ?> (<?php echo htmlspecialchars($attendee['email']); ?>)</li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No attendees have RSVP'd yet.</p>
            <?php endif; ?>
        <?php endif; ?>
            </div>
</main>

<script src="//code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="//stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
function initMap() {
    const eventLocation = { lat: <?php echo $latitude; ?>, lng: <?php echo $longitude; ?> };
    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 15,
        center: eventLocation,
    });
    
    const marker = new google.maps.marker.AdvancedMarkerElement({
        map: map,
        position: eventLocation,
        title: "<?php echo htmlspecialchars($event['title']); ?>"
    });
}

function loadGoogleMapsScript() {
    var script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key="";
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
}

$(document).ready(function() {
    loadGoogleMapsScript();

    $('#rsvp-btn').click(function() {
        $.ajax({
            url: 'api/rsvp.php',
            method: 'POST',
            data: { event_id: <?php echo $event_id; ?> },
            success: function(response) {
                alert('RSVP successful!');
                location.reload();
            },
            error: function(xhr) {
                alert('Error RSVPing: ' + xhr.responseText);
            }
        });
    });
});
</script>

</body>
</html>

<?php
mysqli_close($conn);
?>