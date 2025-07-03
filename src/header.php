<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>


<header class="bg-slate-700/50 backdrop-blur-sm sticky top-0 z-10 border-b border-slate-600">
    <div class="max-w-7xl px-4 mx-auto">
        <div class="flex justify-between items-center ">
            <div class="flex items-center gap-4 w-1/2">
                <a href="index.php">
                  <h1 class="text-3xl font-black p-2 ml-4">Notes</h1>
                </a>
                <?php if($current_page == "index.php" || $current_page == "my_notes.php"): ?>
                <form action=<?= $current_page ?> method="get">
                    <div class="relative">
                        <input
                            type="text"
                            name="q"
                            value="<?= htmlspecialchars($search) ?>"
                            placeholder="Search notes..."
                            class="bg-slate-800/60 border bg-slate-800 border-slate-600 p-2 pl-10 rounded-xl w-full">
                        <svg class="w-5 h-5 text-slate-400 absolute top-1/2 left-3 transform -translate-y-1/2"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>
                <?php endif; ?>
            </div>
            <?php if(isset($_SESSION['user_id'])):?>
            <div class="flex items-center justify-end gap-x-4 w-1/2 pr-4 ">
                <span class="text-lg font-semibold text-slate-300">
                    Hi <?= htmlspecialchars($_SESSION['username']) ?>!
                </span>

                <a href="my_notes.php"
                class="px-4 py-2 rounded-xl bg-teal-500 text-slate-900 font-semibold hover:bg-teal-400 transition">
                    My Notes
                </a>

                <a href="logout.php"
                class="px-4 py-2 rounded-xl bg-slate-600 text-white font-semibold hover:bg-slate-500 transition">
                    Logout
                </a>
            </div>

            <?php endif; ?>
            <?php if(!isset($_SESSION['user_id'])):?>
              <div class="flex items-center w-1/2 justify-end">
                  <a href="login.php" class="text-slate-800 bg-teal-500 rounded-xl font-bold text-lg p-1 px-5 my-2 mr-7 hover:bg-teal-400">Log in</a>
              </div>
            <?php endif; ?>
        </div>
    </div>
</header>