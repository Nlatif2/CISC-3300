<?php

namespace App\Models;

use PDO;

class Post extends Model // Assuming Model is your base class with DB connection logic.
{
    protected static $tableName = 'posts';

    public static function all()
    {
        $stmt = self::getConnection()->prepare("SELECT * FROM " . self::$tableName);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $stmt = self::getConnection()->prepare("INSERT INTO " . self::$tableName . " (title, content) VALUES (:title, :content)");
        return $stmt->execute([':title' => $data['title'], ':content' => $data['content']]);
    }

    public static function find($id)
    {
        $stmt = self::getConnection()->prepare("SELECT * FROM " . self::$tableName . " WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}