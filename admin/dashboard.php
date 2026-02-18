<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_auth(['admin']);
$db = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (isset($_POST['approve_job_id'])) {
        $stmt = $db->prepare("UPDATE jobs SET status='approved' WHERE id=:id");
        $stmt->execute(['id' => (int) $_POST['approve_job_id']]);
        flash('success', 'Job approved successfully.');
    }
    if (isset($_POST['delete_user_id'])) {
        $stmt = $db->prepare('DELETE FROM users WHERE id=:id AND role != "admin"');
        $stmt->execute(['id' => (int) $_POST['delete_user_id']]);
        flash('success', 'User deleted successfully.');
    }
    header('Location: /admin/dashboard.php');
    exit;
}

$stats = [
    'users' => (int) $db->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'jobs' => (int) $db->query('SELECT COUNT(*) FROM jobs')->fetchColumn(),
    'applications' => (int) $db->query('SELECT COUNT(*) FROM applications')->fetchColumn(),
];
$pendingJobs = $db->query("SELECT jobs.id, jobs.title, employers.company_name FROM jobs JOIN employers ON jobs.employer_id = employers.id WHERE jobs.status='pending' ORDER BY jobs.created_at DESC")->fetchAll();
$users = $db->query('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 20')->fetchAll();
$messages = $db->query('SELECT name, email, message, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 10')->fetchAll();
$title='Admin Dashboard';
include __DIR__ . '/../includes/header.php';
?>
<h1 class="h3 mb-3">Admin Dashboard</h1>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card border-0 shadow-sm p-3"><h2 class="h6">Total Users</h2><p class="display-6 mb-0"><?= $stats['users'] ?></p></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm p-3"><h2 class="h6">Total Jobs</h2><p class="display-6 mb-0"><?= $stats['jobs'] ?></p></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm p-3"><h2 class="h6">Applications</h2><p class="display-6 mb-0"><?= $stats['applications'] ?></p></div></div>
</div>
<div class="card border-0 shadow-sm p-3 mb-4">
    <h2 class="h5">Pending Jobs</h2>
    <?php foreach($pendingJobs as $job): ?>
        <form method="post" class="d-flex justify-content-between border-bottom py-2">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <span><?= e($job['title']) ?> - <?= e($job['company_name']) ?></span>
            <button name="approve_job_id" value="<?= (int)$job['id'] ?>" class="btn btn-sm btn-success">Approve</button>
        </form>
    <?php endforeach; ?>
</div>
<div class="card border-0 shadow-sm p-3 mb-4">
    <h2 class="h5">Recent Users</h2>
    <?php foreach($users as $row): ?>
        <form method="post" class="d-flex justify-content-between border-bottom py-2 align-items-center">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <span><?= e($row['name']) ?> (<?= e($row['role']) ?>)</span>
            <?php if($row['role'] !== 'admin'): ?><button name="delete_user_id" value="<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-danger">Delete</button><?php endif; ?>
        </form>
    <?php endforeach; ?>
</div>
<div class="card border-0 shadow-sm p-3">
    <h2 class="h5">Contact Messages</h2>
    <?php foreach($messages as $m): ?><div class="border-bottom py-2"><strong><?= e($m['name']) ?></strong> (<?= e($m['email']) ?>)<br><?= e($m['message']) ?></div><?php endforeach; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
