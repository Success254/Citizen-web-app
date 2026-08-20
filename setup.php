<?php
// Run this ONCE after deploying, to create your first admin account.
// Delete this file (or rename it) immediately afterwards — leaving it
// live would let anyone create an admin account.

require_once __DIR__ . '/../config/db.php';

$done = false;
$error = '';

// Safety check: refuse to run if an admin already exists
$existing = $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($existing > 0) {
        $error = 'An admin account already exists. Delete this file for security.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || strlen($password) < 8) {
            $error = 'Username is required and password must be at least 8 characters.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                "INSERT INTO admins (username, password_hash, role) VALUES (?, ?, 'super_admin')"
            );
            $stmt->execute([$username, $hash]);
            $done = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Setup</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>
<main class="container" style="max-width:480px; padding-top:40px;">
    <h1>First-time Admin Setup</h1>

    <?php if ($existing > 0 && !$done): ?>
        <div class="alert error">An admin account already exists. Please delete this file.</div>
    <?php elseif ($done): ?>
        <div class="alert success">Admin account created. Please delete admin/setup.php now, then <a href="/admin/login.php">log in</a>.</div>
    <?php else: ?>
        <?php if ($error): ?><div class="alert error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form class="stack" method="post" action="/admin/setup.php">
            <div>
                <label for="username">Username</label><br>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Password (min 8 characters)</label><br>
                <input type="password" id="password" name="password" required minlength="8">
            </div>
            <button type="submit" class="btn">Create Admin Account</button>
        </form>
    <?php endif; ?>
</main>
</body>
</html>
