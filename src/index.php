<?php
require_once __DIR__ . '/session.php';
require __DIR__ . '/config.php';
require __DIR__ . '/Comment.php';



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
<body class="bg-slate-800 text-slate-300 min-h-screen" data-page="home">

    <?php include 'header.php'; ?>

    <div class="mx-auto max-w-7xl lg:p-8">
        <div class="flex justify-center gap-4">
        
            <div class="flex flex-col w-full lg:w-2/3 p-6 shadow-md rounded-xl bg-slate-700 space-y-4 ">
                <h2 class="text-xl font-bold">Latest Notes</h2>
                
                <ul class="space-y-4 " id="notes-list">
                    <?php
                    try {
                        $notes = $search_notes ?? $notes_repo->getOlderThan(null, null, 10);
                        foreach ($notes as $note): ?>
                            <li class='bg-slate-800/50 rounded-xl border border-transparent hover:text-accent hover:border-accent transition-colors duration-300 group overflow-hidden'
                            data-id="<?= $note->id ?>" 
                            data-updated-at="<?= $note->updated_at ?>">

                                <a href="notes/<?= $note->slug ?> " class="block ">
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
                                </a>
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
            <?php if(!empty($_SESSION['is_admin'])): ?>
                <div class="flex flex-col w-full lg:w-1/3 p-6 shadow-md rounded-xl bg-slate-700 space-y-4 ">
                    <h2 class="text-slate-300 font-bold text-lg">pending comments</h2>
                    <ul>
                        <?php 
                        $commentsRepo = new CommentRepository();
                        $comments = $commentsRepo->findAllPending();
                        foreach($comments as $c):?>
                            <li class="bg-slate-600 rounded-lg p-2 px-4 mb-2 ">
                                <div class="flex justify-between">
                                    <h2 class="font-bold text-slate-300/80 "><?= htmlspecialchars($c->is_guest? $c->guest_name : $c->username) ?></h2>
                                    <div class="space-x-1 my-2">
                                        <a href="approve_comment.php?id=<?= $c->id ?>" class='bg-teal-400 hover:bg-teal-500 text-white text-sm font-bold py-1 px-2 rounded-full'>
                                            Approve
                                        </a>
                                        <a href="delete_comment.php?id=<?= $c->id ?>" class='bg-red-400 hover:bg-red-500 text-white text-sm font-bold py-1 px-2 rounded-full'>
                                            Reject
                                        </a>
                                    </div>
                                </div>
                                <a href="">
                                    <div class="flex items-center justify-between">
                                        <p class="rounded-xl mx-2"><?= htmlspecialchars($c->content) ?></p>
                                        <p class="text-sm text-slate-300/50"><?= date("d-m-Y", strtotime($c->created_at)) ?></p>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>

<script src="loadMore.js?v=<?= time() ?>" defer></script>
</body>
</html>
