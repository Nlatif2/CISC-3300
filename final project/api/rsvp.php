<?php
require_once '../config/db_connect.php';
require_once '../send_email.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;

    // Check if the user has already RSVP'd
    $check_sql = "SELECT * FROM rsvps WHERE user_id = ? AND event_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $event_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($check_result) > 0) {
        echo "You have already RSVP'd for this event.";
    } else {
        // Insert RSVP
        $sql = "INSERT INTO rsvps (user_id, event_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $event_id);

        if (mysqli_stmt_execute($stmt)) {
            // Fetch event details
            $event_sql = "SELECT * FROM events WHERE id = ?";
            $event_stmt = mysqli_prepare($conn, $event_sql);
            mysqli_stmt_bind_param($event_stmt, "i", $event_id);
            mysqli_stmt_execute($event_stmt);
            $event_result = mysqli_stmt_get_result($event_stmt);
            $event = mysqli_fetch_assoc($event_result);

            // Fetch user email
            $user_sql = "SELECT email FROM users WHERE id = ?";
            $user_stmt = mysqli_prepare($conn, $user_sql);
            mysqli_stmt_bind_param($user_stmt, "i", $user_id);
            mysqli_stmt_execute($user_stmt);
            $user_result = mysqli_stmt_get_result($user_stmt);
            $user = mysqli_fetch_assoc($user_result);

            // Send confirmation email
            $to = $user['email'];
            $subject = "RSVP Confirmation for " . $event['title'];
            $body = "You have successfully RSVP'd for the event: " . $event['title'] . " on " . $event['date'] . " at " . $event['time'];
            sendEmail($to, $subject, $body);

            echo "RSVP successful!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_stmt_close($check_stmt);
} else {
    echo "Invalid request";
}

mysqli_close($conn);