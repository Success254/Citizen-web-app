<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : ''; ?>Homa Bay County Citizen Portal</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="/index.php" class="brand">Homa Bay County <span>Citizen Portal</span></a>
        <nav>
            <a href="/index.php">Home</a>
            <a href="/departments.php">Departments</a>
            <a href="/feedback.php">Feedback</a>
            <a href="/admin/login.php">Admin</a>
        </nav>
    </div>
</header>
<main class="container">
