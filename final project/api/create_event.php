<?php
session_start();
require_once '../config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $max_attendees = intval($_POST['max_attendees']);
    $created_by = $_SESSION['user_id'];

    $sql = "INSERT INTO events (title, description, date, time, location, category, max_attendees, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssii", $title, $description, $date, $time, $location, $category, $max_attendees, $created_by);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["success" => true, "message" => "Event created successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error creating event"]);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
}
?>