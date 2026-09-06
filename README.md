# PHP OOP Password Manager

An object-oriented PHP application designed for generating custom passwords and managing an encrypted password vault.

## Status
- **Version 1:** OOP `PasswordGenerator` class implementation
- **Version 2:** OOP `Encryption` class (AES-256-CBC) implementation
- **Version 3:** OOP `Database` connection class (PDO) implementation

## Features
- **Customizable Password Generation:** Set parameters for length, uppercase, lowercase, numeric, and special characters.
- **AES-256 Encryption:** OpenSSL encryption handler for master keys and vault records.
- **PDO Database Abstraction:** Secure MySQL connection handling using prepared statements.