<?php

try {
    // Create an expectation by checking for a specific condition
    $number = 10;

    // Let's say we expect $number to be greater than 20
    if ($number <= 20) {
        // If this condition is met, throw an exception
        throw new Exception("The number must be greater than 20. Given: $number");
    }

    // If the condition is met, this line will not execute
    echo "The number is valid.";

} catch (Exception $e) {
    // Custom message in the catch block
    echo "Error: " . $e->getMessage();
}

?>
