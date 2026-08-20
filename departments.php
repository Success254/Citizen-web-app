<?php
require_once __DIR__ . '/config/db.php';
$pageTitle = 'Departments';
include __DIR__ . '/includes/header.php';

$departments = $pdo->query("SELECT * FROM departments ORDER BY name ASC")->fetchAll();
?>

<h1>County Departments</h1>
<p class="subtitle">Browse departments to find their mandate and contact details.</p>

<div class="grid">
    <?php foreach ($departments as $d): ?>
        <div class="card">
            <h3><?php echo htmlspecialchars($d['name']); ?></h3>
            <p><?php echo htmlspecialchars(mb_strimwidth($d['description'] ?? '', 0, 120, '...')); ?></p>
            <a class="card-link" href="/department.php?id=<?php echo (int)$d['id']; ?>">View details &rarr;</a>
        </div>
    <?php endforeach; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
