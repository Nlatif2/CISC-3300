<?php

class Post {
    private $title;
    private $content;

    public function __construct($title, $content) {
        if (strlen($title) < 3) {
            throw new Exception("Title must be at least 3 characters.");
        }
        if (strlen($content) < 10) {
            throw new Exception("Content must be at least 10 characters.");
        }
        $this->title = $title;
        $this->content = $content;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getContent() {
        return $this->content;
    }

    public static function save($title, $content) {
        $pdo = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
        $stmt = $pdo->prepare("INSERT INTO posts (title, content) VALUES (:title, :content)");
        $stmt->execute(['title' => $title, 'content' => $content]);
    }

    public static function searchByTitle($searchTitle) {
        $pdo = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE title LIKE :title");
        $stmt->execute(['title' => '%' . $searchTitle . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
