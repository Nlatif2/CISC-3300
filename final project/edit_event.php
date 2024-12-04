<?php
session_start();
require_once 'config/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

function getCategoryId($categoryName) {
    $categories = [
        'Music' => 0,
        'Tech' => 1,
        'Food' => 2,
        'Art' => 3,
        'Occasion' => 4,
        'Sports' => 5
    ];
    return isset($categories[$categoryName]) ? $categories[$categoryName] : null;
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

// Fetch event details
$sql = "SELECT * FROM events WHERE id = ? AND created_by = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $event_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$event = mysqli_fetch_assoc($result);

// If event doesn't exist or doesn't belong to the user, redirect
if (!$event) {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $categoryId = getCategoryId($category);
    $max_attendees = intval($_POST['max_attendees']);

    // Handle file upload
    $image_path = $event['image_path']; // Keep existing image path by default
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $file_extension = pathinfo($_FILES["event_image"]["name"], PATHINFO_EXTENSION);
        $new_filename = "event_" . $event_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;

        // Check if the file is a valid image
        $check = getimagesize($_FILES["event_image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["event_image"]["tmp_name"], $target_file)) {
                $image_path = $target_file; // Update image path if a new image is uploaded
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "File is not an image.";
        }
    }

    // Update event in database
    $update_sql = "UPDATE events SET title = ?, description = ?, date = ?, time = ?, location = ?, category = ?, max_attendees = ?, image_path = ? WHERE id = ? AND created_by = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "sssssiisii", $title, $description, $date, $time, $location, $categoryId, $max_attendees, $image_path, $event_id, $user_id);

    if (mysqli_stmt_execute($update_stmt)) {
        header("Location: event_details.php?id=" . $event_id . "&message=Event updated successfully!");
        exit();
    } else {
        echo "Error updating event: " . mysqli_error($conn);
    }
    mysqli_stmt_close($update_stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Event Management Platform</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/edit_event.css">
    <link rel="stylesheet" href="css/fonts.css">
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
    <div class="container mt-5">
        <h2>Edit Event</h2>
        <?php if (isset($error_message)) echo "<p class='text-danger'>$error_message</p>"; ?>
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Event Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($event['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($event['date']); ?>" required>
            </div>
            <div class="form-group">
                <label for="time">Time</label>
                <input type="time" class="form-control" id="time" name="time" value="<?php echo htmlspecialchars($event['time']); ?>" required>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" required>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select class="form-control" id="category" name="category" required>
                    <option value="">Select a category</option>
                    <?php
                    $categories = ['Music', 'Tech', 'Food', 'Art', 'Occasion', 'Sports'];
                    foreach ($categories as $cat) {
                        $selected = (getCategoryName($event['category']) == $cat) ? 'selected' : '';
                        echo "<option value=\"$cat\" $selected>$cat</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="max_attendees">Maximum Attendees</label>
                <input type="number" class="form-control" id="max_attendees" name="max_attendees" value="<?php echo htmlspecialchars($event['max_attendees']); ?>" required min="1">
            </div>
            <div class="form-group">
                <label for="event_image">Upload New Event Image (optional):</label>
                <input type="file" class="form-control-file" id="event_image" name="event_image" accept=".jpg,.jpeg,.png,.gif">
                <?php if (!empty($event['image_path'])): ?>
                    <p>Current Image:</p>
                    <img src="<?php echo htmlspecialchars($event['image_path']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" style='max-width: 200px; height: auto;' class='mb-3'>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Update Event</button>
            <a href='event_details.php?id=<?php echo $event_id; ?>' class='btn btn-secondary mt-3'>Cancel</a>
        </form>
    </div>

    <script src="//code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="//stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
mysqli_close($conn);
?>