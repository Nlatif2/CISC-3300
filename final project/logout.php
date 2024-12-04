<?php
session_start();
$_SESSION = [];
session_destroy();
header("Location: index.php?message=You have been logged out successfully.");
exit();
?>