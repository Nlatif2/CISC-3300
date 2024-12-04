<?php
session_start();
require_once '../config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $event_id = intval($_POST['event_id']);
    $user_id = $_SESSION['user_id'];

    // Check if the event has reached max attendees
    $check_sql = "SELECT COUNT(*) as rsvp_count, max_attendees FROM events e LEFT JOIN rsvps r ON e.id = r.event_id WHERE e.id = ? GROUP BY e.id";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "i", $event_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    $event_data = mysqli_fetch_assoc($check_result);

    if ($event_data['rsvp_count'] < $event_data['max_attendees']) {
        $sql = "INSERT INTO rsvps (event_id, user_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $event_id, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["success" => true, "message" => "RSVP successful"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error creating RSVP"]);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(["success" => false, "message" => "Event is at full capacity"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
}
?>