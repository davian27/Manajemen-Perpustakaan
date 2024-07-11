<?php
// Sertakan file konfigurasi database
include '../config/database.php';

// Cek apakah id_buku telah diterima melalui URL
if (isset($_GET['id_buku'])) {
    $id_buku = $_GET['id_buku'];

    // Query untuk menghapus data buku berdasarkan id_buku
    $sql = "DELETE FROM Buku WHERE id_buku = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_buku]);

    // Redirect ke halaman utama setelah penghapusan
    header("Location: ../buku.php");
    exit;
} else {
    echo "ID Buku tidak ditemukan.";
    exit;
}
?>
