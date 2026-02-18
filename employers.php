<?php
require_once __DIR__ . '/includes/auth.php';
$db = Database::getConnection();
$title='Employer Listing';
$employers = $db->query('SELECT company_name, website, description FROM employers ORDER BY company_name ASC LIMIT 100')->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<h1 class="h3 mb-3">Employers</h1>
<div class="row g-3">
<?php foreach($employers as $employer): ?>
<div class="col-md-6"><div class="card border-0 shadow-sm h-100"><div class="card-body">
<h2 class="h5"><?= e($employer['company_name']) ?></h2>
<p><?= e($employer['description'] ?? 'No description yet.') ?></p>
<?php if($employer['website']): ?><a href="<?= e($employer['website']) ?>" target="_blank">Visit website</a><?php endif; ?>
</div></div></div>
<?php endforeach; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
