<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'classes/PasswordGenerator.php';
require_once 'classes/PasswordVault.php';

$vault = new PasswordVault();
$generatedPassword = '';

// Handle Password Generation
if (isset($_POST['action']) && $_POST['action'] === 'generate') {
    $gen = new PasswordGenerator(
        (int)$_POST['length'],
        (int)$_POST['uppercase'],
        (int)$_POST['lowercase'],
        (int)$_POST['numbers'],
        (int)$_POST['special']
    );
    $generatedPassword = $gen->generate();
}

// Handle Saving Password Record
if (isset($_POST['action']) && $_POST['action'] === 'save') {
    $vault->addPassword($_SESSION['user_id'], $_POST['website_name'], $_POST['vault_password']);
    header('Location: dashboard.php');
    exit;
}

// Handle Deleting Password Record
if (isset($_GET['delete_id'])) {
    $vault->deletePassword((int)$_GET['delete_id'], $_SESSION['user_id']);
    header('Location: dashboard.php');
    exit;
}

$passwords = $vault->getPasswords($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Password Manager</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        .card { border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; max-width: 650px; border-radius: 5px; }
        table { border-collapse: collapse; width: 100%; max-width: 800px; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .gen-output { background: #e9ecef; padding: 10px; font-family: monospace; font-size: 1.1em; display: inline-block; }
    </style>
</head>
<body>
    <h1>User Dashboard</h1>
    <p>Logged in as: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> | <a href="change_password.php">Change Password</a> | <a href="logout.php">Logout</a></p>

    <!-- Password Generator GUI -->
    <div class="card">
        <h3>1. Custom Password Generator</h3>
        <form method="POST">
            <input type="hidden" name="action" value="generate">
            <label>Length: <input type="number" name="length" value="<?= $_POST['length'] ?? 12 ?>" min="4" style="width: 60px;"></label> | 
            <label>Uppercase: <input type="number" name="uppercase" value="<?= $_POST['uppercase'] ?? 3 ?>" style="width: 50px;"></label> | 
            <label>Lowercase: <input type="number" name="lowercase" value="<?= $_POST['lowercase'] ?? 3 ?>" style="width: 50px;"></label><br><br>
            <label>Numbers: <input type="number" name="numbers" value="<?= $_POST['numbers'] ?? 3 ?>" style="width: 50px;"></label> | 
            <label>Special: <input type="number" name="special" value="<?= $_POST['special'] ?? 3 ?>" style="width: 50px;"></label><br><br>
            <button type="submit">Generate Password</button>
        </form>

        <?php if ($generatedPassword): ?>
            <p>Generated Result: <span class="gen-output"><?= htmlspecialchars($generatedPassword) ?></span></p>
        <?php endif; ?>
    </div>

    <!-- Save Vault Record GUI -->
    <div class="card">
        <h3>2. Save Credential Record</h3>
        <form method="POST">
            <input type="hidden" name="action" value="save">
            <label>Website / Program Name:</label><br>
            <input type="text" name="website_name" placeholder="e.g. FB, GMAIL" required style="width: 300px; padding: 5px;"><br><br>
            <label>Password to Save:</label><br>
            <input type="text" name="vault_password" value="<?= htmlspecialchars($generatedPassword) ?>" placeholder="Enter or paste generated password" required style="width: 300px; padding: 5px;"><br><br>
            <button type="submit">Save to Vault</button>
        </form>
    </div>

    <!-- Saved Passwords Display Table -->
    <h3>3. Saved Passwords Vault</h3>
    <table>
        <thead>
            <tr>
                <th>Website / Program</th>
                <th>Decrypted Password</th>
                <th>Date & Time Saved</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($passwords as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['website_name']) ?></td>
                    <td><code><?= htmlspecialchars($p['decrypted_password']) ?></code></td>
                    <td><?= htmlspecialchars($p['created_at']) ?></td>
                    <td><a href="dashboard.php?delete_id=<?= $p['id'] ?>" onclick="return confirm('Delete this record?')">Delete</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($passwords)): ?>
                <tr><td colspan="4">No saved credentials found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>