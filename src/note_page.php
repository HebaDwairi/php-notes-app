<?php
require_once __DIR__ . '/session.php';
require __DIR__ . '/config.php';
require __DIR__  . "/User.php";
require __DIR__  . "/Comment.php";
require __DIR__  . "/Like.php";


$likes = new Likes();

if (isset($_GET['slug'])) {
    $note = $notes_repo->findBySlug($_GET['slug']);

    $comments_repo = new CommentRepository();
    $comments = $comments_repo->findAllByNoteId($note->id);
    
}
else {
    header('Location: /');
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
    <style>
    .note-content ul {
        list-style-type: disc;
        padding-left: 1.5rem;
        margin-bottom: 1rem;
    }

    .note-content ol {
        list-style-type: decimal;
        padding-left: 1.5rem;
        margin-bottom: 1rem;
    }

    .note-content li {
        margin-bottom: 0.25rem;
    }
</style>


</head>
<body class="bg-slate-800 text-slate-300 min-h-screen">
    <?php include 'header.php'; ?>
    <div class="flex flex-col  gap-6 p-4 max-w-7xl mx-auto h-4/5">
        <div class=" mx-auto p-8 shadow-md rounded-xl space-y-4 mt-4 bg-slate-700 text-white w-full lg:w-2/3 ">
            <div class="flex justify-between">
                <div class="border-b-2 border-slate-600 ">
                    <h2 class="text-lg font-bold"><?= $note->title ?></h2>
                    <p class=' text-slate-400 mt-5 '>By: <?= $note->username ?></p>
                </div>
                
                <?php if(isset($_SESSION['user_id']) && ($note->user_id == $_SESSION['user_id'])): ?>
                    <div class='flex space-x-2 '>
                    <a href='/notes/edit/<?= $note->id?>' class='pt-3 px-2 text-blue-300 hover:underline  ml-2'>Edit</a>
                    <form action='/delete_note.php' method='POST' class='mt-2'>
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
                    <img src="<?= htmlspecialchars($note->image_path) ?>" class="max-w-full object-contain rounded-xl">
                <?php endif; ?>
                <div class="note-content pt-4">
                    <?= $note->content ?>
                </div>
                

                <div class="flex justify-between items-center border-y-2 my-6 p-3 border-slate-600 rounded-lg">
                    <div>
                        <p class='text-sm text-slate-500'>Edited: <?= $note->updated_at ?></p>
                        <p class='text-sm text-slate-500'>Created: <?= $note->created_at ?></p>
                    </div>
                    <div class="flex flex-col items-center mr-4">
                        <?php if(!empty($_SESSION['user_id'])): ?>
                            <button id="like-btn">
                                <div class="w-8 h-8" id="heart-icon">
                                    <?php if($likes->isNoteLiked($_SESSION['user_id'], $note->id) ): ?>
                                        <svg  class="fill-red-400 hover:fill-white hover:scale-125 transition-all" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z"/></svg>
                                    <?php else: ?>
                                        <svg  class="fill-white hover:fill-red-400 hover:scale-125 transition-all" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8l0-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5l0 3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20-.1-.1s0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5l0 3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2l0-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z"/></svg>
                                    <?php endif; ?>
                                </div>
                            </button>
                        <?php endif;?>
                        <p class="font-black" ><span id="likes-count"><?= $note->likes ?> </span> likes</p>
                    </div>
                </div>
            </div>

            <?php include __DIR__ . "/comment_section.php" ?>

        </div>
    </div>
    <script>
        const likeBtn = document.querySelector("#like-btn");
        const likesCount = document.querySelector("#likes-count");
        const heartIcon = document.querySelector("#heart-icon");
        

        likeBtn.addEventListener('click', async function () {
            fetch("/like_note.php", {
            method: "post",
            headers: {
            "Content-Type": "application/json"
            },
            body: JSON.stringify({note_id: <?= $note->id ?>})
            })
            .then(response => response.json())
            .then(data => {
              console.log("Response body:", data);
              likesCount.textContent = parseInt(likesCount.textContent) + (data.liked ? 1 : -1);
              
              heartIcon.innerHTML = data.liked? 
                `<svg  class="fill-red-400 hover:fill-white hover:scale-125 transition-all" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z"/></svg>`
                :
                `<svg  class="fill-white hover:fill-red-400 hover:scale-125 transition-all" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8l0-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5l0 3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20-.1-.1s0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5l0 3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2l0-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z"/></svg>`
            });

        });
        
        
    </script>
</body>
</html>