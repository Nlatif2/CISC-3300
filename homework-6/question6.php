<?php
$person = [
    "name" => "Nafisa Latif",
    "age" => 23,
    "occupation" => "Student"
];

foreach ($person as $key => $value) {
    echo "The $key is $value.<br>";
}

echo "<br>";

function greetPerson(string $name, int $age = 25): string {
    return "Hello, $name! You are $age years old.<br>";
}

echo greetPerson("Jane Smith", 28); 
echo greetPerson("Tom Johnson"); 
?>
