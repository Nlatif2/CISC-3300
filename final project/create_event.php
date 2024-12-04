<?php
session_start();
require_once 'config/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error_message = '';
    $title = isset($_POST['title']) ? mysqli_real_escape_string($conn, $_POST['title']) : '';
    $description = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : '';
    $date = isset($_POST['date']) ? mysqli_real_escape_string($conn, $_POST['date']) : '';
    $time = isset($_POST['time']) ? mysqli_real_escape_string($conn, $_POST['time']) : '';
    $location = isset($_POST['location']) ? mysqli_real_escape_string($conn, $_POST['location']) : '';
    $category = isset($_POST['category']) ? mysqli_real_escape_string($conn, $_POST['category']) : '';
    $max_attendees = isset($_POST['max_attendees']) ? intval($_POST['max_attendees']) : 0;

    $categoryId = getCategoryId($category);

    if ($categoryId === null) {
        die("Invalid category selected.");
    }

    // Check if any required fields are empty
    if (empty($title) || empty($description) || empty($date) || empty($time) || empty($location) || empty($category) || $max_attendees <= 0) {
        die("All fields are required.");
    }

    // Handle file upload
    $image_path = 'uploads/';
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["event_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is a valid image
        $check = getimagesize($_FILES["event_image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["event_image"]["tmp_name"], $target_file)) {
                $image_path = htmlspecialchars($target_file);
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "File is not an image.";
        }
    }

    // Insert event into database
    $sql = "INSERT INTO events (title, description, date, time, location, category, max_attendees, created_by, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssiiss", $title, $description, $date, $time, $location, $categoryId, $max_attendees, $_SESSION['user_id'], $image_path);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php?message=Event created successfully!");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - Event Management Platform</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/create_event.css">
    <link rel="stylesheet" href="css/fonts.css">
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
    <div class="container mt-5">
    <h2>Create Event</h2>
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Event Title:</label>
            <input type="text" class="form-control" id="title" name="title" required placeholder="Title">
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" rows="3" required placeholder="Description"></textarea>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" class="form-control" id="date" name="date" required>
        </div>
        <div class="form-group">
            <label for="time">Time:</label>
            <input type="time" class="form-control" id="time" name="time" required>
        </div>
        <div class="form-group">
            <label for="location">Location:</label>
            <input type="text" class="form-control" id="location" name="location" required placeholder="Location">
        </div>
        <div class="form-group">
            <label for="category">Category:</label>
            <select class="form-control" id="category" name="category" required>
                <option value="">Select a category</option>
                <option value="Music">Music</option>
                <option value="Tech">Tech</option>
                <option value="Food">Food</option>
                <option value="Art">Art</option>
                <option value="Occasion">Occasion</option>
                <option value="Sports">Sports</option>
            </select>
        </div>
        <div class="form-group">
            <label for="max_attendees">Maximum Attendees:</label>
            <input type="number" class="form-control" id="max_attendees" name="max_attendees" required min="1">
        </div>
        <div class="form-group">
            <label for="event_image">Upload Event Image:</label>
            <input type="file" class="form-control-file" id="event_image" name="event_image" accept=".jpg,.jpeg,.png,.gif" onchange="previewImage(event)">
        </div>
        <img id="image_preview" src="#" alt="" class='image-preview' style='display:none;' />

        <button id="CButton" type="submit" class="btn btn-primary">Create Event</button>
        <br>
        <br>
    </form>
</div>

<script src="//code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="//stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
// Function to preview uploaded image
function previewImage(event) {
    const imagePreview = document.getElementById('image_preview');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
        }
        
        reader.readAsDataURL(file);
    } else {
        imagePreview.style.display = 'none';
    }
}
</script>


</body>
</html>

<?php
mysqli_close($conn);
?>