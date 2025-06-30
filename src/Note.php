<?php

class Note {
  public $id;
  public $title;
  public $content;
  public $user_id;
  public $created_at;
  public $updated_at;
  public $username;


  public function __construct($title, $content, $user_id, $id=null, $created_at=null, $updated_at=null) { 
    $this->title = $title;
    $this->content = $content;
    $this->user_id = $user_id;
    $this->id = $id;
    $this->created_at = $created_at;
    $this->updated_at = $updated_at;
  }
}


class NoteRepository {
  private $pdo;

  public function __construct() {
    $this->pdo = Database::getInstance();
  }

  public function create(Note $note) {
    $sql = "INSERT INTO notes (title, content, user_id) VALUES (?, ?, ?)";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$note->title, $note->content, $note->user_id]);
  }

  public function update(Note $note) {
    $sql = "UPDATE notes SET title = ?, content = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$note->title, $note->content, $note->id]);
  }

  public function delete($id) {
    $sql = "DELETE FROM notes WHERE id = ?";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$id]);
  }

  public function findById($id) {
    $sql = "SELECT * FROM notes WHERE id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$id]);
    
    $data = $stmt->fetch();
    
    if ($data) {
      return new Note(
        $title=$data['title'],
        $content=$data['content'],
        $user_id=$data['user_id'],
        $id=$data['id'],
        $created_at=$data['created_at'],
        $updated_at=$data['updated_at']
      );
    }
    
    return null;
  }

  public function getAll() {
    $sql = "SELECT notes.*, users.username
            FROM notes 
            JOIN users ON notes.user_id = users.id
            ORDER BY notes.updated_at DESC";
    
    $stmt = $this->pdo->query($sql);
    
    $notes = [];

    while($data = $stmt->fetch()) {
      $note = new Note(
        $data['title'],
        $data['content'],
        $data['user_id'],
        $data['id'],
        $data['created_at'],
        $data['updated_at']
      );

      $note->username = $data['username'];
      $notes[] = $note;
    }
    
    return $notes;
  }

  public function search($query) {
    $sql = "SELECT notes.*, users.username
            FROM notes 
            JOIN users ON notes.user_id = users.id
            WHERE notes.title LIKE ? OR notes.content LIKE ?
            ORDER BY notes.updated_at DESC";

    $stmt = $this->pdo->prepare($sql);
    $search_param = '%'.$query.'%';
    $stmt->execute([$search_param, $search_param]);
    
    $notes = [];

    while($data = $stmt->fetch()) {
      $note = new Note(
        $data['title'],
        $data['content'],
        $data['user_id'],
        $data['id'],
        $data['created_at'],
        $data['updated_at']
      );

      $note->username = $data['username'];
      $notes[] = $note;
    }
    
    return $notes;
  }

}