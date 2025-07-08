<?php
require_once __DIR__ . '/session.php';
require __DIR__ . '/config.php';
require __DIR__ . '/Like.php';
header('Content-Type: application/json');

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$likes = new Likes();
if ($data['note_id'] && !empty($_SESSION['user_id'])){
    if(!$likes->isNoteLiked($_SESSION['user_id'], $data['note_id'])) {
        $likes->create($data['note_id'], $_SESSION['user_id']);
        echo json_encode([
        'success' => true,
        'liked' => true,
        ]);

    }
    else {
        $likes->delete($data['note_id'], $_SESSION['user_id']);
        echo json_encode([
        'success' => true,
        'liked' => false,
        ]);
    }
   
}
else {
    echo json_encode([
    'success' => false,
    ]);
}