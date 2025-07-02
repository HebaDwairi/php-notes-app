<?php
require_once __DIR__ . '/session.php';
require __DIR__ . '/config.php';
require __DIR__  . "/User.php";



$usersRepo = new UserRepository();

$error = '';

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if(!$username || !$email || !$password || !$confirm_password) {
        $error = "All fields are required";
    }
    else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email";
    }
    else if(strlen($password) < 8 ) {
        $error = "Password should be at least 8 characters long";
    }
    else if(!($password === $confirm_password)) {
        $error = "Passwords don't match";
    }
    else if($usersRepo->findByUsername($username)|| $usersRepo->findByEmail($email)) {
        $error = "User already exists";
    }
    else {
        try{
            $user = User::register($username, $email, $password);
            $usersRepo->create($user);

            session_regenerate_id(true);
            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->username;
            header('Location: index.php');
            exit;
        }
        catch(Exception $e){
            $error = 'Failed to create user';
        }
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
  <div class="flex flex-col min-h-3/4-screen items-center mt-10">
    <div class="shadow-md rounded-lg p-9 bg-slate-700 text-white w-1/4">
      <h1 class="text-2xl font-bold mb-7">Sign Up</h1>
      <form action="register.php" method="post" class="flex flex-col gap-4">
        <div>
          <input
            type="text"
            name="username" required
            class="border bg-slate-800/60 border-slate-600 p-3 rounded-lg w-full"
            placeholder="Username">
        </div>
        <div>
          <input
            type="email"
            name="email" required
            class="border bg-slate-800/60 border-slate-600 p-3 rounded-lg w-full"
            placeholder="Email">
        </div>
        <div>
          <input
            type="password"
            name="password" required
            class="border bg-slate-800/60 border-slate-600 p-3 rounded-lg w-full"
            placeholder="Password">
        </div>
        <div>
          <input
            type="password"
            name="confirm_password" required
           class="border bg-slate-800/60 border-slate-600 p-3 rounded-lg w-full"
            placeholder="Confirm Password">
        </div>
        <?php if (!empty($error)): ?>
          <div class="bg-red-500 text-white p-3 rounded mb-4">
            <?php echo htmlspecialchars($error); ?>
          </div>
        <?php endif; ?>
        <button
          type="submit"
          class="bg-teal-400 w-full hover:bg-teal-300 text-white font-bold py-2 px-4 rounded mx-auto my-2">Sign Up</button>
        <p class="mt-4 text-center text-sm text-slate-400">
          Already have an account? 
          <a href="login.php" class="text-teal-300 hover:underline">Login</a>
        </p>
      </form>
    </div>
  </div>
</body>