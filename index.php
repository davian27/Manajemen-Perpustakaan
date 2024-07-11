<?php include './config/database.php'; ?>
<?php include './includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/navbar.css">
    <link rel="stylesheet" href="./assets/css/footer.css">
    
    <title>Perpustakaan</title>
</head>

<body>
    <header>
        <div class="navbar">
            <div class="logo">
                <a href="#">Perpustakaan</a>
            </div>
            <ul class="links">
                <li><a href="#">Home</a></li>
                <li><a href="./pages/buku.php">Buku</a></li>
                <li><a href="./pages/anggota.php">Anggota</a></li>
                <!-- <li><a href="./pages/kategori.php">Kategori</a></li>
                <li><a href="./pages/genre.php">Genre</a></li> -->
                <li><a href="./pages/peminjaman.php">Peminjaman</a></li>
                <li><a href="./pages/history_peminjaman.php">History</a></li>
            </ul>
        </div>
    </header>

    <div class="container mt-5">
        <div class="jumbotron">
            <h1 class="display-4">Selamat Datang di Sistem Manajemen Perpustakaan</h1>
            <p class="lead">Gunakan menu di atas untuk mengelola data perpustakaan. Anda dapat menambahkan buku baru,
                melihat daftar buku, memperbarui informasi buku, dan menghapus buku.</p>
        </div>

        <h2 class="mt-5">List Buku Yang Dipinjam</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID Anggota</th>
                    <th>Nama</th>
                    <th>Buku yang Dipinjam</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM Anggota";
                $stmt = $pdo->query($sql);

                while ($anggota = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>{$anggota['id_anggota']}</td>";
                    echo "<td>{$anggota['nama']}</td>";
                    echo "<td>";

                    // Query untuk mengambil data peminjaman buku oleh anggota
                    $peminjamanSql = "SELECT peminjaman.*, buku.judul AS judul_buku
                                      FROM peminjaman
                                      JOIN buku ON peminjaman.id_buku = buku.id_buku
                                      WHERE peminjaman.id_anggota = ?";
                    $peminjamanStmt = $pdo->prepare($peminjamanSql);
                    $peminjamanStmt->execute([$anggota['id_anggota']]);
                    $peminjamanList = $peminjamanStmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($peminjamanList as $peminjaman) {
                        echo "<p>{$peminjaman['judul_buku']} (Pinjam: {$peminjaman['tanggal_pinjam']}, Kembali: {$peminjaman['tanggal_kembali']})</p>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php include './includes/footer.php'; ?>
</body>

</html>