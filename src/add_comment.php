<?php
require_once __DIR__ . '/session.php';
require __DIR__  . "/Comment.php";
require __DIR__  . "/Database.php";

header('Content-Type: application/json');

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
$user_id = $_SESSION['user_id'] ?? null;


if($data) {
  $content = $data['content'];
  $note_id = $data['note_id'];
  $is_guest = empty($user_id)? 1 : 0;
  $guest_name = 'guest-' . rand(1000, 1999);

  if(empty($note_id) || empty($content)) {
    echo json_encode([
      'success' => false,
      'error' => "note_id and comment content are required",
    ]);
    exit;
  }

  try {
    $commentRepo = new CommentRepository();
    $comment = null;

    if($is_guest) {
      $comment = new Comment($content, $note_id, $is_guest, $guest_name);
    }
    else{
      $comment = new Comment($content, $note_id, $is_guest, null, $user_id);
    }

    $commentRepo->create($comment);

    echo json_encode([
      'success' => true,
      'comment' => $comment->content,
      'username' => $comment->username,
      'created_at' => $comment->created_at,
      'guest_name' => $comment->guest_name,
      'is_guest' => $comment->is_guest
    ]);
  }
  catch(Exception $e) {
    echo json_encode([
      'success' => false,
      'error' => $e->getMessage(),
    ]);
  }

}