<?php
require_once __DIR__ . '/session.php';
require __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    try {
        if ($id) {
            $note = $notes_repo->findById($id);

            if($note->user_id == $_SESSION['user_id']) {
                $notes_repo->delete($id);
            }
            else {
                die("you don't have the permission to delete this note");
            }
        }
    } 
    catch(Exception $e){
        $_SESSION['message'] = 'Failed to delete note' . $e->getMessage() . $id;
    }

    header('Location: index.php');
}
