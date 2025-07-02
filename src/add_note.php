<?php
require_once __DIR__ . '/session.php';
require __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = $_POST['content'];
    $image_path = null;

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
    if(!empty($_FILES['image']['name'])) {
        $uploads_dir = __DIR__ . '/uploads/';

        $filename = time() . "_"  .basename( $_FILES['image']['name']); 


        $target_path = $uploads_dir . $filename;
        $path_in_db = '/uploads/' . $filename;

        if(move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = $path_in_db;
        }
        else {
            $_SESSION['message'] = "Failed to upload image";
            header("Location: index.php");
            exit;
        }
    }
    

    try{
        $note = new Note(
            title: $title,
            content: $content,
            user_id: $_SESSION['user_id'],
            image_path: $image_path
        );
        $notes_repo->create($note);
    }
    catch(Exception $e){
        $_SESSION['message'] = 'Failed to add note';
    }

    header('Location: index.php');
    exit;
}
