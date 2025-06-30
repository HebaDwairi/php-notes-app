<?php

class User {
  public $id;
  public $username;
  public $email;
  public $password_hash;
  public $created_at;
  public $updated_at;

  private function __construct (){}

  public static function register($username, $email, $password, $id = null) {
    $user = new User();
    $user->id = $id;
    $user->username = $username;
    $user->email = $email;
    $user->password_hash = password_hash($password, PASSWORD_DEFAULT);
    return $user;
  }

  public static function fromDatabase($id, $username, $email, $password_hash, $created_at=null, $updated_at=null) {
    $user = new User();
    $user->id = $id;
    $user->username = $username;
    $user->email = $email;
    $user->password_hash = $password_hash;
    $user->created_at = $created_at;
    $user->updated_at = $updated_at;
    return $user;
  }

  public function verifyPassword($password) {
    return password_verify($password, $this->password_hash);
  }
}

class UserRepository {
  private $pdo;

  public function __construct() {
    $this->pdo = Database::getInstance();
  }

  public function create(User $user) {
    $sql = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$user->username, $user->email, $user->password_hash]);

    $user->id = $this->pdo->lastInsertId();
  }

  public function findByUsername($username) {
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$username]);
    
    $data = $stmt->fetch();
    
    if ($data) {
      return User::fromDatabase(
        $data['id'],
        $data['username'],
        $data['email'],
        $data['password_hash'],
        $data['created_at'],
        $data['updated_at']
      );
    }
    
    return null;
  }

  public function findByEmail($email) {
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$email]);
    
    $data = $stmt->fetch();
    
    if ($data) {
      return User::fromDatabase(
        $data['id'],
        $data['username'],
        $data['email'],
        $data['password_hash'],
        $data['created_at'],
        $data['updated_at']
      );
    }
    
    return null;
  }
}
