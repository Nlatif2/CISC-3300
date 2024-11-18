<?php

namespace app\models;

use PDO;
use PDOException;

abstract class Model {

    public function findAll() {
        $query = "select * from $this->table";
        return $this->fetchAll($query);
    }

    private function connect() {

        $type = 'mysql';
        $port = '8889';
        $charset = 'utf8mb4';

//      some of these are optional
        $dsn = "$type:hostname=" . DBHOST .";dbname=" . DBNAME . ";port=$port;charset=$charset";

        $options = [
            //we can set the error mode, to throw exceptions or PDO type errors
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            //set the default fetch type
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            return new PDO($dsn, DBUSER, DBPASS, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), $e->getCode());
        }

    }

    public function fetch($query) {
        $connectedPDO = $this->connect();
        $statementObject = $connectedPDO->query($query);
        //no params, one row
        return $statementObject->fetch();
    }

    public function fetchAll($query) {
        $connectedPDO = $this->connect();
        $statementObject = $connectedPDO->query($query);
        //no params, multiple rows
        return $statementObject->fetchAll();
    }

    public function fetchAllWithParams($query, $data = []) {
        $connection = $this->connect();
        //prepare statement -
        $statementObject = $connection->prepare($query);
        //data needs
        $successOrFail = $statementObject->execute($data);
        if ($successOrFail) {
            $result = $statementObject->fetchAll(PDO::FETCH_OBJ);
            if (is_array($result) && count($result)) {
                return $result;
            }
        }
        return false;
    }

}