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

function previewHTML($html, $limit = 200) {
    $text = strip_tags($html);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim(mb_substr($text, 0, $limit));
    return htmlspecialchars($text);
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
    <script src="https://cdn.tiny.cloud/1/9kxz2wr0h6q5fm3aw7fas6b73nhrzr7pl2hvp2fiyaar3ux0/tinymce/6/tinymce.min.js"></script>
    <script>
    tinymce.init({
        selector: 'textarea[name="content"]',
        plugins: 'image lists',
        toolbar: 'undo redo | formatselect | bold italic | fontsizeselect | forecolor backcolor | alignleft aligncenter alignright | bullist numlist',
        content_style: "body { background-color: #1E293B; color:white;}",
        skin: 'oxide-dark',
        content_css: 'dark',
        menubar: false,
        height: 250
    });
    </script>
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
<body class="bg-slate-800 text-slate-300 min-h-screen" data-page="my-notes">

    <?php include 'header.php'; ?>


    <div class="mx-auto max-w-7xl p-8">
        <div class="flex flex-col lg:flex-row gap-8 ">
        
            <div class="resize-x overflow-auto mx-auto p-6 shadow-md rounded-xl space-y-4 bg-slate-700 text-slate-300 w-full lg:w-2/5 lg:sticky lg:top-[5.35rem] self-start">
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
                            class="border bg-slate-800/60 border-slate-600 p-3 rounded-xl w-full h-50"></textarea>
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



            <div class="flex-1 w-full lg:w-3/5 p-6 shadow-md rounded-xl bg-slate-700 space-y-4 flex flex-col ">
                <h2 class="text-xl font-bold">Your Notes</h2>
                <p id="no-notes-message" class="text-center text-slate-400 hidden font-bold p-28 text-lg">
                You have no notes yet.
                </p>
                <ul class="space-y-4 " id="notes-list">
                    <?php
                    try {
                        $notes = $search_notes ?? $notes_repo->getOlderThanByUser($_SESSION['user_id'], null, null, 10);
                        foreach ($notes as $note): ?>
                            <li class='bg-slate-800/50 rounded-xl border border-transparent hover:text-accent hover:border-accent 
                                       transition-colors duration-300 group overflow-hidden'
                                data-id="<?= $note->id ?>" 
                                data-updated-at="<?= $note->updated_at ?>">
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
                                        <?php
                                          echo  previewHTML($note->content)
                                        ?>
                                    </p>
                                    <div class="flex justify-between items-center mt-5 px-4 pb-4">
                                        <p class="font-bold"><?= $note->likes?> likes</p>
                                        <div>
                                            <p class='text-xs text-slate-500'>Edited: <?= htmlspecialchars($note->updated_at) ?></p>
                                            <p class='text-xs text-slate-500'>Created: <?= htmlspecialchars($note->created_at) ?></p>
                                        </div>
                                    </div>
                                
                            </li>
                        <?php endforeach;
                        
                    } 
                    catch (Exception $e) {
                        echo "<li>Error fetching notes: {$e->getMessage()}</li>";
                    }
                    ?>
                </ul>
                <button 
                   id="load-more-btn" 
                    class="bg-accent hover:bg-accent-hover text-slate-800 py-2 px-3 rounded-xl transition-colors mx-auto "
                   >Load More</button>
            </div>
        </div>
    </div>


    <script src="loadMore.js?v=<?= time() ?>" defer></script>

</body>
</html>
