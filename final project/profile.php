<?php
session_start();
require_once 'config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    die("User not found.");
}

$events_sql = "SELECT * FROM events WHERE created_by = ? ORDER BY date DESC";
$events_stmt = mysqli_prepare($conn, $events_sql);
mysqli_stmt_bind_param($events_stmt, "i", $user_id);
mysqli_stmt_execute($events_stmt);
$events_result = mysqli_stmt_get_result($events_stmt);

mysqli_stmt_close($stmt);
mysqli_stmt_close($events_stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Event Management Platform</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/profile.css">
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
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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

    <!-- Display messages -->
    <?php if (isset($_GET['message'])): ?>
        <div class='alert alert-success'><?php echo htmlspecialchars($_GET['message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class='alert alert-danger'><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <main class="container mt-4">
        <h2>User Profile</h2>
        <div class="card mb-4">
            <div class="card-body">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>

        <h3>Your Events</h3>
        <div class="row">
    <?php if (mysqli_num_rows($events_result) > 0): ?>
        <?php while ($event = mysqli_fetch_assoc($events_result)): ?>
            <?php if (mysqli_num_rows($events_result) <= 2): ?>
                <div class="col-12 mb-4">
            <?php else: ?>
                <div class="col-md-4 mb-4">
            <?php endif; ?>
                    <div class="card">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                            <p class="card-text">
                                <?php echo htmlspecialchars(substr($event['description'], 0, 100)) . '...'; ?></p>
                            <p class="card-text"><small class="text-muted">Date:
                                    <?php echo date('F d, Y', strtotime($event['date'])); ?></small></p>
                        </div>
                        <div class="card-footer p-0 d-flex">
                            <a href="event_details.php?id=<?php echo $event['id']; ?>"
                               class="btn btn-view flex-grow-1">Details</a>
                            <a href="edit_event.php?id=<?php echo $event['id']; ?>"
                               class="btn btn-edit flex-grow-1">Edit</a>
                            <form action="delete_event.php?id=<?php echo $event['id']; ?>" method="post"
                                  class="flex-grow-1">
                                <button type="submit"
                                        onclick="return confirm('Are you sure you want to delete this event?');"
                                        class="btn btn-delete w-100 h-100">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12">
            <p>You haven't created any events yet.</p>
        </div>
    <?php endif; ?>
</div>
    </main>
    <script src="//code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="//stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

<?php
mysqli_close($conn);
?>