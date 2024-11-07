<?php
require "../app/models/User.php";
require "../app/controllers/UserController.php";
require_once '../app/controllers/PostController.php';


use app\controllers\UserController;

//get URI without query string
$url = strtok($_SERVER["REQUEST_URI"], '?');

//split url into array
$uriArray = explode("/", $url);

if ($uriArray[1] === 'api' && $uriArray[2] === 'users' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $userController = new UserController();
    $userController->getUsers();
}

if ($uriArray[1] === 'users' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require './views/users.html';
}

if ($uriArray[1] === 'api' && $uriArray[2] === 'users' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $userController = new UserController();
    $userController->saveUser();
}

if ($uriArray[1] === 'add-users' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require './views/add-users.html';
}

if ($_SERVER['REQUEST_URI'] == '/posts' && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $controller = new PostController();
    $controller->index();
} elseif ($_SERVER['REQUEST_URI'] == '/posts/create' && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $controller = new PostController();
    $controller->create();
} elseif ($_SERVER['REQUEST_URI'] == '/posts/store' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller = new PostController();
    $controller->store();
}
