<?php
// Include at the top of any admin-only page.
if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}
