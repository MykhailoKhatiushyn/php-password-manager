# PHP OOP Password Manager

An object-oriented PHP application designed for generating custom passwords and managing an encrypted password vault.

## Status
- **Version 1:** OOP `PasswordGenerator` class implementation
- **Version 2:** OOP `Encryption` class (AES-256-CBC) implementation
- **Version 3:** OOP `Database` connection class (PDO) implementation
- **Version 4:** OOP `User` class for authentication and AES master key management
- **Version 5:** OOP `PasswordVault` class for encrypted vault item storage and retrieval
- **Version 6:** Web GUI interfaces (Registration, Login, Dashboard, Logout)

## Features
- **Customizable Password Generation:** Set parameters for length, uppercase, lowercase, numeric, and special characters.
- **AES-256 Encryption:** OpenSSL encryption handler for master keys and vault records.
- **PDO Database Abstraction:** Secure MySQL connection handling using prepared statements.
- **Master Key Security:** Unique AES key generated per user, encrypted using the user's plain password, and decrypted into session on login.
- **Encrypted Vault Storage:** Automatic timestamping and AES encryption of saved website/app credentials.
- **Web Interface:** Interactive dashboard with parameter controls, live password creation, and vault listing.