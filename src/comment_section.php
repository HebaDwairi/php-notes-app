<div class=" p-4 rounded-lg flex flex-col ">
    <h2 class="font-bold text-xl w-full pb-4 text-center">Comments</h2>
    <form class="bg-slate-800/20 rounded-xl p-4 border-b-2 border-slate-600 my-4">
        <label class="text-slate-300/70 font-bold ">Add a comment</label>
        <div class="flex gap-4">
            <textarea
                name="content"
                id="comment-content"
                placeholder="write your comment here..."
                class="bg-slate-600 p-2 rounded-xl w-full border-2 border-slate-500"
                rows=1></textarea>
            <button id="add-comment-btn"
            class="bg-teal-500 hover:bg-teal-400 text-slate-800 font-bold py-3 px-4 rounded-xl transition-colors">Add</button>
        </div>
    </form>

    <ul id="comments-list">
        <?php foreach($comments as $c):?>
            <li class="bg-slate-800/20 rounded-lg p-2 px-4 mb-2 flex gap-4 items-center border-b-2 border-slate-600">
                <svg class='w-8 h-8 fill-slate-300/80' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M399 384.2C376.9 345.8 335.4 320 288 320l-64 0c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z"/></svg>
                <div class=" flex-1">
                    <div class="flex justify-between">
                        <h2 class="font-bold text-slate-300 mb-2"><?= htmlspecialchars($c->is_guest? $c->guest_name : $c->username) ?></h2>
                        <div class="space-x-1 my-2">
                        
                        <?php if(($c->status == 'pending') && !empty($_SESSION['user_id']) && ($_SESSION['user_id'] == $c->user_id)): ?>
                            <p class='bg-red-500/60 text-white text-sm font-bold py-1 px-2 rounded-full'>
                                Pending
                            </p>
                        <?php endif; ?>
                        <?php if((!empty($_SESSION['is_admin'])) && (!empty($_SESSION['user_id'])) && ($_SESSION['user_id'] == $c->user_id)): ?>
                            <a href="/delete_comment.php?id=<?= $c->id ?>" class='bg-red-400 hover:bg-red-500 text-white text-sm font-bold p-1 rounded-full'>
                                Delete
                            </a>
                        <?php endif; ?>
                    </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-300/80"><?= htmlspecialchars($c->content) ?></p>
                        <p class="text-sm text-slate-300/50"><?= date("d-m-Y", strtotime($c->created_at)) ?></p>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<script>
    const addCommentBtn = document.querySelector('#add-comment-btn');
    const commentContent = document.querySelector('#comment-content');
    const commentsList = document.querySelector('#comments-list');

    addCommentBtn.addEventListener("click", async function (event) {
        event.preventDefault();
        
        if(!commentContent.value) return;

        fetch("/add_comment.php", {
            method: "post",
            headers: {
            "Content-Type": "application/json"
            },
            body: JSON.stringify({
                note_id: <?= $note->id ?>,
                content: commentContent.value
            })
            })
            .then(response => response.json())
            .then(data => {
                const comment = document.createElement('li');
                comment.className = "bg-slate-800/20 rounded-lg p-2 px-4 mb-2 flex gap-4 items-center border-b-2 border-slate-600";
                comment.innerHTML =`<svg class='w-8 h-8 fill-slate-300/80' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M399 384.2C376.9 345.8 335.4 320 288 320l-64 0c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z"/></svg>
                                    <div class=" flex-1">
                                        <div class="flex justify-between">
                                            <h2 class="font-bold text-slate-300 mb-2">${data.is_guest? data.guest_name : data.username}</h2>
                                            <div class="space-x-1 my-2">
                                            <p class='bg-red-500/60 text-white text-sm font-bold py-1 px-2 rounded-full'>
                                                Pending
                                            </p>
                                        </div>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm text-slate-300/80">${data.comment}</p>
                                            <p class="text-sm text-slate-300/50">${data.created_at}</p>
                                        </div>
                                    </div>`;

                commentsList.insertBefore(comment, commentsList.firstChild);
                commentContent.value = "";
            });
    });
</script>