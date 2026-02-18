<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
$db = Database::getConnection();
$title = 'Home';
$stmt = $db->query("SELECT jobs.id, jobs.title, jobs.location, jobs.salary, jobs.job_type, employers.company_name, categories.name AS category_name
                    FROM jobs
                    JOIN employers ON jobs.employer_id = employers.id
                    LEFT JOIN categories ON jobs.category_id = categories.id
                    WHERE jobs.status = 'approved'
                    ORDER BY jobs.created_at DESC
                    LIMIT 6");
$jobs = $stmt->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<section class="hero mb-4">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <h1 class="display-6 fw-bold">Find your dream job today</h1>
            <p class="lead mb-4">Discover top opportunities from verified companies across full-time, part-time, and remote roles.</p>
            <a href="/browse-jobs.php" class="btn btn-primary btn-lg">Browse Jobs</a>
        </div>
        <div class="col-lg-5">
            <form id="jobFilterForm" class="card p-3 border-0 shadow-sm bg-white">
                <h2 class="h5">Quick Search</h2>
                <input type="text" name="keyword" class="form-control mb-2" placeholder="Job title or keyword">
                <input type="text" name="location" class="form-control mb-2" placeholder="Location">
                <select name="job_type" class="form-select mb-3">
                    <option value="">Any type</option>
                    <option>Full-time</option>
                    <option>Part-time</option>
                    <option>Remote</option>
                </select>
                <button class="btn btn-primary">Search Jobs</button>
            </form>
        </div>
    </div>
</section>
<section>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Latest Jobs</h2>
        <a href="/browse-jobs.php" class="text-decoration-none">See all</a>
    </div>
    <div id="jobsContainer" class="row g-3">
        <?php foreach ($jobs as $job): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card job-card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <span class="badge badge-soft mb-2"><?= e($job['category_name'] ?? 'General') ?></span>
                        <h3 class="h5"><?= e($job['title']) ?></h3>
                        <p class="mb-1"><i class="bi bi-building"></i> <?= e($job['company_name']) ?></p>
                        <p class="mb-1"><i class="bi bi-geo-alt"></i> <?= e($job['location']) ?></p>
                        <p class="mb-3"><i class="bi bi-cash-coin"></i> $<?= e((string) $job['salary']) ?></p>
                        <a class="btn btn-outline-primary btn-sm" href="/job-details.php?id=<?= (int) $job['id'] ?>">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
