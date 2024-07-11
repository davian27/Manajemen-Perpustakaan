<?php
include '../config/database.php';

// Cek apakah form telah disubmit
if (isset($_POST['submit'])) {
    // Ambil data dari form
    $judul = $_POST['judul'];
    $pengarang = $_POST['pengarang'];
    $penerbit = $_POST['penerbit'];
    $tahun_terbit = $_POST['tahun_terbit'];

    // Query untuk menambahkan data buku
    $sql = "INSERT INTO Buku (judul, pengarang, penerbit, tahun_terbit) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$judul, $pengarang, $penerbit, $tahun_terbit]);

    // Pesan sukses
    $message = "Buku berhasil ditambahkan.";
}
?>

<?php include '../includes/header.php'; ?>

<h1>Tambah Buku Baru</h1>

<?php

if (!empty($message)) {
    echo "<p style='color: green;'>$message</p>";
}
?>

<form action="create_buku.php" method="post">
    <label>Judul:</label><br>
    <input type="text" name="judul" required><br>
    <label>Pengarang:</label><br>
    <input type="text" name="pengarang" required><br>
    <label>Penerbit:</label><br>
    <input type="text" name="penerbit" required><br>
    <label>Tahun Terbit:</label><br>
    <input type="number" name="tahun_terbit" required><br><br>
    <input type="submit" name="submit" value="Simpan">
</form>

<?php include '../includes/footer.php'; ?>
