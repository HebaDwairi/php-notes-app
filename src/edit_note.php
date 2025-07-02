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

if (isset($_GET['id'])) {
    $note = $notes_repo->findById((int)$_GET['id']);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = $_POST['content'];
    $id = $_POST['id'];

    if (empty($title) || empty($content)) {
        $_SESSION['message'] = 'All fields are required';
    }
    if (strlen($title) > 255) {
        $_SESSION['message'] = 'Title must be less than 255 characters';
        header('Location: index.php');
        exit;
    }
    

    try{
        $old_note = $notes_repo->findById($id);

        if($old_note->user_id == $_SESSION['user_id']) {
            $note = new Note($title, $content, '', $id);
            $notes_repo->update($note);
        }
        else {
            die("you don't have the permission to edit this note");
        }
    }
    catch(Exception $e){
        $_SESSION['message'] = 'Failed to update note';
    }

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
<body class="bg-slate-800 text-white">
    <?php include 'header.php'; ?>
    <div class="flex flex-col lg:flex-row gap-6 p-4 max-w-7xl mx-auto h-4/5">
        <div class=" mx-auto p-6 shadow-md rounded-lg space-y-4 mt-4 bg-slate-700 text-white w-full lg:w-2/3 ">
            <h2 class="text-lg font-bold">Edit a Note</h2>
            <form action="edit_note.php" method="POST">
                <div>
                    <label for="title">Title:</label>
                    <input
                        type="text"
                        name="title"
                        placeholder="Title"
                        required
                        maxlength="255"
                        value="<?php echo htmlspecialchars($note->title); ?>"
                        class="border bg-slate-700 border-slate-600 p-2 rounded w-full">
                </div>
                <div>
                    <label for="content">Content:</label>
                    <textarea
                        name="content"
                        placeholder="Content"
                        required
                        class="border bg-slate-700 border-slate-600 p-2 rounded w-full h-32"><?php echo htmlspecialchars($note->content); ?></textarea>
                </div>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($note->id); ?>">
                <?php echo $message ?? null ?>
                <button
                    type="submit"
                    class="bg-slate-600 w-full hover:bg-slate-500 text-white font-bold py-2 px-4 rounded mx-auto my-2">Update Note</button>
            </form>
        </div>
    </div>
</body>
</html>
