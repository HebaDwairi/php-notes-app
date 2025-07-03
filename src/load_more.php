
<?php
require_once __DIR__ . '/session.php';
require __DIR__ . '/config.php';

header('Content-Type: application/json');

$last_timestamp = $_GET['timestamp'] ?? null;
$user_notes = isset($_GET['user_notes']) && $_GET['user_notes'] === 'true';
$limit = 10;

try {
    if ($user_notes && isset($_SESSION['user_id'])) {
        $notes = $notes_repo->getUserNotesOlderThan($_SESSION['user_id'], $last_timestamp, $limit);
    } else {
        $notes = $notes_repo->getOlderThan($last_timestamp, $limit);
    }
    
    $html = '';
    $last_time = null;
    
    foreach ($notes as $note) {
        $last_time = $note->updated_at;
        $is_owner = isset($_SESSION['user_id']) && $note->user_id == $_SESSION['user_id'];
        
        $controls = '';
        if ($is_owner) {
            $controls = '
                <div class="flex items-center space-x-2">
                    <a href="edit_note.php?id='.$note->id.'" class="text-teal-300 hover:underline text-sm">Edit</a>
                    <form action="delete_note.php" method="POST" class="inline">
                        <input type="hidden" name="id" value="'.$note->id.'">
                        <button type="submit" class="bg-red-400 hover:bg-red-500 text-white text-sm py-1 px-2 rounded-full">Delete</button>
                    </form>
                </div>
            ';
        }
        
        $image = '';
        if (!empty($note->image_path)) {
            $image = '<img src="'.htmlspecialchars($note->image_path).'" 
                alt="'.htmlspecialchars($note->title).'" 
                class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300 rounded-t-xl">';
        }
        
        $html .= '
            <li class="bg-slate-800/50 rounded-xl border border-transparent hover:text-accent hover:border-accent transition-colors duration-300 group overflow-hidden">
                <a href="note_page.php?slug='.$note->slug.'" class="block">
                    '.$image.'
                    <div class="flex justify-between items-center p-4">
                        <div>
                            <strong>'.htmlspecialchars($note->title).'</strong> 
                            by '.htmlspecialchars($note->username).'
                        </div>
                        '.$controls.'
                    </div>
                    <p class="text-sm text-slate-300 p-4">
                        '.htmlspecialchars(mb_substr(strip_tags($note->content), 0, 150)).'
                        '.(mb_strlen(strip_tags($note->content)) > 150 ? '...' : '').'
                    </p>
                </a>
            </li>
        ';
    }
    
    echo json_encode([
        'success' => true,
        'html' => $html,
        'has_more' => count($notes) >= $limit,
        'last_timestamp' => $last_time
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error loading more notes'
    ]);
}