<?php
require_once __DIR__ . '/config/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: /index.php');
    exit;
}

$stmt = $pdo->prepare(
    "SELECT a.*, d.id AS dept_id, d.name AS department_name
     FROM announcements a
     LEFT JOIN departments d ON a.department_id = d.id
     WHERE a.id = ?"
);
$stmt->execute([$id]);
$announcement = $stmt->fetch();

if (!$announcement) {
    header('Location: /index.php');
    exit;
}

$pageTitle = $announcement['title'];
include __DIR__ . '/includes/header.php';
?>

<p class="breadcrumb"><a href="/index.php">&larr; Back to home</a></p>

<h1><?php echo htmlspecialchars($announcement['title']); ?></h1>
<p class="meta">
    <?php if ($announcement['department_name']): ?>
        <a href="/department.php?id=<?php echo (int)$announcement['dept_id']; ?>">
            <?php echo htmlspecialchars($announcement['department_name']); ?>
        </a> &middot;
    <?php endif; ?>
    <?php echo date('d M Y, g:i a', strtotime($announcement['created_at'])); ?>
</p>

<div class="card">
    <p><?php echo nl2br(htmlspecialchars($announcement['body'])); ?></p>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
