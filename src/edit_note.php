<?php
require_once __DIR__ . '/session.php';
require __DIR__ . '/config.php';



if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
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
    $id = $_POST['id'];
    require __DIR__ . '/process_note_form.php';
    
    try{
        $old_note = $notes_repo->findById($id);

        if(!$image_path) {
            $image_path = $old_note->image_path;
        }

        if($old_note->user_id == $_SESSION['user_id']) {
            $note = new Note(title: $title, content: $content, id: $id, image_path: $image_path, user_id: '');
            $notes_repo->update($note);
        }
        else {
            die("you don't have the permission to edit this note");
        }
    }
    catch(Exception $e){
        $_SESSION['message'] = 'Failed to update note';
    }

    header('Location: /notes');
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.tiny.cloud/1/9kxz2wr0h6q5fm3aw7fas6b73nhrzr7pl2hvp2fiyaar3ux0/tinymce/6/tinymce.min.js"></script>
    <script>
    tinymce.init({
        selector: 'textarea[name="content"]',
        plugins: 'link image lists table',
        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent',
        content_style: "body { background-color: #1E293B; color:white;}",
        skin: 'oxide-dark',
        content_css: 'dark',
    });
    </script>
    

</head>
<body class="bg-slate-800 text-white">
    <?php include 'header.php'; ?>
    <div class="flex flex-col lg:flex-row gap-6 p-4 max-w-7xl mx-auto h-4/5">
        <div class=" mx-auto p-6 shadow-md rounded-lg space-y-4 mt-4 bg-slate-700 text-white w-full lg:w-2/3 ">
            <h2 class="text-lg font-bold">Edit a Note</h2>
            <form action="/edit_note.php" method="POST" class="space-y-4" enctype="multipart/form-data">
                <div>
                    <label for="title" class="block text-slate-400 mb-1">Title:</label>
                    <input
                        type="text"
                        name="title"
                        placeholder="Title"
                        required
                        maxlength="255"
                        value="<?php echo htmlspecialchars($note->title); ?>"
                        class="border bg-slate-700 border-slate-600 p-2 rounded w-full">
                </div>
                <?php if(!empty($note->image_path)): ?>
                    <img src="<?= htmlspecialchars($note->image_path) ?>" class="max-w-full max-h-72 object-contain">
                <?php endif; ?>
                 <div>
                    <label for="image"class="block text-slate-400 mb-1">Image:</label>
                    <input type="file" name="image" accept="image/*" 
                        class="block w-full text-sm text-slate-300 file:mr-4 file:py-1 file:px-2
                            file:rounded-lg file:border-0
                            file:bg-teal-400 hover:file:bg-teal-300">
                    </div>
                <div>
                    <label for="content" class="block text-slate-400 mb-1">Content:</label>
                    <textarea
                        name="content"
                        placeholder="Content"
                        required
                        class="border bg-slate-700 border-slate-600 p-2 rounded w-full h-52"><?php echo htmlspecialchars($note->content); ?></textarea>
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
