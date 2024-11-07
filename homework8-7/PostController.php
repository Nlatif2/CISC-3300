<?php

require_once '../models/Post.php';

class PostController {
    public function index() {
        $search = $_GET['search'] ?? '';
        $posts = Post::searchByTitle($search);
        require 'public/views/posts.html';
    }

    public function create() {
        require 'public/views/add-posts.html';
    }

    public function store() {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';

        try {
            $post = new Post($title, $content);
            $post->save($title, $content);
            header("Location: /posts");
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            require 'public/views/add-posts.html';
        }
    }
}
