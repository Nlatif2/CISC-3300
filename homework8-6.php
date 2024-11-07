<?php

try {
    $number = 10;
    if ($number <= 20) {
        throw new Exception("The number must be greater than 20. Given: $number");
    }
    echo "The number is valid.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>
