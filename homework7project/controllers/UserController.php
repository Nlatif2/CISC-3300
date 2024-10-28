<?php
require_once __DIR__ . '/../models/UserModel.php';

class UserController {
    public function showUserPage() {
        include __DIR__ . '/../views/users.html';
    }
    public function getUsersJson() {
        $userModel = new UserModel();
        $users = $userModel->getAllUsers();
        header('Content-Type: application/json');
        echo json_encode($users);
    }
}
?>
