<?php
require_once __DIR__ . '/session.php';
require __DIR__ . '/config.php';
header('Content-Type: application/json');


function previewHTML($html, $limit = 200) {
    $text = strip_tags($html);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim(mb_substr($text, 0, $limit));
    return htmlspecialchars($text);
}




$updated_at = $_GET['updated_at'] ?? null;
$id = $_GET['id'] ?? null;
$is_homepage = $_GET['is_homepage']?? null;
$user_id = $_GET['user_id'] ?? null;
$limit = $_GET['limit']?? 10;


try {
    $notes= [];

    if ($is_homepage) {
        $notes = $notes_repo->getOlderThan($updated_at, $id, $limit);
    } else {
        $notes = $notes_repo->getOlderThanByUser($user_id, $updated_at, $id,  $limit);
    }

    echo json_encode([
        'success' => true,
        'notes' => array_map(function($note){
            return [
                'title' => htmlspecialchars($note->title),
                'content' => previewHTML($note->content),
                'user_id' => $note->user_id,
                'id' => $note->id,
                'created_at' => $note->created_at,
                'updated_at' => $note->updated_at,
                'slug' => $note->slug,
                'image_path' => $note->image_path,
            ];
        }, $notes)
    ]);
    
    
} 
catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error loading more notes'
    ]);
}