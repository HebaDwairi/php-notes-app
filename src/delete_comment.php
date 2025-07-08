<?php
require_once __DIR__ . '/session.php';
require __DIR__ . '/config.php';
require __DIR__ . '/Comment.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'] ?? null;

    try {
        if ($id) {
            $cRepo = new CommentRepository();
            if($_SESSION['is_admin']) {
                $cRepo->delete($id);
            }
            else {
                die("you don't have the permission to delete this comment");
            }
        }
    } 
    catch(Exception $e){
        $_SESSION['message'] = 'Failed to delete comment' . $e->getMessage() . $id;
    }

    header('Location: index.php');
}