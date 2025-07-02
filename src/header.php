<header class="bg-slate-700/50 backdrop-blur-sm sticky top-0 z-10 border-b border-slate-600">
    <div class="max-w-7xl px-4 mx-auto">
        <div class="flex justify-between items-center ">
            <a href="index.php">
              <h1 class="text-3xl font-black p-2 ml-4">Notes</h1>
            </a>
            <?php if(isset($_SESSION['user_id'])):?>
              <div class="flex items-center">
                  <h2 class="font-bold text-lg p-2 mr-7"><?php echo"Welcome back {$_SESSION['username']}!" ?></h2>
                  <a href="logout.php" class="bg-slate-600 rounded-xl font-bold text-lg p-2 mr-7 my-2 hover:bg-slate-500">Logout</a>
              </div>
            <?php endif; ?>
        </div>
    </div>
</header>