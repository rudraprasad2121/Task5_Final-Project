<?php
require 'db.php'; // PDO connection
session_start();

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Fetch user from DB
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();

    if ($user) {
        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header("Location: view_posts.php");
            exit;
        } else {
            header("Location: view_posts.php");
        }
    } else {
        $error = "❌ Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-card {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            padding: 30px;
            background: #fff;
            width: 100%;
            max-width: 380px;
        }
        .login-btn {
            background: linear-gradient(45deg, #007bff, #00c6ff);
            border: none;
            font-weight: bold;
        }
        .login-btn:hover {
            background: linear-gradient(45deg, #0056b3, #0096c7);
        }
        .form-label { font-weight: 600; }
    </style>
</head>
<body>

<div class="login-card">
    <h3 class="text-center mb-4">🔑 User Login</h3>

    <?php if (!empty($error)) { ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php } ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">👤 Username</label>
            <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
        </div>

        <div class="mb-3">
            <label class="form-label">🔒 Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
        </div>

        <button type="submit" name="login" class="btn btn-primary login-btn w-100">Login</button>
    </form>

    <p class="text-center mt-3">
        Don’t have an account? <a href="register.php">Register</a>
    </p>
</div>

</body>
</html>
