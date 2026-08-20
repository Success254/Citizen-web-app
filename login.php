<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';

// Already logged in
if (!empty($_SESSION['admin_id'])) {
    header('Location: /admin/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        // Prevent session fixation
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_role'] = $admin['role'];
        header('Location: /admin/dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}

$pageTitle = 'Admin Login';
include __DIR__ . '/../includes/header.php';
?>

<h1>Admin Login</h1>

<?php if ($error): ?>
    <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form class="stack" method="post" action="/admin/login.php">
    <div>
        <label for="username">Username</label><br>
        <input type="text" id="username" name="username" required autofocus>
    </div>
    <div>
        <label for="password">Password</label><br>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" class="btn">Log in</button>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
