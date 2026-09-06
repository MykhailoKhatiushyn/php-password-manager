<?php

require_once 'Database.php';
require_once 'Encryption.php';

class PasswordVault 
{
    private PDO $db;
    private Encryption $crypto;

    public function __construct() 
    {
        $database = new Database();
        $this->db = $database->connect();
        $this->crypto = new Encryption();
    }

    public function addPassword(int $userId, string $websiteName, string $plainVaultPassword): bool 
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['master_key'])) {
            return false;
        }

        // Encrypt the password payload using the user's master key from session
        $encryptedPassword = $this->crypto->encrypt($plainVaultPassword, $_SESSION['master_key']);

        $stmt = $this->db->prepare(
            "INSERT INTO vault_passwords (user_id, website_name, encrypted_password, created_at) 
             VALUES (:user_id, :website_name, :encrypted_password, NOW())"
        );

        return $stmt->execute([
            ':user_id' => $userId,
            ':website_name' => $websiteName,
            ':encrypted_password' => $encryptedPassword
        ]);
    }

    public function getPasswords(int $userId): array 
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['master_key'])) {
            return [];
        }

        $stmt = $this->db->prepare(
            "SELECT id, website_name, encrypted_password, created_at 
             FROM vault_passwords 
             WHERE user_id = :user_id 
             ORDER BY created_at DESC"
        );
        $stmt->execute([':user_id' => $userId]);
        $records = $stmt->fetchAll();

        // Decrypt each password payload for user display
        foreach ($records as &$record) {
            $decrypted = $this->crypto->decrypt($record['encrypted_password'], $_SESSION['master_key']);
            $record['decrypted_password'] = $decrypted ?? '[Decryption Failed]';
        }

        return $records;
    }

    public function deletePassword(int $vaultId, int $userId): bool 
    {
        $stmt = $this->db->prepare(
            "DELETE FROM vault_passwords WHERE id = :id AND user_id = :user_id"
        );
        return $stmt->execute([
            ':id' => $vaultId,
            ':user_id' => $userId
        ]);
    }
}