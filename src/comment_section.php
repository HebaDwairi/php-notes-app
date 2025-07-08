<div class=" p-4 rounded-lg  flex flex-col">
    <h2 class="font-bold text-xl border-b w-full border-slate-500 pb-4 text-center">Comments</h2>
    <form action="add_comment.php" method="POST">
        <div class="m-4 bg-slate-800/60 rounded-xl p-4 ">
            <label class="text-slate-300/70 font-bold " for="comment">Add a comment</label>
            <div class="flex gap-4">
                <textarea
                    name="content"
                    placeholder="write your comment here..."
                    class="bg-slate-600 p-2 rounded-xl w-full border-2 border-slate-500"
                    rows=1></textarea>
                <input type="hidden" value=<?= $note->id?> name="note_id">
                <button type="submit"
                class="bg-teal-500 hover:bg-teal-400 text-slate-800 font-bold py-3 px-4 rounded-xl transition-colors">Add</button>
            </div>
        </div>
    </form>


    <ul>
        <?php foreach($comments as $c):?>
            <?php if($c->status == "approved"): ?>
                <li class="bg-slate-600 rounded-lg p-2 px-4 mx-4 mb-2">
                    <h2 class="font-bold text-slate-300/80 "><?= htmlspecialchars($c->is_guest? $c->guest_name : $c->username) ?></h2>
                    <div class="flex items-center justify-between">
                        <p class="rounded-xl mx-2"><?= htmlspecialchars($c->content) ?></p>
                        <p class="text-sm text-slate-300/50"><?= date("d-m-Y", strtotime($c->created_at)) ?></p>
                    </div>
                </li>
            <?php elseif(($c->status == 'pending') && !empty($_SESSION['user_id']) && ($_SESSION['user_id'] == $c->user_id)): ?>
                <li class="bg-slate-600 rounded-lg p-2 px-4 mx-4 mb-2">
                    <div class="flex justify-between">
                        <h2 class="font-bold text-slate-300/80 "><?= htmlspecialchars($c->is_guest? $c->guest_name : $c->username) ?></h2>
                        <div class="space-x-1 my-2">
                            <p class='bg-slate-800/60 text-white text-sm font-bold py-1 px-2 rounded-full'>
                                Pending
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="rounded-xl mx-2"><?= htmlspecialchars($c->content) ?></p>
                        <p class="text-sm text-slate-300/50"><?= date("d-m-Y", strtotime($c->created_at)) ?></p>
                    </div>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>
