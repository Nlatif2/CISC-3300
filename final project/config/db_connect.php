<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', ''); //Enter your username
define('DB_PASSWORD', ''); //Create or Enter your password
define('DB_NAME', ''); //Your Database name

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>