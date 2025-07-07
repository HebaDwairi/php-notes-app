<?php
require_once __DIR__ . '/session.php';
require __DIR__  . "/Comment.php";
require __DIR__  . "/Database.php";

$user_id = $_SESSION['user_id'] ?? null;


if($_SERVER['REQUEST_METHOD'] === 'POST') {
  $content = $_POST['content'] ?? null;
  $note_id = $_POST['note_id'] ?? null;
  $is_guest = empty($user_id)? 1 : 0;
  $guest_name = 'guest-' . rand(1000, 1999);

  if(empty($note_id) || empty($content)) {
    //i should add a nicer message
    die("note_id and comment content are required");
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
  }
  catch(Exception $e) {
    die("error creating new comment". $e->getMessage());
  }

  header('Location: ' .$_SERVER['HTTP_REFERER']);
  exit;
}