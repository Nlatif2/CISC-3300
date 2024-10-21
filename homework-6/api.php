<?php
$person = [
    "name" => "Nafisa Latif",
    "age" => 23,
    "occupation" => "Student"
];

header('Content-Type: application/json');
echo json_encode($person);
?>
