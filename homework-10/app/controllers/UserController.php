<?php

namespace app\controllers;

use app\models\User;

class UserController
{
    public function validateUser($inputData) {
        $errors = [];
        $firstName = $inputData['firstName'];
        $lastName = $inputData['lastName'];

        if ($firstName) {
            $firstName = htmlspecialchars($firstName, ENT_QUOTES|ENT_HTML5, 'UTF-8', true);
            if (strlen($firstName) < 2) {
                $errors['firstNameShort'] = 'first name is too short';
            }
        } else {
            $errors['requiredFirstName'] = 'first name is required';
        }

        if ($lastName) {
            $lastName = htmlspecialchars($lastName, ENT_QUOTES|ENT_HTML5, 'UTF-8', true);
            if (strlen($lastName) < 2) {
                $errors['lastNameShort'] = 'last name is too short';
            }
        } else {
            $errors['requiredLastName'] = 'last name is required';
        }

        if (count($errors)) {
            http_response_code(400);
            echo json_encode($errors);
            exit();
        }
        return [
            'firstName' => $firstName,
            'lastName' => $lastName,
        ];
    }

    public function getAllUsers() {
        $userModel = new User();
        header("Content-Type: application/json");
        $users = $userModel->getAllUsers();

        echo json_encode($users);
        exit();
    }

    public function getUserByID($id) {
        $userModel = new User();
        header("Content-Type: application/json");
        $user = $userModel->getUserById($id);
        echo json_encode($user);
        exit();
    }

    public function saveUser() {
        $inputData = [
            'firstName' => $_POST['firstName'] ? $_POST['firstName'] : false,
            'lastName' => $_POST['lastName'] ? $_POST['lastName'] : false,
        ];
        $userData = $this->validateUser($inputData);

        $user = new User();
        $user->saveUser(
            [
                'firstName' => $userData['firstName'],
                'lastName' => $userData['lastName'],
            ]
        );

        http_response_code(200);
        echo json_encode([
            'success' => true
        ]);
        exit();
    }

    public function updateUser($id) {
        if (!$id) {
            http_response_code(404);
            exit();
        }

        //no built-in super global for PUT
        parse_str(file_get_contents('php://input'), $_PUT);

        $inputData = [
            'firstName' => $_PUT['firstName'] ? $_PUT['firstName'] : false,
            'lastName' => $_PUT['lastName'] ? $_PUT['lastName'] : false,
        ];
        $userData = $this->validateUser($inputData);
        //we could save the data off to be saved here

        $user = new User();
        $user->updateUser(
            [
                'id' => $id,
                'firstName' => $userData['firstName'],
                'lastName' => $userData['lastName'],
            ]
        );

        http_response_code(200);
        echo json_encode([
            'success' => true
        ]);
        exit();
    }

    public function deleteUser($id) {
        if (!$id) {
            http_response_code(404);
            exit();
        }

        $user = new User();
        $user->deleteUser(
            [
                'id' => $id,
            ]
        );

        http_response_code(200);
        echo json_encode([
            'success' => true
        ]);
        exit();
    }

    public function usersView() {
        include __DIR__ . '/../../public/assets/views/user/users-view.html';
        exit();
    }

    public function usersAddView() {
        include __DIR__ . '/../../public/assets/views/user/users-add.html';
        exit();
    }

    public function usersDeleteView() {
        include __DIR__ . '/../../public/assets/views/user/users-delete.html';
        exit();
    }

    public function usersUpdateView() {
        include __DIR__ . '/../../public/assets/views/user/users-update.html';
        exit();
    }
}