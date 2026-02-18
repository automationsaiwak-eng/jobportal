<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
$db = Database::getConnection();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = (string) ($_POST['password'] ?? '');
    if ($email) {
        $stmt = $db->prepare('SELECT id, name, email, password, role FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            $_SESSION['user'] = $user;
            $path = $user['role'] === 'admin' ? '/admin/dashboard.php' : ($user['role'] === 'employer' ? '/employer/dashboard.php' : '/seeker/dashboard.php');
            header('Location: ' . $path);
            exit;
        }
    }
    flash('danger', 'Invalid credentials.');
}
$title='Login';
include __DIR__ . '/includes/header.php';
?>
<form method="post" class="card border-0 shadow-sm p-4 mx-auto" style="max-width:480px" data-validate novalidate>
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <h1 class="h3 mb-3">Login</h1>
    <input class="form-control mb-2" name="email" type="email" placeholder="Email" required>
    <input class="form-control mb-3" name="password" type="password" placeholder="Password" required>
    <button class="btn btn-primary">Login</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
