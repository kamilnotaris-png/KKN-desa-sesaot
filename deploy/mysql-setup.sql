-- Setup database & user MySQL untuk KKN Sesaot, terisolasi dari
-- database `fhunizar` dan `legalverse` yang sudah ada di VPS yang sama.
--
-- Jalankan sebagai root MySQL:
--   mysql -u root -p < mysql-setup.sql
-- lalu GANTI password di bawah ini dan samakan dengan .env di server
-- (jangan retype manual di dua tempat berbeda - lihat catatan di
-- CLAUDE.md website-FH soal risiko salah ketik password).

CREATE DATABASE IF NOT EXISTS kkn_sesaot
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- MySQL memperlakukan localhost (socket) dan 127.0.0.1 (TCP) sebagai host
-- berbeda - buat user untuk keduanya seperti pola fhunizar/legalverse.
CREATE USER IF NOT EXISTS 'kkn_sesaot'@'localhost' IDENTIFIED BY 'GANTI_PASSWORD_INI';
CREATE USER IF NOT EXISTS 'kkn_sesaot'@'127.0.0.1' IDENTIFIED BY 'GANTI_PASSWORD_INI';

GRANT ALL PRIVILEGES ON kkn_sesaot.* TO 'kkn_sesaot'@'localhost';
GRANT ALL PRIVILEGES ON kkn_sesaot.* TO 'kkn_sesaot'@'127.0.0.1';

FLUSH PRIVILEGES;
