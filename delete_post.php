<?php
require 'db.php';
session_start();
        session_start();
        if (isset($_SESSION['username']) && $_SESSION['username'] === 'omm') {
    die("❌ You are not allowed to delete posts.");
}
 

if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to delete posts.");
}

if (!isset($_GET['id'])) {
    die("Error: Post ID not provided.");
}

$id = (int)$_GET['id'];

// Fetch post via PDO
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    die("Error: Post not found.");
}

$isOwner = ($post['user_id'] == $_SESSION['user_id']);
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

if (!($isOwner || $isAdmin)) {
    die("Error: You do not have permission to delete this post.");
}

$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
if ($stmt->execute([$id])) {
    header("Location: view_posts.php?msg=" . urlencode("Post deleted successfully."));
    exit;
} else {
    echo "Error deleting post.";
}
?>
