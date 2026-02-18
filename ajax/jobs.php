<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
$db = Database::getConnection();

$sql = "SELECT jobs.id, jobs.title, jobs.location, jobs.salary, jobs.job_type, employers.company_name, categories.name AS category_name
        FROM jobs
        JOIN employers ON jobs.employer_id = employers.id
        LEFT JOIN categories ON jobs.category_id = categories.id
        WHERE jobs.status = 'approved'";
$params = [];

if (!empty($_GET['keyword'])) {
    $sql .= ' AND (jobs.title LIKE :keyword OR jobs.description LIKE :keyword)';
    $params['keyword'] = '%' . trim((string) $_GET['keyword']) . '%';
}
if (!empty($_GET['location'])) {
    $sql .= ' AND jobs.location LIKE :location';
    $params['location'] = '%' . trim((string) $_GET['location']) . '%';
}
if (!empty($_GET['job_type'])) {
    $sql .= ' AND jobs.job_type = :job_type';
    $params['job_type'] = trim((string) $_GET['job_type']);
}
if (!empty($_GET['salary_min'])) {
    $sql .= ' AND jobs.salary >= :salary_min';
    $params['salary_min'] = (int) $_GET['salary_min'];
}
if (!empty($_GET['salary_max'])) {
    $sql .= ' AND jobs.salary <= :salary_max';
    $params['salary_max'] = (int) $_GET['salary_max'];
}

$sql .= ' ORDER BY jobs.created_at DESC LIMIT 20';
$stmt = $db->prepare($sql);
$stmt->execute($params);
$jobs = $stmt->fetchAll();
?>
<div class="row g-3">
    <?php if ($jobs === []): ?>
        <div class="col-12"><div class="alert alert-info">No jobs found for selected filters.</div></div>
    <?php endif; ?>
    <?php foreach ($jobs as $job): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card job-card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <span class="badge badge-soft mb-2"><?= e($job['category_name'] ?? 'General') ?></span>
                    <h2 class="h5"><?= e($job['title']) ?></h2>
                    <p class="mb-1"><i class="bi bi-building"></i> <?= e($job['company_name']) ?></p>
                    <p class="mb-1"><i class="bi bi-geo-alt"></i> <?= e($job['location']) ?></p>
                    <p class="mb-3"><i class="bi bi-cash-coin"></i> $<?= e((string) $job['salary']) ?></p>
                    <a class="btn btn-outline-primary btn-sm" href="/job-details.php?id=<?= (int) $job['id'] ?>">View Details</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
