<?php

namespace app\models;

//using the database class namespace
use app\models\Model;

class User extends Model {

    public function getAllUsersByName($name) {
        $query = "select * from users WHERE CONCAT(firstName,' ',lastName) like :name";
        return $this->fetchAllWithParams($query, ['name' => '%' . $name . '%']);
    }

    public function getAllUsers() {
        $query = "select * from users";
        return $this->fetchAll($query);
    }

    public function getUserById($id){
        $query = "select * from users where id = :id";
        return $this->fetchAllWithParams($query, ['id' => $id]);
    }

    public function saveUser($inputData){
        $query = "insert into users (firstName, lastName) values (:firstName, :lastName);";
        return $this->fetchAllWithParams($query, $inputData);
    }

    public function updateUser($inputData){
        $query = "update users set firstName = :firstName, lastName = :lastName where id = :id";
        return $this->fetchAllWithParams($query, $inputData);
    }

    public function deleteUser($inputData){
        $query = "DELETE FROM users where id = :id";
        return $this->fetchAllWithParams($query, $inputData);
    }
}