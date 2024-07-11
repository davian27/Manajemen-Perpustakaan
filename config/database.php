<?php
$host = 'localhost';
$dbname = 'manajemen_perpus';
$username = 'root';
$password = '';

try {
    // Membuat koneksi ke database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Mengatur mode error PDO ke exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Mengatur karakter set ke UTF-8
    $pdo->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    // Menangkap kesalahan dan menampilkan pesan error
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}
