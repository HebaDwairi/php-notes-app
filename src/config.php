<?php
require __DIR__ .'/Database.php';
require __DIR__ .'/Note.php';

try {
  $db = new Database();
  $notes_repo = new NoteRepository();
}
catch (Exception $e) {
  error_log($e->getMessage());
  die("Database error");
}