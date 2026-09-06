<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'classes/User.php';

$message = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];

    if (!empty($oldPassword) && !empty($newPassword)) {
        if ($user->changePassword($_SESSION['user_id'], $oldPassword, $newPassword)) {
            $successMessage = 'Password updated and master KEY successfully recoded!';
        } else {
            $message = 'Incorrect current password or update failed.';
        }
    } else {
        $message = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password - Password Manager</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .form-container { max-width: 400px; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; margin-bottom: 5px; }
        .input-group input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; background: #007bff; color: white; border: none; cursor: pointer; }
        .error { color: red; margin-bottom: 15px; }
        .success { color: green; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Change Login Password</h2>
        <?php if ($message): ?>
            <div class="error"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($successMessage): ?>
            <div class="success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="input-group">
                <label>Current Password</label>
                <input type="password" name="old_password" required>
            </div>
            <div class="input-group">
                <label>New Password</label>
                <input type="password" name="new_password" required>
            </div>
            <button type="submit">Update & Recode Key</button>
        </form>
        <p><a href="dashboard.php">&larr; Back to Dashboard</a></p>
    </div>
</body>
</html>