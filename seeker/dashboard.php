<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_auth(['job_seeker']);
$db = Database::getConnection();
$user = current_user();
$seekerStmt = $db->prepare('SELECT * FROM job_seekers WHERE user_id = :uid');
$seekerStmt->execute(['uid' => $user['id']]);
$seeker = $seekerStmt->fetch();
if (!$seeker) {
    exit('Profile not found');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $resumePath = $seeker['resume'] ?? null;
    if (!empty($_FILES['resume']['name']) && is_uploaded_file($_FILES['resume']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['pdf', 'doc', 'docx'], true) && $_FILES['resume']['size'] <= 2 * 1024 * 1024) {
            $resumePath = 'uploads/resume_' . $user['id'] . '_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['resume']['tmp_name'], __DIR__ . '/../' . $resumePath);
        }
    }

    $update = $db->prepare('UPDATE job_seekers SET phone=:phone, address=:address, skills=:skills, experience=:experience, resume=:resume WHERE id=:id');
    $update->execute([
        'phone' => trim((string) $_POST['phone']),
        'address' => trim((string) $_POST['address']),
        'skills' => trim((string) $_POST['skills']),
        'experience' => trim((string) $_POST['experience']),
        'resume' => $resumePath,
        'id' => $seeker['id'],
    ]);
    flash('success', 'Profile updated successfully.');
    header('Location: /seeker/dashboard.php');
    exit;
}

$appStmt = $db->prepare('SELECT applications.status, applications.applied_at, jobs.title FROM applications JOIN jobs ON applications.job_id = jobs.id WHERE applications.job_seeker_id = :sid ORDER BY applied_at DESC');
$appStmt->execute(['sid' => $seeker['id']]);
$applications = $appStmt->fetchAll();
$title = 'Job Seeker Dashboard';
include __DIR__ . '/../includes/header.php';
?>
<h1 class="h3 mb-3">Job Seeker Dashboard</h1>
<div class="card border-0 shadow-sm p-3 mb-4">
    <h2 class="h5">My Profile</h2>
    <form method="post" enctype="multipart/form-data" class="row g-2" data-validate novalidate>
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <div class="col-md-6"><input name="phone" class="form-control" value="<?= e($seeker['phone'] ?? '') ?>" placeholder="Phone"></div>
        <div class="col-md-6"><input name="address" class="form-control" value="<?= e($seeker['address'] ?? '') ?>" placeholder="Address"></div>
        <div class="col-md-6"><input name="skills" class="form-control" value="<?= e($seeker['skills'] ?? '') ?>" placeholder="Skills"></div>
        <div class="col-md-6"><input name="experience" class="form-control" value="<?= e($seeker['experience'] ?? '') ?>" placeholder="Experience"></div>
        <div class="col-12"><input type="file" name="resume" class="form-control" accept=".pdf,.doc,.docx"></div>
        <div class="col-12"><button class="btn btn-primary">Update Profile</button></div>
    </form>
</div>
<div class="card border-0 shadow-sm p-3">
    <h2 class="h5">My Applications</h2>
    <div class="table-responsive"><table class="table"><thead><tr><th>Job</th><th>Status</th><th>Date</th></tr></thead><tbody>
    <?php foreach($applications as $app): ?><tr><td><?= e($app['title']) ?></td><td><?= e($app['status']) ?></td><td><?= e($app['applied_at']) ?></td></tr><?php endforeach; ?>
    </tbody></table></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
