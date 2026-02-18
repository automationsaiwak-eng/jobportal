<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
$db = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = (string) ($_POST['password'] ?? '');
    $role = in_array($_POST['role'] ?? '', ['job_seeker', 'employer'], true) ? $_POST['role'] : 'job_seeker';

    if ($name && $email && strlen($password) >= 8) {
        $db->beginTransaction();
        try {
            $stmt = $db->prepare('INSERT INTO users (name, email, password, role, created_at) VALUES (:name, :email, :password, :role, NOW())');
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role,
            ]);
            $userId = (int) $db->lastInsertId();
            if ($role === 'job_seeker') {
                $db->prepare('INSERT INTO job_seekers (user_id) VALUES (:user_id)')->execute(['user_id' => $userId]);
            } else {
                $db->prepare('INSERT INTO employers (user_id, company_name) VALUES (:user_id, :company_name)')->execute(['user_id' => $userId, 'company_name' => $name]);
            }
            $db->commit();
            flash('success', 'Registration complete. Please login.');
            header('Location: /login.php');
            exit;
        } catch (Throwable $e) {
            $db->rollBack();
            flash('danger', 'Registration failed. Email may already exist.');
        }
    } else {
        flash('danger', 'Please provide valid details.');
    }
}
$title='Register';
include __DIR__ . '/includes/header.php';
?>
<form method="post" class="card border-0 shadow-sm p-4 mx-auto" style="max-width:560px" data-validate novalidate>
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <h1 class="h3 mb-3">Create Account</h1>
    <input class="form-control mb-2" name="name" placeholder="Full name / Company name" required>
    <input class="form-control mb-2" name="email" type="email" placeholder="Email" required>
    <input class="form-control mb-2" name="password" type="password" minlength="8" placeholder="Password" required>
    <select name="role" class="form-select mb-3" required>
        <option value="job_seeker">Job Seeker</option>
        <option value="employer">Employer</option>
    </select>
    <button class="btn btn-primary">Register</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
