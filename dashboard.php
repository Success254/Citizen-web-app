<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/db.php';

$message = '';

// Handle: create announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_announcement') {
    $title = trim($_POST['title'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $departmentId = filter_input(INPUT_POST, 'department_id', FILTER_VALIDATE_INT);

    if ($title !== '' && $body !== '') {
        $stmt = $pdo->prepare(
            "INSERT INTO announcements (title, body, department_id) VALUES (?, ?, ?)"
        );
        $stmt->execute([$title, $body, $departmentId ?: null]);
        $message = 'Announcement posted.';
    }
}

// Handle: delete announcement
if (($_GET['delete_announcement'] ?? null) && filter_var($_GET['delete_announcement'], FILTER_VALIDATE_INT)) {
    $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->execute([(int)$_GET['delete_announcement']]);
    header('Location: /admin/dashboard.php');
    exit;
}

// Handle: update feedback status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_feedback_status') {
    $feedbackId = filter_input(INPUT_POST, 'feedback_id', FILTER_VALIDATE_INT);
    $status = $_POST['status'] ?? '';
    if ($feedbackId && in_array($status, ['new', 'in_review', 'resolved'], true)) {
        $stmt = $pdo->prepare("UPDATE feedback SET status = ? WHERE id = ?");
        $stmt->execute([$status, $feedbackId]);
        $message = 'Feedback status updated.';
    }
}

$departments = $pdo->query("SELECT id, name FROM departments ORDER BY name ASC")->fetchAll();

$announcements = $pdo->query(
    "SELECT a.id, a.title, a.created_at, d.name AS department_name
     FROM announcements a LEFT JOIN departments d ON a.department_id = d.id
     ORDER BY a.created_at DESC"
)->fetchAll();

$feedbackList = $pdo->query(
    "SELECT f.*, d.name AS department_name
     FROM feedback f LEFT JOIN departments d ON f.department_id = d.id
     ORDER BY f.created_at DESC"
)->fetchAll();

$pageTitle = 'Admin Dashboard';
include __DIR__ . '/../includes/header.php';
?>

<h1>Admin Dashboard</h1>
<p class="subtitle">
    Logged in as <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>
    &middot; <a href="/admin/logout.php">Log out</a>
</p>

<?php if ($message): ?>
    <div class="alert success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<h2>Post a New Announcement</h2>
<form class="stack" method="post" action="/admin/dashboard.php">
    <input type="hidden" name="action" value="create_announcement">
    <div>
        <label for="title">Title</label><br>
        <input type="text" id="title" name="title" required>
    </div>
    <div>
        <label for="department_id">Department</label><br>
        <select id="department_id" name="department_id">
            <option value="">-- General --</option>
            <?php foreach ($departments as $d): ?>
                <option value="<?php echo (int)$d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for="body">Content</label><br>
        <textarea id="body" name="body" required></textarea>
    </div>
    <button type="submit" class="btn">Post Announcement</button>
</form>

<h2 style="margin-top:36px;">Existing Announcements</h2>
<table>
    <tr><th>Title</th><th>Department</th><th>Posted</th><th></th></tr>
    <?php foreach ($announcements as $a): ?>
        <tr>
            <td><?php echo htmlspecialchars($a['title']); ?></td>
            <td><?php echo htmlspecialchars($a['department_name'] ?? 'General'); ?></td>
            <td><?php echo date('d M Y', strtotime($a['created_at'])); ?></td>
            <td>
                <a href="/admin/dashboard.php?delete_announcement=<?php echo (int)$a['id']; ?>"
                   onclick="return confirm('Delete this announcement?');"
                   style="color:#a83232;">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<h2 style="margin-top:36px;">Citizen Feedback</h2>
<table>
    <tr><th>From</th><th>Department</th><th>Message</th><th>Status</th></tr>
    <?php foreach ($feedbackList as $f): ?>
        <tr>
            <td><?php echo htmlspecialchars($f['full_name']); ?><br><small><?php echo htmlspecialchars($f['email']); ?></small></td>
            <td><?php echo htmlspecialchars($f['department_name'] ?? 'General'); ?></td>
            <td><?php echo htmlspecialchars(mb_strimwidth($f['message'], 0, 100, '...')); ?></td>
            <td>
                <form method="post" action="/admin/dashboard.php" style="display:flex; gap:6px; align-items:center;">
                    <input type="hidden" name="action" value="update_feedback_status">
                    <input type="hidden" name="feedback_id" value="<?php echo (int)$f['id']; ?>">
                    <span class="status-badge status-<?php echo $f['status']; ?>"><?php echo $f['status']; ?></span>
                    <select name="status" onchange="this.form.submit()">
                        <option value="new" <?php echo $f['status']==='new'?'selected':''; ?>>New</option>
                        <option value="in_review" <?php echo $f['status']==='in_review'?'selected':''; ?>>In review</option>
                        <option value="resolved" <?php echo $f['status']==='resolved'?'selected':''; ?>>Resolved</option>
                    </select>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include __DIR__ . '/../includes/footer.php'; ?>
