<?php
require_once 'models/User.php';
require_once 'controllers/UserController.php';

use controllers\UserController;

$controller = new UserController();
$controller->index();
?>
