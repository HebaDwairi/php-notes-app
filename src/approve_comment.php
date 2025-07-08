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
                $cRepo->approve($id);
            }
            else {
                die("you don't have the permission to approve this comment");
            }
        }
    } 
    catch(Exception $e){
        $_SESSION['message'] = 'Failed to approve comment' . $e->getMessage() . $id;
    }

    header('Location: index.php');
}