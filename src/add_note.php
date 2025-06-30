<?php
session_start();
require __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = $_POST['content'];

    if (empty($title) || empty($content)) {
        $_SESSION['message'] = 'All fields are required';
        header('Location: index.php');
        exit;
    }
    if (strlen($title) > 255) {
        $_SESSION['message'] = 'Title must be less than 255 characters';
        header('Location: index.php');
        exit;
    }
    

    try{
        $note = new Note($title, $content, $_SESSION['user_id']);
        $notes_repo->create($note);
    }
    catch(Exception $e){
        $_SESSION['message'] = 'Failed to add note';
    }

    header('Location: index.php');
    exit;
}
