<?php
require 'db.php';
session_start();

if (isset($_SESSION['username']) && $_SESSION['username'] === 'omm') {
    die("❌ You are not allowed to edit posts.");
}


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Error: Post ID not provided.");
}

$id = (int)$_GET['id'];

// Fetch post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    die("Error: Post not found.");
}

$isOwner = ($post['user_id'] == $_SESSION['user_id']);
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
if (!($isOwner || $isAdmin)) {
    die("Error: You do not have permission to edit this post.");
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? "");
    $content = trim($_POST['content'] ?? "");

    if ($title === "" || $content === "") {
        $error = "All fields are required.";
    } else {
        try {
            $update = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
            $update->execute([$title, $content, $id]);
            header("Location: view_posts.php?msg=" . urlencode("Post updated successfully."));
            exit;
        } catch (Exception $e) {
            $error = "Error updating post: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Edit Post</h3>
            </div>
            <div class="card-body">
                <form method="POST" onsubmit="return validateEdit();">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" id="title" 
                               value="<?php echo htmlspecialchars($post['title']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" name="content" id="content" rows="6" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Update Post</button>
                    <a href="view_posts.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

<script>
function validateEdit(){
    const t = document.getElementById('title').value.trim();
    const c = document.getElementById('content').value.trim();
    if(!t || !c){
        alert('Both title and content are required.');
        return false;
    }
    return true;
}
</script>
</body>
</html>
