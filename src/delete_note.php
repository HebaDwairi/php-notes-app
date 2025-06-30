<?php
session_start();
require __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    try {
        if ($id) {
            $notes_repo->delete($id);

        }
    } 
    catch(Exception $e){
        $_SESSION['message'] = 'Failed to delete note' . $e->getMessage() . $id;
    }

    header('Location: index.php');
}
