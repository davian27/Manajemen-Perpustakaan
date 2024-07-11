<?php include '../config/database.php'; ?>
<?php include '../includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>History Peminjaman</title>
</head>
<body>
<header>
    <div class="navbar">
        <div class="logo">
            <a href="../index.php">Perpustakaan</a>
        </div>
        <ul class="links">
            <li><a href="../index.php">Home</a></li>
            <li><a href="./buku.php">Buku</a></li>
            <li><a href="./anggota.php">Anggota</a></li>
            <!-- <li><a href="./pages/kategori.php">Kategori</a></li>
            <li><a href="./pages/genre.php">Genre</a></li> -->
            <li><a href="./peminjaman.php">Peminjaman</a></li>
            <li><a href="#">History</a></li>
        </ul>
    </div>
</header>

<div class="container mt-5">
    <h1>History Peminjaman</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>ID Peminjaman</th>
                <th>ID Anggota</th>
                <th>ID Buku</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Tanggal Kembali Sebenarnya</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM history_peminjaman";
            $stmt = $pdo->query($sql);

            while ($history = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>{$history['id_history']}</td>";
                echo "<td>{$history['id_peminjaman']}</td>";
                echo "<td>{$history['id_anggota']}</td>";
                echo "<td>{$history['id_buku']}</td>";
                echo "<td>{$history['tanggal_pinjam']}</td>";
                echo "<td>{$history['tanggal_kembali']}</td>";
                echo "<td>{$history['tanggal_kembali_sebenarnya']}</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
