<?php
require_once __DIR__ . '/session.php';
require __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    require __DIR__ . '/process_note_form.php';

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

    header('Location: /notes');
    exit;
}
