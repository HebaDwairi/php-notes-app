<?php

require __DIR__ . '/config.php';

session_start();
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
</head>
<body class="bg-slate-800 text-white">
    <h1 class="text-2xl font-bold p-4">Notes</h1>
    <div class="flex flex-col lg:flex-row gap-6 p-4 max-w-7xl mx-auto h-4/5">
        <div class=" mx-auto p-6 shadow-md rounded-lg space-y-4 mt-4 bg-slate-700 text-white w-full lg:w-1/3 ">
            <h2 class="text-lg font-bold">Add a Note</h2>
            <form action="add_note.php" method="POST">
                <div>
                  <label for="title">Title:</label>
                  <input
                    type="text"
                    name="title"
                    placeholder="Title"
                    required
                    maxlength="255"
                    class="border bg-slate-700 border-slate-600 p-2 rounded w-full">
                </div>
                <div>
                  <label for="content">Content:</label>
                  <textarea
                    name="content"
                    placeholder="Content"
                    required
                    class="border bg-slate-700 border-slate-600 p-2 rounded w-full h-32"></textarea>
                </div>
                <div>
                  <label for="author">Author:</label>
                  <input type="text"
                    name="author"
                    placeholder="Author"
                    required
                    class="border bg-slate-700 border-slate-600 p-2 rounded w-full">
                </div>
                <?php echo $message ?? null ?>
                <button
                  type="submit"
                  class="bg-slate-600 w-full hover:bg-slate-500 text-white font-bold py-2 px-4 rounded mx-auto my-2">Add Note</button>
            </form>
        </div>
        <div class="w-full lg:w-2/3 mx-auto p-6 shadow-md rounded-lg space-y-4 mt-4 bg-slate-700 text-white overflow-y-auto" style="max-height: calc(100vh - 140px);">
            <h2 class="text-lg font-bold">Notes</h2>
            <form action="index.php" method="get">
                <div class="border-b border-slate-600 pb-5">
                  <label>Search Notes:</label>
                  <input 
                    type="text"
                    name="q"
                    class="border bg-slate-700 border-slate-600 p-2 rounded w-full">
                </div>
            </form>
            <ul >
                <?php
                try {
                    $notes = $search_notes ?? $notes_repo->getAll();
                    foreach ($notes as $note) {
                        echo "<li class='p-2 border-b border-slate-600'>
                                <div class='flex justify-between items-center'>
                                    <div><strong>" . htmlspecialchars($note->title) . "</strong> by " . htmlspecialchars($note->author) . "</div>
                                    <div class='flex items-center space-x-2 '>
                                        <a href='edit_note.php?id={$note->id}' class='pt-2 px-2 text-blue-300 hover:underline text-sm ml-2'>Edit</a>
                                        <form action='delete_note.php' method='POST' class='mt-2'>
                                            <input type='hidden' name='id' value='{$note->id}'>
                                            <button 
                                                type='submit' 
                                                class='bg-red-400 hover:bg-red-500 text-white text-sm font-bold py-1 px-2 rounded-full'>X</button>
                                        </form>
                                    </div>
                                </div>
                                <br>
                                <span class='text-sm text-slate-300'>" . htmlspecialchars($note->content) . "</span>
                                <br>
                                <br>
                                <p class='text-xs text-slate-500'>Edited: " . htmlspecialchars($note->updated_at) . "</p>
                                <p class='text-xs text-slate-500'>Created: " . htmlspecialchars($note->created_at) . "</p>
                              </li>";
                    }
                } catch (Exception $e) {
                    echo "<li>Error fetching notes: {$e->getMessage()}</li>";
                }
                ?>
            </ul>
        </div>
    </div>
</body>
</html>
