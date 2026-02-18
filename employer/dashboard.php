<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_auth(['employer']);
$db = Database::getConnection();
$user = current_user();
$empStmt = $db->prepare('SELECT * FROM employers WHERE user_id = :uid');
$empStmt->execute(['uid' => $user['id']]);
$employer = $empStmt->fetch();
if (!$employer) {
    exit('Employer profile not found');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $stmt = $db->prepare('INSERT INTO jobs (employer_id, title, description, location, salary, category_id, job_type, deadline, status, created_at)
        VALUES (:employer_id, :title, :description, :location, :salary, :category_id, :job_type, :deadline, :status, NOW())');
    $stmt->execute([
        'employer_id' => $employer['id'],
        'title' => trim((string) $_POST['title']),
        'description' => trim((string) $_POST['description']),
        'location' => trim((string) $_POST['location']),
        'salary' => (int) $_POST['salary'],
        'category_id' => (int) $_POST['category_id'],
        'job_type' => trim((string) $_POST['job_type']),
        'deadline' => $_POST['deadline'],
        'status' => 'pending',
    ]);
    flash('success', 'Job posted and awaiting admin approval.');
    header('Location: /employer/dashboard.php');
    exit;
}

$jobsStmt = $db->prepare('SELECT * FROM jobs WHERE employer_id = :id ORDER BY created_at DESC');
$jobsStmt->execute(['id' => $employer['id']]);
$jobs = $jobsStmt->fetchAll();
$categories = $db->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$title = 'Employer Dashboard';
include __DIR__ . '/../includes/header.php';
?>
<h1 class="h3 mb-3">Employer Dashboard</h1>
<div class="card border-0 shadow-sm p-3 mb-4">
    <h2 class="h5">Post New Job</h2>
    <form method="post" class="row g-2" data-validate novalidate>
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <div class="col-md-6"><input class="form-control" name="title" placeholder="Job title" required></div>
        <div class="col-md-3"><input class="form-control" name="location" placeholder="Location" required></div>
        <div class="col-md-3"><input class="form-control" type="number" name="salary" placeholder="Salary" required></div>
        <div class="col-md-3"><select name="category_id" class="form-select" required><?php foreach($categories as $cat): ?><option value="<?= (int)$cat['id'] ?>"><?= e($cat['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><select name="job_type" class="form-select" required><option>Full-time</option><option>Part-time</option><option>Remote</option></select></div>
        <div class="col-md-3"><input type="date" name="deadline" class="form-control" required></div>
        <div class="col-12"><textarea class="form-control" name="description" rows="4" placeholder="Description" required></textarea></div>
        <div class="col-12"><button class="btn btn-primary">Publish Job</button></div>
    </form>
</div>
<div class="card border-0 shadow-sm p-3">
    <h2 class="h5">My Jobs</h2>
    <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Title</th><th>Status</th><th>Deadline</th></tr></thead><tbody>
    <?php foreach($jobs as $job): ?><tr><td><?= e($job['title']) ?></td><td><span class="badge text-bg-secondary"><?= e($job['status']) ?></span></td><td><?= e($job['deadline']) ?></td></tr><?php endforeach; ?>
    </tbody></table></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
