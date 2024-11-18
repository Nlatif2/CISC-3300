<?php
require_once "../app/models/Model.php";
require_once "../app/models/User.php";
require_once "../app/controllers/UserController.php";
// Add these new requires for Posts
require_once "../app/models/Post.php";
require_once "../app/controllers/PostController.php";

$env_file = __DIR__ . '/../.env';
if (file_exists($env_file)) {
    $env = parse_ini_file($env_file);
    if ($env === false) {
        die("Error parsing .env file");
    }
} else {
    die(".env file not found");
}

// Then use the $env array safely
define('DBNAME', $env['hw10DB'] ?? '');
define('DBHOST', $env['DBHOST'] ?? '');
define('DBUSER', $env['DBUSER'] ?? '');
define('DBPASS', $env['DBPASS'] ?? '');

// Test database connection
try {
    // Use port 8889 which is common for MAMP
    $conn = new PDO("mysql:host=".DBHOST.";port=8889;dbname=".DBNAME, DBUSER, DBPASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully to database<br>";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "<br>";
    echo "DBNAME: " . DBNAME . "<br>";
    echo "DBHOST: " . DBHOST . "<br>";
    echo "DBUSER: " . DBUSER . "<br>";
    exit; // Stop execution if database connection fails
}

use app\controllers\UserController;
use app\controllers\PostController;

//get uri without query strings
$uri = strtok($_SERVER["REQUEST_URI"], '?');

//get uri pieces
$uriArray = explode("/", $uri);

// Existing User routes
// ... [Keep all existing User routes as they are] ...

// New Post routes

//get all or a single post
if ($uriArray[1] === 'api' && $uriArray[2] === 'posts' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = isset($uriArray[3]) ? intval($uriArray[3]) : null;
    $postController = new PostController();

    if ($id) {
        $postController->getPostByID($id);
    } else {
        $postController->getAllPosts();
    }
}

//save post
if ($uriArray[1] === 'api' && $uriArray[2] === 'posts' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $postController = new PostController();
    $postController->savePost();
}

//update post
if ($uriArray[1] === 'api' && $uriArray[2] === 'posts' && $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $postController = new PostController();
    $id = isset($uriArray[3]) ? intval($uriArray[3]) : null;
    $postController->updatePost($id);
}

//delete post
if ($uriArray[1] === 'api' && $uriArray[2] === 'posts' && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $postController = new PostController();
    $id = isset($uriArray[3]) ? intval($uriArray[3]) : null;
    $postController->deletePost($id);
}

// Post views

if ($uri === '/posts-add' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $postController = new PostController();
    $postController->postsAddView();
}

if ($uriArray[1] === 'posts-update' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $postController = new PostController();
    $postController->postsUpdateView();
}

if ($uriArray[1] === 'posts-delete' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $postController = new PostController();
    $postController->postsDeleteView();
}

if ($uri === '/posts' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $postController = new PostController();
    $postController->postsView();
}

// Main page route
if (($uriArray[1] === '' || $uriArray[1] === 'public' || $uriArray[1] === 'index.php') && $_SERVER['REQUEST_METHOD'] === 'GET') {
    echo "Main page route matched<br>";
    $userController = new UserController();
    $userController->usersView();
    exit();
}

// User views
if ($uri === '/users-add' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $userController = new UserController();
    $userController->usersAddView();
    exit();
}

if ($uri === '/users-update' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $userController = new UserController();
    $userController->usersUpdateView();
    exit();
}

if ($uri === '/users-delete' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $userController = new UserController();
    $userController->usersDeleteView();
    exit();
}

if ($uri === '/' || $uri === '/users' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $userController = new UserController();
    $userController->usersView();
    exit();
}

// Debug info before not found
echo "No routes matched. Debug info:<br>";
echo "URI Array:<br>";
print_r($uriArray);

// Keep the existing not found route at the end
include '../public/assets/views/notFound.html';
exit();
?>