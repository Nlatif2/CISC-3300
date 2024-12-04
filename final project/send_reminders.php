<?php
require_once 'config/db_connect.php';
require_once 'send_email.php';

// Function to send reminders for events happening tomorrow
function sendEventReminders() {
    global $conn;

    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    
    // Fetch events happening tomorrow
    $sql = "SELECT e.*, u.email as creator_email 
            FROM events e 
            JOIN users u ON e.created_by = u.id 
            WHERE e.date = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $tomorrow);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($event = mysqli_fetch_assoc($result)) {
        // Send reminder to event creator
        $subject = "Reminder: Your event '" . $event['title'] . "' is tomorrow";
        $body = "Your event '" . $event['title'] . "' is scheduled for tomorrow at " . $event['time'] . ". Don't forget to prepare!";
        sendEmail($event['creator_email'], $subject, $body);

        // Send reminders to RSVPd users
        $rsvp_sql = "SELECT u.email 
                     FROM rsvps r 
                     JOIN users u ON r.user_id = u.id 
                     WHERE r.event_id = ?";
        $rsvp_stmt = mysqli_prepare($conn, $rsvp_sql);
        mysqli_stmt_bind_param($rsvp_stmt, "i", $event['id']);
        mysqli_stmt_execute($rsvp_stmt);
        $rsvp_result = mysqli_stmt_get_result($rsvp_stmt);

        while ($rsvp = mysqli_fetch_assoc($rsvp_result)) {
            $subject = "Reminder: Event '" . $event['title'] . "' is tomorrow";
            $body = "The event '" . $event['title'] . "' you RSVP'd for is scheduled for tomorrow at " . $event['time'] . ". We look forward to seeing you!";
            sendEmail($rsvp['email'], $subject, $body);
        }

        mysqli_stmt_close($rsvp_stmt);
    }

    mysqli_stmt_close($stmt);
}

// Run the reminder function
sendEventReminders();

mysqli_close($conn);