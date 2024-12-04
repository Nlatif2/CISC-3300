<?php
session_start();
require_once 'config/db_connect.php';
require_once 'send_email.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error_message = '';
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check_user_sql = "SELECT * FROM users WHERE username = ?";
    $check_user_stmt = mysqli_prepare($conn, $check_user_sql);
    mysqli_stmt_bind_param($check_user_stmt, "s", $username);
    mysqli_stmt_execute($check_user_stmt);
    $check_result = mysqli_stmt_get_result($check_user_stmt);

    if (mysqli_num_rows($check_result) > 0) {
        $error_message = "Username already taken. Please choose a different one.";
    } else {
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['user_id'] = mysqli_insert_id($conn);
            $_SESSION['username'] = $username;
            $to = $email;
            $subject = "Welcome to Event Manager";
            $body = "Dear $username,\n\nThank you for registering with Event Manager. Your account has been successfully created.\n\nWelcome aboard!";
            
            if (sendEmail($to, $subject, $body)) {
                header("Location: index.php?message=Registration successful! A confirmation email has been sent.");
            } else {
                header("Location: index.php?message=Registration successful! However, there was an issue sending the confirmation email.");
            }
            exit();
        } else {
            $error_message = "Error during registration. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_stmt_close($check_user_stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Event Management Platform</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/register.css">
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
            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item"><a class="nav-link" href="create_event.php">Create Event</a></li>
                <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<div class="register-container">
        <div class="register-form">
            <h2 class="register-title">Create Account</h2>
            <?php if (isset($error_message)) echo "<p class='error-message'>$error_message</p>"; ?>
            <form method="post" action="">
                <div class="form-group">
                    <input type="text" class="form-control" id="username" name="username" required>
                    <label for="username" class="form-label">Username</label>
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" id="email" name="email" required>
                    <label for="email" class="form-label">Email</label>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <label for="password" class="form-label">Password</label>
                </div>
                <button type="submit" class="btn btn-register">Register</button>
            </form>
            <p class='login-link'>Already have an account? <a href='login.php'>Sign in</a></p>
        </div>
    </div>

    <script src="//code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="//stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
mysqli_close($conn);
?>