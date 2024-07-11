<?php include '../includes/header.php'; ?>
<?php include '../config/database.php'; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Anggota</title>
    <script>
        function showAlert(message) {
            alert(message);
        }
    </script>
</head>

<header>
    <div class="navbar">
        <div class="logo">
            <a href="../index.php">Perpustakaan</a>
        </div>
        <ul class="links">
            <li><a href="../index.php">Home</a></li>
            <li><a href="./buku.php">Buku</a></li>
            <li><a href="#">Anggota</a></li>
            <!-- <li><a href="./pages/kategori.php">Kategori</a></li>
            <li><a href="./pages/genre.php">Genre</a></li> -->
            <li><a href="./peminjaman.php">Peminjaman</a></li>
            <li><a href="./history_peminjaman.php">History</a></li>
        </ul>
    </div>
</header>

<div class="container">
    <h1>Manajemen Anggota</h1>

    <?php
    if (isset($_POST['submit'])) {
        $id_anggota = $_POST['id_anggota'];
        $nama = trim($_POST['nama']);
        $alamat = trim($_POST['alamat']);
        $telepon = trim($_POST['telepon']);
        $email = trim($_POST['email']);
        $errors = [];

        // Validasi nama
        if (empty($nama)) {
            $errors[] = "Nama tidak boleh kosong.";
        }

        // Validasi alamat
        if (empty($alamat)) {
            $errors[] = "Alamat tidak boleh kosong.";
        }

        // Validasi telepon (opsional, jika diperlukan)
        if (!empty($telepon) && !preg_match('/^[0-9]{10,15}$/', $telepon)) {
            $errors[] = "Nomor telepon harus berupa angka dan memiliki panjang 10-15 karakter.";
        }

        // Validasi email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format email tidak valid.";
        }

        // Cek duplikat email
        $sql = "SELECT COUNT(*) FROM Anggota WHERE email = ? AND id_anggota != ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email, $id_anggota]);
        $emailExists = $stmt->fetchColumn();

        if ($emailExists) {
            $errors[] = "Email sudah digunakan. Silakan gunakan email lain.";
        }

        // Jika tidak ada error, proses data
        if (empty($errors)) {
            if ($id_anggota) {
                $sql = "UPDATE Anggota SET nama = ?, alamat = ?, telepon = ?, email = ? WHERE id_anggota = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nama, $alamat, $telepon, $email, $id_anggota]);
                $message = "Anggota berhasil diperbarui.";
            } else {
                $sql = "INSERT INTO Anggota (nama, alamat, telepon, email) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nama, $alamat, $telepon, $email]);
                $message = "Anggota berhasil ditambahkan.";
            }
        } else {
            $message = implode('\n', $errors);
        }
    }

    if (isset($_GET['delete_id'])) {
        $id_anggota = $_GET['delete_id'];

        try {
            $sql = "DELETE FROM Anggota WHERE id_anggota = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_anggota]);
            $message = "Anggota berhasil dihapus.";
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                echo "<script>alert('Data tidak bisa dihapus karena masih terhubung dengan data lain.');</script>";
            } else {
                echo "<script>alert('Terjadi kesalahan: {$e->getMessage()}');</script>";
            }
        }
    }

    if (isset($_GET['edit_id'])) {
        $id_anggota = $_GET['edit_id'];
        $sql = "SELECT * FROM Anggota WHERE id_anggota = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_anggota]);
        $anggota = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $anggota = ['id_anggota' => '', 'nama' => '', 'alamat' => '', 'telepon' => '', 'email' => ''];
    }
    ?>

    <?php if (!empty($message)) {
        echo "<script>showAlert('$message');</script>";
    } ?>

    <form action="anggota.php" method="post">
        <input type="hidden" name="id_anggota" value="<?php echo $anggota['id_anggota']; ?>">
        <label>Nama:</label><br>
        <input type="text" name="nama" value="<?php echo htmlspecialchars($anggota['nama']); ?>"><br>
        <label>Alamat:</label><br>
        <textarea name="alamat"><?php echo htmlspecialchars($anggota['alamat']); ?></textarea><br>
        <label>Telepon:</label><br>
        <input type="text" name="telepon" value="<?php echo htmlspecialchars($anggota['telepon']); ?>"><br>
        <label>Email:</label><br>
        <input type="email" name="email" value="<?php echo htmlspecialchars($anggota['email']); ?>"><br><br>
        <input type="submit" name="submit" value="Simpan">
    </form>

    <h2 class="mt-5">Daftar Anggota</h2>
    <table class="table table-striped" border="1">
        <thead>
            <tr>
                <th>ID Anggota</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Telepon</th>
                <th>Email</th>
                <th>Buku yang Dipinjam</th>
                <th>Aksi</th>
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
                echo "<td>{$anggota['alamat']}</td>";
                echo "<td>{$anggota['telepon']}</td>";
                echo "<td>{$anggota['email']}</td>";
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
                echo "<td>";
                echo "<a href='anggota.php?edit_id={$anggota['id_anggota']}' class='btn btn-warning btn-sm'>Edit</a> ";
                echo "<a href='anggota.php?delete_id={$anggota['id_anggota']}' class='btn btn-danger btn-sm'>Hapus</a> ";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>