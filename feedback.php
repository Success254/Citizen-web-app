<?php
require_once __DIR__ . '/config/db.php';
$pageTitle = 'Feedback & Complaints';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $departmentId = filter_input(INPUT_POST, 'department_id', FILTER_VALIDATE_INT);
    $message = trim($_POST['message'] ?? '');

    if ($fullName === '') $errors[] = 'Please enter your name.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if ($message === '') $errors[] = 'Please enter your message.';

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "INSERT INTO feedback (full_name, email, department_id, message)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$fullName, $email, $departmentId ?: null, $message]);
        $success = true;
        // Clear posted values after a successful submit
        $fullName = $email = $message = '';
        $departmentId = null;
    }
}

$departments = $pdo->query("SELECT id, name FROM departments ORDER BY name ASC")->fetchAll();
include __DIR__ . '/includes/header.php';
?>

<h1>Feedback & Complaints</h1>
<p class="subtitle">Send a message to the relevant county department. We'll route it accordingly.</p>

<?php if ($success): ?>
    <div class="alert success">Thank you — your message has been submitted.</div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="alert error">
        <?php foreach ($errors as $e) echo '<div>' . htmlspecialchars($e) . '</div>'; ?>
    </div>
<?php endif; ?>

<form class="stack" method="post" action="/feedback.php">
    <div>
        <label for="full_name">Full name</label><br>
        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($fullName ?? ''); ?>" required>
    </div>
    <div>
        <label for="email">Email address</label><br>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
    </div>
    <div>
        <label for="department_id">Department (optional)</label><br>
        <select id="department_id" name="department_id">
            <option value="">-- General / Not sure --</option>
            <?php foreach ($departments as $d): ?>
                <option value="<?php echo (int)$d['id']; ?>" <?php echo (isset($departmentId) && $departmentId == $d['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($d['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for="message">Message</label><br>
        <textarea id="message" name="message" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
    </div>
    <button type="submit" class="btn">Submit</button>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
