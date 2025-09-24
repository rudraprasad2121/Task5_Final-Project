<?php
require 'db.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $content = trim($_POST["content"] ?? "");

    if ($title === "" || $content === "") {
        $error = "All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$_SESSION["user_id"], $title, $content]);

            header("Location: view_posts.php");
            exit;
        } catch (Exception $e) {
            $error = "Failed to create post: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('bgg.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .form-container {
            background: rgba(255,255,255,0.95);
            padding: 30px;
            border-radius: 10px;
            max-width: 700px;
            margin: 50px auto;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="form-container">
        <h1 class="mb-3">Discover. Learn. Share. Grow.</h1>
        <p class="text-muted">Own Diary</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="" novalidate onsubmit="return validatePostForm();">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Content</label>
                <textarea name="content" id="content" class="form-control" rows="6" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">➕ Add Post</button>
            <a href="view_posts.php" class="btn btn-secondary"> View All Posts</a>
        </form>
    </div>
</div>

<script>
function validatePostForm() {
    const title = document.getElementById('title').value.trim();
    const content = document.getElementById('content').value.trim();
    if (!title || !content) {
        alert('Please fill in both Title and Content.');
        return false;
    }
    if (title.length > 255) {
        alert('Title must be 255 characters or less.');
        return false;
    }
    return true;
}
</script>

</body>
</html>
