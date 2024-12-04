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

// Fetch event details to ensure it belongs to the user
$sql = "SELECT * FROM events WHERE id = ? AND created_by = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $event_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$event = mysqli_fetch_assoc($result);

// If event doesn't exist or doesn't belong to the user, redirect
if (!$event) {
    header("Location: profile.php?error=Event not found or you do not have permission to delete it.");
    exit();
}

// Delete all RSVPs associated with the event first
$rsvp_sql = "DELETE FROM rsvps WHERE event_id = ?";
$rsvp_stmt = mysqli_prepare($conn, $rsvp_sql);
mysqli_stmt_bind_param($rsvp_stmt, "i", $event_id);
mysqli_stmt_execute($rsvp_stmt);
mysqli_stmt_close($rsvp_stmt);

$delete_sql = "DELETE FROM events WHERE id = ? AND created_by = ?";
$delete_stmt = mysqli_prepare($conn, $delete_sql);
mysqli_stmt_bind_param($delete_stmt, "ii", $event_id, $user_id);

if (mysqli_stmt_execute($delete_stmt)) {
    header("Location: profile.php?message=Event deleted successfully.");
} else {
    header("Location: profile.php?error=Error deleting event. Please try again.");
}

mysqli_stmt_close($delete_stmt);
mysqli_close($conn);
?>