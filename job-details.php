<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
$db = Database::getConnection();
$id = (int) ($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT jobs.*, employers.company_name FROM jobs JOIN employers ON jobs.employer_id = employers.id WHERE jobs.id = :id");
$stmt->execute(['id' => $id]);
$job = $stmt->fetch();
if (!$job) {
    http_response_code(404);
    exit('Job not found');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_auth(['job_seeker']);
    verify_csrf();

    $seekerStmt = $db->prepare('SELECT id FROM job_seekers WHERE user_id = :user_id');
    $seekerStmt->execute(['user_id' => current_user()['id']]);
    $seeker = $seekerStmt->fetch();

    if ($seeker) {
        $applyStmt = $db->prepare('INSERT INTO applications (job_id, job_seeker_id, cover_letter, status, applied_at) VALUES (:job_id, :job_seeker_id, :cover_letter, :status, NOW())');
        $applyStmt->execute([
            'job_id' => $id,
            'job_seeker_id' => $seeker['id'],
            'cover_letter' => trim((string) ($_POST['cover_letter'] ?? '')),
            'status' => 'pending',
        ]);
        flash('success', 'Application submitted successfully.');
    }
    header('Location: /job-details.php?id=' . $id);
    exit;
}
$title = $job['title'];
include __DIR__ . '/includes/header.php';
?>
<article class="card border-0 shadow-sm p-4">
    <h1 class="h3"><?= e($job['title']) ?></h1>
    <p class="text-muted">By <?= e($job['company_name']) ?> · <?= e($job['location']) ?> · <?= e($job['job_type']) ?></p>
    <p><?= nl2br(e($job['description'])) ?></p>
    <p><strong>Salary:</strong> $<?= e((string) $job['salary']) ?></p>
    <p><strong>Deadline:</strong> <?= e($job['deadline']) ?></p>
</article>
<?php if (current_user() && current_user()['role'] === 'job_seeker'): ?>
<form method="post" class="card border-0 shadow-sm p-3 mt-3" data-validate novalidate>
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <h2 class="h5">Apply for this job</h2>
    <textarea class="form-control mb-2" name="cover_letter" required minlength="30" placeholder="Tell employer why you are a great fit"></textarea>
    <button class="btn btn-primary">Submit Application</button>
</form>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
