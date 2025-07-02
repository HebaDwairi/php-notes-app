<?php
session_set_cookie_params([
  'lifetime' => 60 * 60 * 24 * 7,
  'path' => '/',
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Lax'
]);

ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 7);

session_start();
