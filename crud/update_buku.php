<?php
// Sertakan file konfigurasi database
include '../config/database.php';

// Cek apakah id_buku telah diterima melalui URL
if (isset($_GET['id_buku'])) {
    $id_buku = $_GET['id_buku'];

    // Ambil data buku berdasarkan id_buku
    $sql = "SELECT * FROM Buku WHERE id_buku = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_buku]);
    $buku = $stmt->fetch(PDO::FETCH_ASSOC);

    // Cek apakah buku ditemukan
    if ($buku) {
        // Jika form disubmit, perbarui data buku
        if (isset($_POST['submit'])) {
            $judul = $_POST['judul'];
            $pengarang = $_POST['pengarang'];
            $penerbit = $_POST['penerbit'];
            $tahun_terbit = $_POST['tahun_terbit'];

            $sql = "UPDATE Buku SET judul = ?, pengarang = ?, penerbit = ?, tahun_terbit = ? WHERE id_buku = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$judul, $pengarang, $penerbit, $tahun_terbit, $id_buku]);

            $message = "Buku berhasil diperbarui.";
        }
    } else {
        echo "Buku tidak ditemukan.";
        exit;
    }
} else {
    echo "ID Buku tidak ditemukan.";
    exit;
}
?>

<?php include '../includes/header.php'; ?>
<link rel="stylesheet" href="../assets/css/form.css">
<link rel="stylesheet" href="../assets/css/style.css">
<header>
        <div class="navbar">
            <div class="logo">
                <a href="#">Perpustakaan</a>
            </div>
            <ul class="links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="./pages/buku.php">Buku</a></li>
                <li><a href="./pages/anggota.php">Anggota</a></li>
                <li><a href="./pages/kategori.php">Kategori</a></li>
                <li><a href="./pages/peminjaman.php">Peminjaman</a></li>
            </ul>
        </div>
    </header>

<h1>Perbarui Buku</h1>

<div class="container">
<?php
// Tampilkan pesan sukses jika ada
if (!empty($message)) {
    echo "<p style='color: green;'>$message</p>";
}
?>
    <form action="update_buku.php?id_buku=<?php echo $id_buku; ?>" method="post">
        <label>Judul:</label><br>
        <input type="text" name="judul" value="<?php echo $buku['judul']; ?>" required><br>
        <label>Pengarang:</label><br>
        <input type="text" name="pengarang" value="<?php echo $buku['pengarang']; ?>" required><br>
        <label>Penerbit:</label><br>
        <input type="text" name="penerbit" value="<?php echo $buku['penerbit']; ?>" required><br>
        <label>Tahun Terbit:</label><br>
        <input type="number" name="tahun_terbit" value="<?php echo $buku['tahun_terbit']; ?>" required><br><br>
        <input type="submit" name="submit" value="Perbarui">
    </form>
    <a href="../index.php"><input type="submit" name="submit" value="Kembali"></a>
</div>

<?php include '../includes/footer.php'; ?>
