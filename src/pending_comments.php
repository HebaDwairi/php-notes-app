<?php 
try {
    $commentsRepo = new CommentRepository();
    $comments = $commentsRepo->findAllPending();
}
catch(Exception $e) {
    die("couldn'g access pending comments list" . $e->getMessage());
}
 
?>


<?php if(!empty($_SESSION['is_admin'])): ?>
    <div class="flex flex-col w-full lg:w-1/3 p-6 shadow-md rounded-xl bg-slate-700 space-y-4 ">
        <h2 class="text-slate-300 font-bold text-lg">pending comments</h2>
        <ul>
            <?php 
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