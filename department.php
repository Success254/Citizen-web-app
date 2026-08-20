<?php
require_once __DIR__ . '/config/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: /departments.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM departments WHERE id = ?");
$stmt->execute([$id]);
$department = $stmt->fetch();

if (!$department) {
    header('Location: /departments.php');
    exit;
}

// This department's announcements
$stmt = $pdo->prepare(
    "SELECT id, title, body, created_at FROM announcements
     WHERE department_id = ? ORDER BY created_at DESC"
);
$stmt->execute([$id]);
$announcements = $stmt->fetchAll();

$pageTitle = $department['name'];
include __DIR__ . '/includes/header.php';
?>

<p class="breadcrumb"><a href="/departments.php">&larr; All departments</a></p>

<h1><?php echo htmlspecialchars($department['name']); ?></h1>
<p><?php echo nl2br(htmlspecialchars($department['description'] ?? 'No description available.')); ?></p>

<div class="card" style="max-width:420px;">
    <h3>Contact</h3>
    <p>
        Email: <?php echo htmlspecialchars($department['contact_email'] ?? 'N/A'); ?><br>
        Phone: <?php echo htmlspecialchars($department['contact_phone'] ?? 'N/A'); ?>
    </p>
</div>

<h2 style="margin-top:32px;">Announcements from this department</h2>
<?php if (empty($announcements)): ?>
    <p>No announcements from this department yet.</p>
<?php else: ?>
    <?php foreach ($announcements as $a): ?>
        <div class="card">
            <h3><?php echo htmlspecialchars($a['title']); ?></h3>
            <p class="meta"><?php echo date('d M Y', strtotime($a['created_at'])); ?></p>
            <p><?php echo htmlspecialchars(mb_strimwidth($a['body'], 0, 140, '...')); ?></p>
            <a class="card-link" href="/announcement.php?id=<?php echo (int)$a['id']; ?>">Read more &rarr;</a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
