<?php
require_once __DIR__ . '/session.php';
require __DIR__ . '/config.php';
require __DIR__  . "/User.php";


if (isset($_GET['slug'])) {
    $note = $notes_repo->findBySlug($_GET['slug']);
}
else {
     header('Location: index.php');
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-800 text-slate-300 min-h-screen">
    <?php include 'header.php'; ?>
    <div class="flex flex-col lg:flex-row gap-6 p-4 max-w-7xl mx-auto h-4/5">
        <div class=" mx-auto p-6 shadow-md rounded-lg space-y-4 mt-4 bg-slate-700 text-white w-full lg:w-2/3 ">
            <div class="flex justify-between">
                <div class="border-b border-slate-500 ">
                    <h2 class="text-lg font-bold"><?= $note->title ?></h2>
                    <p class=' text-slate-400 mt-5 '>By: <?= $note->username ?></p>
                </div>
                <?php if(isset($_SESSION['user_id']) && ($note->user_id == $_SESSION['user_id'])): ?>
                    <div class='flex  space-x-2 '>
                    <a href='edit_note.php?id=<?= $note->id?>' class='pt-3 px-2 text-blue-300 hover:underline  ml-2'>Edit</a>
                    <form action='delete_note.php' method='POST' class='mt-2'>
                        <input type='hidden' name='id' value=<?= $note->id ?>>
                        <button
                            type='submit'
                            class='bg-red-400 hover:bg-red-500 text-white text-sm font-bold py-1 px-2 rounded-full'>Delete</button>
                    </form>
                </div>
                <?php endif;?>
            </div>
            <div>
                <?php if(!empty($note->image_path)): ?>
                    <img src="<?= htmlspecialchars($note->image_path) ?>" class="max-w-full max-h-96 object-contain">
                <? endif; ?>
                <div class=" whitespace-pre-line">
                    <?= $note->content ?>
                </div>

                <p class='text-sm text-slate-500 mt-5'>Edited: <?= $note->updated_at ?></p>
                <p class='text-sm text-slate-500'>Created: <?= $note->created_at ?></p>
            </div>

        </div>
    </div>
</body>
</html>
