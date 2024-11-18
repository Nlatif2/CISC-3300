<?php

namespace App\Controllers;

use App\Models\Post;

class PostController
{
    // Display all posts
    public function index()
    {
        $posts = Post::all();
        include_once '../public/assets/views/post/posts-view.html'; // Adjust this path as needed.
    }

    // Show form to create a new post
    public function create()
    {
        include_once '../public/assets/views/post/posts-add.html'; // Adjust this path as needed.
    }

    // Store a new post
    public function store($data)
    {
        $title = $data['title'] ?? '';
        $content = $data['content'] ?? '';

        if ($title && $content) {
            Post::create(['title' => $title, 'content' => $content]);
            header('Location: /posts'); // Redirect to the posts index after creation.
            exit;
        } else {
            echo "Title and Content are required.";
        }
    }

    // Show form to edit an existing post
    public function edit($id)
    {
        $post = Post::find($id);
        include_once '../public/assets/views/post/posts-update.html'; // Adjust this path as needed.
    }

    // Update an existing post
    public function update($id, $data)
    {
        $post = Post::find($id);
        if ($post) {
            $post->title = $data['title'] ?? $post->title;
            $post->content = $data['content'] ?? $post->content;
            $post->save();
            header('Location: /posts'); // Redirect to the posts index after update.
            exit;
        } else {
            echo "Post not found.";
        }
    }

    // Delete a post
    public function destroy($id)
    {
        $post = Post::find($id);
        if ($post) {
            $post->delete();
            header('Location: /posts'); // Redirect to the posts index after deletion.
            exit;
        } else {
            echo "Post not found.";
        }
    }
}