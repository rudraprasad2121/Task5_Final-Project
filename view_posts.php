<?php
include 'db.php';
session_start();

// Pagination settings
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Search
$search = isset($_GET['q']) ? trim($_GET['q']) : "";

// Fetch posts with search & pagination
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM posts 
                           WHERE title LIKE :search OR content LIKE :search
                           ORDER BY created_at DESC
                           LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
} else {
    $stmt = $pdo->prepare("SELECT * FROM posts 
                           ORDER BY created_at DESC
                           LIMIT :limit OFFSET :offset");
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

// Count total posts
if ($search) {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM posts 
                                WHERE title LIKE :search OR content LIKE :search");
    $countStmt->execute([':search' => "%$search%"]);
} else {
    $countStmt = $pdo->query("SELECT COUNT(*) FROM posts");
}
$totalPosts = $countStmt->fetchColumn();
$totalPages = ceil($totalPosts / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-buttons { display: flex; gap: 10px; margin-top: 10px; }
    </style>
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2 class="mb-4">All Posts</h2>
    <a href="add_post.php" class="btn btn-primary mb-3">➕ Add New Post</a>

    <!-- Search Form -->
    <form method="GET" action="view_posts.php" class="mb-4 d-flex">
        <input type="text" name="q" class="form-control me-2" placeholder="🔍 Search posts..." 
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-outline-secondary">Search</button>
    </form>

    <?php if ($posts): ?>
        <?php foreach ($posts as $row): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted">
                        Posted on: <?= $row['created_at'] ?>
                    </h6>
                    <p class="card-text"><?= nl2br(htmlspecialchars($row['content'])) ?></p>

                    <!-- Buttons at bottom: hidden only for user 'omm' -->
                    <?php if (isset($_SESSION['username']) && $_SESSION['username'] !== 'omm'): ?>
                        <div class="card-buttons">
                            <a href="edit_post.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete_post.php?id=<?= $row['id'] ?>" 
                               onclick="return confirm('Are you sure you want to delete this post?');" 
                               class="btn btn-sm btn-danger">Delete</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="alert alert-info">No posts found.</p>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="view_posts.php?page=<?= $i ?>&q=<?= urlencode($search) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
</body>
</html>
                    