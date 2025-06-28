<?php

require __DIR__ . '/src/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author = $_POST['author'];

    $note = new Note($title, $content, $author);
    $notes_repo->create($note);

    header('Location: index.php');
    exit;
}
