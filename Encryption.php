<?php

class Encryption 
{
    private string $cipher = 'aes-256-cbc';

    public function encrypt(string $data, string $key): string 
    {
        $ivLength = openssl_cipher_iv_length($this->cipher);
        $iv = random_bytes($ivLength);
        $encrypted = openssl_encrypt($data, $this->cipher, $key, 0, $iv);
        
        return base64_encode($iv . $encrypted);
    }

    public function decrypt(string $encodedPayload, string $key): ?string 
    {
        $data = base64_decode($encodedPayload);
        $ivLength = openssl_cipher_iv_length($this->cipher);
        
        if (strlen($data) <= $ivLength) {
            return null;
        }

        $iv = substr($data, 0, $ivLength);
        $ciphertext = substr($data, $ivLength);

        $decrypted = openssl_decrypt($ciphertext, $this->cipher, $key, 0, $iv);
        return $decrypted === false ? null : $decrypted;
    }
}