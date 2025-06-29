<?php

class Note {
  public  $id;
  public $title;
  public $content;
  public $author;
  public $created_at;
  public $updated_at;

  public function __construct($title, $content, $author, $id=null, $created_at=null, $updated_at=null) { 
    $this->title = $title;
    $this->content = $content;
    $this->author = $author;
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
    $sql = "INSERT INTO notes (title, content, author) VALUES (?, ?, ?)";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$note->title, $note->content, $note->author]);
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
        $author=$data['author'],
        $id=$data['id'],
        $created_at=$data['created_at'],
        $updated_at=$data['updated_at']
      );
    }
    
    return null;
  }

  public function getAll() {
    $sql = "SELECT * FROM notes ORDER BY updated_at DESC";
    $stmt = $this->pdo->query($sql);
    
    $notes = [];

    while($data = $stmt->fetch()) {
      $notes[] = new Note(
        $title=$data['title'],
        $content=$data['content'],
        $author=$data['author'],
        $id=$data['id'],
        $created_at=$data['created_at'],
        $updated_at=$data['updated_at']
      );
    }
    
    return $notes;
  }

}