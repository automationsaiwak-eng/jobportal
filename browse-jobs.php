<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
$title = 'Browse Jobs';
include __DIR__ . '/includes/header.php';
?>
<section class="mb-4">
    <h1 class="h3 mb-3">Browse Jobs</h1>
    <form id="jobFilterForm" class="row g-2">
        <div class="col-md-3"><input type="text" name="keyword" class="form-control" placeholder="Keyword"></div>
        <div class="col-md-2"><input type="text" name="location" class="form-control" placeholder="Location"></div>
        <div class="col-md-2"><select name="job_type" class="form-select"><option value="">Type</option><option>Full-time</option><option>Part-time</option><option>Remote</option></select></div>
        <div class="col-md-2"><input type="number" name="salary_min" class="form-control" placeholder="Min Salary"></div>
        <div class="col-md-2"><input type="number" name="salary_max" class="form-control" placeholder="Max Salary"></div>
        <div class="col-md-1 d-grid"><button class="btn btn-primary">Filter</button></div>
    </form>
</section>
<div id="jobsContainer"></div>
<script>
window.addEventListener('DOMContentLoaded', () => document.querySelector('#jobFilterForm').dispatchEvent(new Event('submit', {cancelable: true})));
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
