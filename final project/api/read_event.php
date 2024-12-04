<?php
require_once '../config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT * FROM events";
    $result = mysqli_query($conn, $sql);

    $events = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }

    echo json_encode($events);
}
?>