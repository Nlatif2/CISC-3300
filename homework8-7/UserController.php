<?php

namespace app\controllers;
use app\models\User;

class UserController
{
    public function getUsers() {
        $params = [
            //ternary shorthand, if left if true assign it, and if not assign right
            'name' => $_GET['name'] ?: null,
        ];
        $userModel = new User();
        $users = $userModel->getAllUsersByName($params);
        header("Content-Type: application/json");
        echo json_encode($users);
        exit();
    }

    public function saveUser() {
        //get post data from our form post
        $name = $_POST['name'] ?: null;
        $age = $_POST['age'] ?: null;
        $email = $_POST['email'] ?: null;
        $errors = [];

        //validate and sanitize name
        if ($name) {
            //sanitize, htmlspecialchars replaces html reserved characters with their entity numbers
            //meaning they can't be run as code
            $name = htmlspecialchars($name);

//            echo htmlspecialchars($name);

            //validate text length
            if (strlen($name) < 2) {
                $errors['name'] = 'name is too short';
            }
        } else {
            $errors['name'] = 'name is required';
        }

        //numbers
        if ($age) {
            if ($age < 0 || $age > 120 || !intval($age)) {
                $errors['age'] = 'age is invalid';
            }
        } else {
            $errors['age'] = 'age is required';
        }

        //email via regular expressions
        if ($email) {
            //regex for valid email
            $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
            if (!preg_match($regex, $email)) {
                $errors['email'] = 'email is invalid';
            }
        } else {
            $errors['email'] = 'email is required';
        }

        //if we have errors
        if (count($errors)) {
            http_response_code(400);
            echo json_encode($errors);
            exit();
        }

        //we could save the data off to be saved here

        $returnData = [
            'name' => $name,
            'age' => $age,
            'email' => $email,
        ];
        echo json_encode($returnData);
        exit();

    }

}