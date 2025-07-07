<?php

class Likes {
  private $pdo;

  public function __construct() {
    $this->pdo = Database::getInstance();
  }

  public function create($note_id, $user_id) {
    $stmt = $this->pdo->prepare("INSERT INTO likes (user_id, note_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $note_id]);
  }

  public function isNoteLiked($user_id, $note_id) {
    $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as num_likes
            FROM likes 
            WHERE user_id = ? AND note_id = ?");
    $stmt->execute([$user_id, $note_id]);

    return $stmt->fetch()['num_likes'] > 0;
  }

}