<?php
require_once __DIR__ . '/session.php';
require __DIR__  . "/config.php";
require __DIR__  . "/User.php";

if (isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

$usersRepo = new UserRepository();

$message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if(empty($username) || empty($password)) {
    $message = "all fields are required";
  }

  $user = $usersRepo->findByUsername($username);

  if(!$user || !$user->verifyPassword($password)) {
    $message = "incorrect username or password";
  }
  else {
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user->id;
    $_SESSION['username'] = $user->username;
    $_SESSION['is_admin'] = $user->is_admin;
    header('Location: /');
    exit;
  }

}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notes App</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-800 text-white">
  <?php include 'header.php'; ?>
  <div class="flex flex-col min-h-3/4-screen items-center">
    <div class="shadow-md rounded-lg p-9 bg-slate-700 text-white mt-20 w-1/4">
      <h1 class="text-2xl font-bold mb-7">Login</h1>
      <form action="login.php" method="post" class="flex flex-col gap-5">
        <div>
          <input
            type="text"
            name="username" required
           class="border bg-slate-800/60 border-slate-600 p-3 rounded-lg w-full"
            placeholder="Username">
        </div>
        <div>
          <input
            type="password"
            name="password" required
            class="border bg-slate-800/60 border-slate-600 p-3 rounded-lg w-full"
            placeholder="Password">
        </div>
        <?php if (!empty($message)): ?>
          <div class="bg-red-500 text-white p-3 rounded mb-4">
            <?php echo htmlspecialchars($message); ?>
          </div>
        <?php endif; ?>
        <button
          type="submit"
          class="bg-teal-400 w-full hover:bg-teal-300 text-slate-800 font-bold py-2 px-4 rounded mx-auto my-2">Login</button>
        <p class="mt-4 text-center text-sm text-slate-400">
          Don't have an account? 
          <a href="/register" class="text-teal-300 hover:underline">Register</a>
        </p>
      </form>
    </div>
  </div>
</body>