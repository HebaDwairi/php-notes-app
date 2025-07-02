<?php

class Note {
  public $id;
  public $title;
  public $content;
  public $user_id;
  public $created_at;
  public $updated_at;
  public $username;
  public $slug;
  public $image_path;


  public function __construct($title, $content, $user_id, $id=null, $created_at=null, $updated_at=null, $slug=null, $image_path=null) { 
    $this->title = $title;
    $this->content = $content;
    $this->user_id = $user_id;
    $this->id = $id;
    $this->created_at = $created_at;
    $this->updated_at = $updated_at;
    $this->slug = $slug;
    $this->image_path = $image_path;
  }
 
}


class NoteRepository {
  private $pdo;

  public function __construct() {
    $this->pdo = Database::getInstance();
  }

  public function create(Note $note) {
    $note->slug = $this->create_slug($note->title);

    $sql = "INSERT INTO notes (title, content, user_id, slug, image_path) VALUES (?, ?, ?, ?, ?)";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$note->title, $note->content, $note->user_id, $note->slug, $note->image_path]);
  }

  public function update(Note $note) {
    $note->slug = $this->create_slug($note->title);

    $sql = "UPDATE notes SET title = ?, content = ?, updated_at = NOW(), slug = ?, image_path = ? WHERE id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$note->title, $note->content, $note->slug, $note->image_path] , $note->id);
  }

  public function delete($id) {
    $sql = "DELETE FROM notes WHERE id = ?";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$id]);
  }

  public function findById($id) {
    $sql = "SELECT notes.*, users.username
            FROM notes 
            JOIN users ON notes.user_id = users.id
            WHERE notes.id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$id]);
    
    $data = $stmt->fetch();
    
    if ($data) {
      $note = new Note(
        $title=$data['title'],
        $content=$data['content'],
        $user_id=$data['user_id'],
        $id=$data['id'],
        $created_at=$data['created_at'],
        $updated_at=$data['updated_at'],
        $slug=$data['slug'],
        $image_path=$data['image_path']
      );

      $note->username = $data['username'];
      return $note;
    }
    
    return null;
  }

  public function findBySlug($slug) {
    $sql = "SELECT notes.*, users.username
            FROM notes 
            JOIN users ON notes.user_id = users.id
            WHERE notes.slug = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$slug]);
    
    $data = $stmt->fetch();
    
    
    if ($data) {
      $note = new Note(
        $title=$data['title'],
        $content=$data['content'],
        $user_id=$data['user_id'],
        $id=$data['id'],
        $created_at=$data['created_at'],
        $updated_at=$data['updated_at'],
        $slug=$data['slug'],
        $image_path=$data['image_path']
      );

      $note->username = $data['username'];
      return $note;
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
        $data['updated_at'],
        $data['slug'],
        $data['image_path']
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
        $data['updated_at'],
        $data['slug'],
        $data['image_path']
      );

      $note->username = $data['username'];
      $notes[] = $note;
    }
    
    return $notes;
  }

  public function create_slug($title) {
    $baseSlug = preg_replace('~[^\pL\d]+~u', '-', $title);
    $baseSlug = iconv('utf-8', 'us-ascii//TRANSLIT', $baseSlug);
    $baseSlug = preg_replace('~[^-\w]+~', '', $baseSlug);
    $baseSlug = trim($baseSlug, '-');
    $baseSlug = preg_replace('~-+~', '-', $baseSlug);

    $baseSlug = strtolower($baseSlug);
    $slug = $baseSlug;
    $i = 1;

    while (true) {
      $sql = "SELECT COUNT(*) FROM notes WHERE slug = ?";
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([$slug]);
      if ($stmt->fetchColumn() == 0) {
          return $slug;
      }
      $slug = $baseSlug . '-' . $i;
      $i++;
    }
  }

}