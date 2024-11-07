<?php

namespace app\models;

class User {
    public function getAllUsersByName($params) {
        //in future these will come from the database

        $allUsers = [
            [
                'id' => '1',
                'name' => 'pinecone'
            ],
            [
                'id' => '2',
                'name' => 'nathan'
            ],
        ];

        if (!empty($params['name'])) {
            return array_filter($allUsers, function ($user) use ($params) {
                if ($user['name'] === $params['name']) {
                    return $user;
                };
                return null;
            });
        }

        return $allUsers;
    }

    public function saveUsers() {
        //in future this will save a user to the database
    }
}