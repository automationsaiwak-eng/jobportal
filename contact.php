<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
$db = Database::getConnection();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string)($_POST['name'] ?? ''));
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $message = trim((string)($_POST['message'] ?? ''));
    if ($name && $email && strlen($message) > 10) {
        $stmt = $db->prepare('INSERT INTO contact_messages (name, email, message, created_at) VALUES (:name, :email, :message, NOW())');
        $stmt->execute(['name' => $name, 'email' => $email, 'message' => $message]);
        flash('success', 'Message sent successfully.');
        header('Location: /contact.php');
        exit;
    }
    flash('danger', 'Please fill the form correctly.');
}
$title='Contact'; include __DIR__ . '/includes/header.php';
?>
<form method="post" class="card border-0 shadow-sm p-4 mx-auto" style="max-width:680px" data-validate novalidate>
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <h1 class="h3 mb-3">Contact Us</h1>
    <input class="form-control mb-2" name="name" placeholder="Name" required>
    <input class="form-control mb-2" name="email" type="email" placeholder="Email" required>
    <textarea class="form-control mb-3" name="message" rows="5" minlength="10" placeholder="Message" required></textarea>
    <button class="btn btn-primary">Send Message</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
