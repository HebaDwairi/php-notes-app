<?php

class Comment {
  public $content;
  public $id; 
  public $user_id;
  public $note_id;
  public $is_guest;
  public $guest_name;
  public $status;
  public $created_at;
  public $username;

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
    $stmt->execute([$c->content, $c->note_id, $c->is_guest, $c->is_guest ? $c->guest_name : $c->user_id]);
    $id = $this->pdo->lastInsertId();

    $stmt = $this->pdo->prepare("SELECT comments.*, users.username 
                                 FROM comments 
                                 LEFT JOIN users ON users.id = comments.user_id  
                                 WHERE comments.id = ?");
    $stmt->execute([$id]);
    $result =  $this->comment_from_db_row($stmt->fetch());
    $c->username = $result->username;
    $c->created_at = $result->created_at;
  }

  public function delete($id) {
    $sql = "DELETE FROM comments WHERE id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$id]);
  }

  public function approve($id) {
    $sql = "UPDATE comments SET status = 'approved' WHERE id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$id]);
  }

  public function findAllByNoteId($note_id) {
    $sql = "SELECT comments.*, users.username
            From comments
            LEFT JOIN users ON users.id = comments.user_id
            WHERE note_id = ?
            ORDER BY created_at DESC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$note_id]);

    $comments= [];

    while($data = $stmt->fetch()) {
      $c = $this->comment_from_db_row($data);
      $comments[] = $c;
    }
    

    return $comments;
  }

  public function findAllPending() {
    $sql = "SELECT comments.*, users.username
            From comments
            LEFT JOIN users ON users.id = comments.user_id
            WHERE status = 'pending'";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();

    $comments= [];

    while($data = $stmt->fetch()) {
      $c = $this->comment_from_db_row($data);
      $comments[] = $c;
    }
    

    return $comments;
  }

  private function comment_from_db_row($data) {
    $comment = new Comment(
        $data['content'],
        $data['note_id'],
        $data['is_guest'],
        $data['guest_name'],
        $data['user_id'],
        $data['id'],
        $data['status'],
        date("d-m-Y", strtotime($data['created_at'])),
      );

    $comment->username = $data['username'];

    return $comment;
  }
}