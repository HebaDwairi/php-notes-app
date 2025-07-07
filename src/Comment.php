<?php

require __DIR__ . 'Database.php';

class Comment {
  public $content;
  public $id; 
  public $user_id;
  public $note_id;
  public $is_guest;
  public $guest_name;
  public $status;
  public $created_at;

  public function __construct($content, $note_id, $is_guest, $guest_name=null, $user_id=null, $id=null, $status=null, $created_at=null) {
    $this->content = $content;
    $this->note_id = $note_id;
    $this->is_guest = $is_guest;
    $this->guest_name = $guest_name;
    $this->id = $id;
    $this->user_id = $user_id;
    $this->status = $status;
    $this->created_at = $created_at;
  }
}




class CommentRepository {
  private $pdo;

  public function __construct() {
    $this->pdo = Database::getInstance();
  }

  public function create(Comment $c) {
    $sql = '';
    
    if($c->is_guest) {
      $sql = "INSERT INTO comments (content, note_id, is_guest, guest_name) values (?, ?, ?, ?)";
    }
    else {
      $sql = "INSERT INTO comments (content, note_id, is_guest, user_id) values (?, ?, ?, ?)";
    }

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$c->content, $c->note_id, $c->is_guest, $c->user_id || $c->guest_name]);
  }

  public function delete(Comment $c) {
    $sql = "DELETE FROM comments WHERE id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$c->id]);
  }

  public function approve(Comment $comment) {
    $sql = "UPDATE comments SET status = 'approved' WHERE id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$c->id]);
  }

  public function findByNoteId($note_id) {
    $sql = "SELECT FROM comments WHERE $note_id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$note_id]);

    
  }

  public function findAllPending() {

  }
}