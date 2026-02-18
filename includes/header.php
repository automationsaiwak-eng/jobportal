<?php
require_once __DIR__ . '/auth.php';
$user = current_user();
$flash = consume_flash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Modern job portal for candidates and employers">
    <title><?= e($title ?? 'Job Portal') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="/index.php"><i class="bi bi-briefcase-fill me-1"></i>JobPortal</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="/browse-jobs.php">Jobs</a></li>
                <li class="nav-item"><a class="nav-link" href="/employers.php">Employers</a></li>
                <li class="nav-item"><a class="nav-link" href="/about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="/contact.php">Contact</a></li>
                <?php if ($user): ?>
                    <?php $dash = $user['role'] === 'admin' ? '/admin/dashboard.php' : ($user['role'] === 'employer' ? '/employer/dashboard.php' : '/seeker/dashboard.php'); ?>
                    <li class="nav-item"><a class="btn btn-outline-primary btn-sm" href="<?= $dash ?>">Dashboard</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm" href="/logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="btn btn-outline-primary btn-sm" href="/login.php">Login</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm" href="/register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="py-4">
    <div class="container">
        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
