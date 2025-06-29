<?php

class Database {
  private $host;
  private $user;
  private $password;
  private $dbname;
  private $charset;

  public static $pdo = null;

  public function __construct() {
    $this->host = getenv("DB_HOST");
    $this->user = getenv('MYSQL_USER');
    $this->password = getenv('MYSQL_PASSWORD');
    $this->dbname = getenv('MYSQL_DATABASE');
    $this->charset = 'utf8mb4';
  }

  public function connect() {
    if (self::$pdo) {
      return self::$pdo;
    }
    
    $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
    $options = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
      self::$pdo = new PDO($dsn, $this->user, $this->password, $options);
    }
    catch(\PDOException $e) {
      throw new Exception("Connection failed: " . $e->getMessage());
    }

    return self::$pdo;
  }

  public static function getInstance() {
    if(self::$pdo == null) {
      $db = new self();
      $db->connect();
    }

    return self::$pdo;
  }
}