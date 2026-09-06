<?php

require_once 'Database.php';
require_once 'Encryption.php';

class User 
{
    private PDO $db;
    private Encryption $crypto;

    public function __construct() 
    {
        $database = new Database();
        $this->db = $database->connect();
        $this->crypto = new Encryption();
    }

    public function register(string $username, string $plainPassword): bool 
    {
        // Hash login password for secure authentication storage
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        // Generate unchangeable 256-bit master KEY for vault encryption
        $masterKey = bin2hex(random_bytes(16));

        // Encrypt the master KEY using the user's plain login password
        $encryptedMasterKey = $this->crypto->encrypt($masterKey, $plainPassword);

        $stmt = $this->db->prepare(
            "INSERT INTO users (username, password, encrypted_key) VALUES (:username, :password, :encrypted_key)"
        );

        return $stmt->execute([
            ':username' => $username,
            ':password' => $hashedPassword,
            ':encrypted_key' => $encryptedMasterKey
        ]);
    }

    public function login(string $username, string $plainPassword): bool 
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($plainPassword, $user['password'])) {
            // Decrypt the persistent master KEY using the provided plain login password
            $decryptedMasterKey = $this->crypto->decrypt($user['encrypted_key'], $plainPassword);

            if ($decryptedMasterKey === null) {
                return false;
            }

            // Start session and store user metadata + decrypted master KEY
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['master_key'] = $decryptedMasterKey;

            return true;
        }

        return false;
    }

    public function logout(): void 
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
    }
    public function changePassword(int $userId, string $oldPlainPassword, string $newPlainPassword): bool 
{
    $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($oldPlainPassword, $user['password'])) {
        return false;
    }

    // 1. Decrypt the persistent master KEY using the OLD password
    $masterKey = $this->crypto->decrypt($user['encrypted_key'], $oldPlainPassword);
    if ($masterKey === null) {
        return false;
    }

    // 2. Re-encrypt the EXACT SAME master KEY using the NEW password
    $reEncryptedMasterKey = $this->crypto->encrypt($masterKey, $newPlainPassword);

    // 3. Hash the new login password
    $newHashedPassword = password_hash($newPlainPassword, PASSWORD_DEFAULT);

    // 4. Update the user record in MySQL
    $updateStmt = $this->db->prepare(
        "UPDATE users SET password = :password, encrypted_key = :encrypted_key WHERE id = :id"
    );

    $success = $updateStmt->execute([
        ':password' => $newHashedPassword,
        ':encrypted_key' => $reEncryptedMasterKey,
        ':id' => $userId
    ]);

    if ($success) {
        // Keep active session updated with master key
        $_SESSION['master_key'] = $masterKey;
    }

    return $success;
}
}