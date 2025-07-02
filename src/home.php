<?php
require_once __DIR__ . '/session.php';
require __DIR__ . '/config.php';



$message = null;

if (isset($_SESSION['message'])) {
    $message = "<p style='color: red;'>{$_SESSION['message']}</p>";
    unset($_SESSION['message']);
}

$search = $_GET['q'] ?? '';
$notes = [];

if (!empty($search)) {
    $search_notes = $notes_repo->search($search);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    accent: {
                        DEFAULT: '#2dd4bf',
                        hover: '#14b8a6',
                        text: '#5eead4',
                    }
                }
            }
        }
    }
</script>
</head>
<body class="bg-slate-800 text-slate-300 min-h-screen">

    <?php include 'header.php'; ?>
    <form action="index.php" method="get">
                    <div class="relative">
                        <input
                            type="text"
                            name="q"
                            value="<?= htmlspecialchars($search) ?>"
                            placeholder="Search your notes..."
                            class="bg-slate-800/60 border bg-slate-800 border-slate-600 p-3 pl-10 rounded-xl w-full">
                        <svg class="w-5 h-5 text-slate-400 absolute top-1/2 left-3 transform -translate-y-1/2" 
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>

    <div class="mx-auto max-w-7xl p-8">
        <div class="flex justify-center ">
        
            <div class="w-full lg:w-2/3 p-6 shadow-md rounded-xl bg-slate-700 space-y-4 ">
                <h2 class="text-xl font-bold">Your Notes</h2>
                
                <ul class="space-y-4">
                    <?php
                    try {
                        $notes = $search_notes ?? $notes_repo->getAll();
                        foreach ($notes as $note): ?>
                            <li class='bg-slate-800/50 rounded-xl border border-transparent hover:text-accent hover:border-accent transition-colors duration-300 group overflow-hidden'>
                                <a href="note_page.php?slug=<?= $note->slug ?> " class="block ">
                                    <?php if (!empty($note->image_path)): ?>
                                        <img src="<?= htmlspecialchars($note->image_path) ?>" 
                                            alt="<?= htmlspecialchars($note->title) ?>" 
                                            class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300 rounded-t-xl">
                                    <?php endif; ?>
                                    <div class='flex justify-between items-center p-4'>
                                        <div >
                                            <strong> <?= htmlspecialchars($note->title) ?>
                                            </strong> by <?=  htmlspecialchars($note->username) ?>
                                        </div>
                                        

                                    </div>
                                    <p class='text-sm text-slate-300 p-4'>
                                        <?= htmlspecialchars(mb_substr($note->content, 0, 200) . (mb_strlen($note->content) > 200 ? '...' : '')) ?>
                                    </p>
                                    <div class="flex justify-between items-center mt-5 px-4 pb-4">
                                        <p class='text-xs text-slate-500'>Edited: <?= htmlspecialchars($note->updated_at) ?></p>
                                        <p class='text-xs text-slate-500'>Created: <?= htmlspecialchars($note->created_at) ?></p>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach;
                    } 
                    catch (Exception $e) {
                        echo "<li>Error fetching notes: {$e->getMessage()}</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>



    
</body>
</html>
