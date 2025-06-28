<?php

require __DIR__ . '/src/config.php';

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes App</title>
</head>
<body>
    <h1>Notes App</h1>
    <p>Welcome to the Notes App!</p>

    <div>
        <h2>Add a Note</h2>
        <form action="add_note.php" method="POST">
            <div>
              <label for="title">Title:</label>
              <input type="text" name="title" placeholder="Title" required>
            </div>
            <div>
              <label for="content">Content:</label>
              <textarea name="content" placeholder="Content" required></textarea>
            </div>
            <div>
              <label for="author">Author:</label>
              <input type="text" name="author" placeholder="Author" required>
            </div>
            <button type="submit">Add Note</button>
        </form>
    </div>

    <div>
        <h2>Notes</h2>
        <ul>
            <?php
            $notes = $notes_repo->getAll();
            foreach ($notes as $note) {
                echo "<li>{$note->title} by {$note->author} on {$note->created_at}</li>";
            }
            ?>
        </ul>
    </div>
</body>
</html>
