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

    <div class="mx-auto max-w-7xl p-8">
        <div class="flex justify-center ">
        
            <div class="w-full lg:w-2/3 p-6 shadow-md rounded-xl bg-slate-700 space-y-4 ">
                <h2 class="text-xl font-bold">Latest Notes</h2>
                
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
