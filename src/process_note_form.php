<?php

$title = trim($_POST['title']);
$content = $_POST['content'];
$image_path = null;



if (empty($title) || empty($content)) {
    $_SESSION['message'] = 'All fields are required';
    header('Location: my_notes.php');
    exit;
}
if (strlen($title) > 255) {
    $_SESSION['message'] = 'Title must be less than 255 characters';
    header('Location: my_notes.php');
    exit;
}
if(!empty($_FILES['image']['name'])) {

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
    finfo_close($finfo);

    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!in_array($mimeType, $allowed)) {
        $_SESSION['message'] = 'Invalid file type, allowed types: jpeg, png, webp, gif';
        header('Location: my_notes.php');
        exit;
    }


    $uploads_dir = __DIR__ . '/uploads/';

    $filename = time() . "_"  .basename( $_FILES['image']['name']); 


    $target_path = $uploads_dir . $filename;
    $path_in_db = '/uploads/' . $filename;

    if(move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
        $image_path = $path_in_db;
    }
    else {
        $_SESSION['message'] = "Failed to upload image";
        header("Location: my_notes.php");
        exit;
    }
}