<?php
session_start();
require_once 'config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Event Management Platform</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css' rel='stylesheet' />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js'></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/login.css">
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
<div class="login-container">
    <div class="login-form">
        <h2 class="login-title">Welcome Back</h2>
        <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
        <form method="post" action="">
            <div class="form-group">
                <input type="text" class="form-control" id="username" name="username" required>
                <label for="username" class="form-label">Username</label>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" id="password" name="password" required>
                <label for="password" class="form-label">Password</label>
            </div>
            <button type="submit" class="btn btn-login">Login</button>
        </form>
        <p class="register-link">Don't have an account? <a href="register.php">Sign up</a></p>
    </div>
</div>
</body>
</html>