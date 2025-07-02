<?php
require_once __DIR__ . '/session.php';
require __DIR__ . '/config.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

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
        <div class="flex flex-col lg:flex-row gap-8 ">
        


            <div class=" mx-auto p-6 shadow-md rounded-xl space-y-4 bg-slate-700 text-slate-300 w-full lg:w-1/3 lg:sticky lg:top-24 self-start">
                <h2 class="text-xl font-bold">Add a Note</h2>
                <form action="add_note.php" 
                      method="POST" 
                      class="flex flex-col space-between h-full space-y-4"
                      enctype="multipart/form-data">
                    <div class="space-y-4">
                        <div>
                        
                        <input
                            type="text"
                            name="title"
                            placeholder="Title"
                            required
                            maxlength="255"
                            class="border bg-slate-800/60 border-slate-600 p-3 rounded-xl w-full">
                        </div>
                        
                        <div>
                        
                        <textarea
                            name="content"
                            placeholder="Content"
                            required
                            class="border bg-slate-800/60 border-slate-600 p-3 rounded-xl w-full h-40"></textarea>
                        </div>

                        <div >
                            <label for="image"class="block text-sm text-slate-400 mb-1">Image:</label>
                            <input type="file" name="image" accept="image/*"  
                               class="block w-full text-sm text-slate-300 file:mr-4 file:py-1 file:px-2
                                    file:rounded-lg file:border-0
                                    file:bg-accent hover:file:bg-accent-hover">
                        </div>
                        
                        <?php echo $message ?? null ?>
                    </div>
                
                    <button
                    type="submit"
                    class="bg-accent hover:bg-accent-hover text-slate-800 font-bold py-3 px-4 rounded-xl transition-colors w-full ">Add Note</button>
                </form>
            </div>





            <div class="w-full lg:w-2/3 p-6 shadow-md rounded-xl bg-slate-700 space-y-4 ">
                <h2 class="text-xl font-bold">Your Notes</h2>
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
                <ul class="space-y-4">
                    <?php
                    try {
                        $notes = $search_notes ?? $notes_repo->findByUser($_SESSION['user_id']);
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
                                        
                                    </a>
                                        <?php if($note->user_id == $_SESSION['user_id']): ?>
                                            <div class='flex items-center space-x-2 '>
                                            <a href='edit_note.php?id=<?= $note->id ?>' class='pt-2 font-bold px-2 text-teal-300 hover:underline text-sm ml-2'>Edit</a>
                                            <form action='delete_note.php' method='POST' class='mt-2'>
                                                <input type='hidden' name='id' value=<?= $note->id ?>>
                                                <button
                                                type='submit'
                                                class='bg-red-400 hover:bg-red-500 text-white text-sm py-1 px-2 rounded-full'>Delete</button>
                                            </form>
                                        </div>
                                        <?php endif;?>
                                    </div>
                                    <p class='text-sm text-slate-300 p-4'>
                                        <?= htmlspecialchars(mb_substr($note->content, 0, 200) . (mb_strlen($note->content) > 200 ? '...' : '')) ?>
                                    </p>
                                    <div class="flex justify-between items-center mt-5 px-4 pb-4">
                                        <p class='text-xs text-slate-500'>Edited: <?= htmlspecialchars($note->updated_at) ?></p>
                                        <p class='text-xs text-slate-500'>Created: <?= htmlspecialchars($note->created_at) ?></p>
                                    </div>
                                
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
