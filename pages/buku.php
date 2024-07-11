<?php include '../config/database.php'; ?>
<?php include '../includes/header.php'; ?>

<header>
    <div class="navbar">
        <div class="logo">
            <a href="../index.php">Perpustakaan</a>
        </div>
        <ul class="links">
            <li><a href="../index.php">Home</a></li>
            <li><a href="#">Buku</a></li>
            <li><a href="./anggota.php">Anggota</a></li>
            <li><a href="./peminjaman.php">Peminjaman</a></li>
            <li><a href="./history_peminjaman.php">History</a></li>
        </ul>
    </div>
</header>

<div class="container">
    <h1>Manajemen Buku</h1>

    <?php
    $message = '';
    if (isset($_POST['submit'])) {
        $id_buku = $_POST['id_buku'];
        $kode_buku = trim($_POST['kode_buku']);
        $judul = trim($_POST['judul']);
        $pengarang = trim($_POST['pengarang']);
        $penerbit = trim($_POST['penerbit']);
        $tahun_terbit = trim($_POST['tahun_terbit']);
        $rak_buku = trim($_POST['rak_buku']);
        $errors = [];

        // Validasi kode buku
        if (empty($kode_buku)) {
            $errors[] = "Kode Buku tidak boleh kosong.";
        }
        elseif (!ctype_digit($kode_buku) || $kode_buku <= 0) {
            $errors[] = "Kode Buku harus berupa angka positif.";
        }

        // Validasi judul
        if (empty($judul)) {
            $errors[] = "Judul tidak boleh kosong.";
        }

        // Validasi pengarang
        if (empty($pengarang)) {
            $errors[] = "Pengarang tidak boleh kosong.";
        }

        // Validasi penerbit
        if (empty($penerbit)) {
            $errors[] = "Penerbit tidak boleh kosong.";
        }

        // Validasi tahun terbit
        if (!is_numeric($tahun_terbit) || $tahun_terbit <= 0) {
            $errors[] = "Tahun terbit harus berupa angka yang valid.";
        }

        // Validasi rak buku
        if (empty($rak_buku)) {
            $errors[] = "Rak Buku tidak boleh kosong.";
        }

        // Cek apakah judul buku sudah ada di database
        if (empty($errors)) {
            $sql = "SELECT COUNT(*) FROM Buku WHERE judul = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$judul]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $errors[] = "Judul buku sudah ada.";
            }
        }

        // Jika tidak ada error, proses data
        if (empty($errors)) {
            if ($id_buku) {
                $sql = "UPDATE Buku SET kode_buku = ?, judul = ?, pengarang = ?, penerbit = ?, tahun_terbit = ?, rak_buku = ? WHERE id_buku = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$kode_buku, $judul, $pengarang, $penerbit, $tahun_terbit, $rak_buku, $id_buku]);
                $message = "Buku berhasil diperbarui.";
            } else {
                $sql = "INSERT INTO Buku (kode_buku, judul, pengarang, penerbit, tahun_terbit, rak_buku) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$kode_buku, $judul, $pengarang, $penerbit, $tahun_terbit, $rak_buku]);
                $message = "Buku berhasil ditambahkan.";
            }
        } else {
            $message = implode('\n', $errors);
        }
    }

    // Cek apakah ada ID buku untuk dihapus
    if (isset($_GET['delete_id'])) {
        $id_buku = $_GET['delete_id'];
        
        // Cek apakah buku masih terhubung dengan data peminjaman
        $sql = "SELECT COUNT(*) FROM peminjaman WHERE id_buku = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_buku]);
        $count = $stmt->fetchColumn();
    
        if ($count > 0) {
            $message = "Data ini masih terhubung dengan data lain.";
        } else {
            $sql = "DELETE FROM Buku WHERE id_buku = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_buku]);
            $message = "Buku berhasil dihapus.";
        }
    }

    // Mengambil data buku untuk diedit jika ada ID buku di URL
    if (isset($_GET['edit_id'])) {
        $id_buku = $_GET['edit_id'];
        $sql = "SELECT * FROM Buku WHERE id_buku = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_buku]);
        $buku = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $buku = ['id_buku' => '', 'kode_buku' => '', 'judul' => '', 'pengarang' => '', 'penerbit' => '', 'tahun_terbit' => '', 'rak_buku' => ''];
    }
    ?>

    <?php if (!empty($message)) {
        echo '<script>alert("' . htmlspecialchars($message) . '");</script>';
    } ?>

    <form class="font-weight-bold" action="buku.php" method="post">
        <input type="hidden" name="id_buku" value="<?php echo htmlspecialchars($buku['id_buku']); ?>">
        <label>Kode Buku:</label><br>
        <input type="text" name="kode_buku" value="<?php echo htmlspecialchars($buku['kode_buku']); ?>"><br>
        <label>Judul:</label><br>
        <input type="text" name="judul" value="<?php echo htmlspecialchars($buku['judul']); ?>"><br>
        <label>Pengarang:</label><br>
        <input type="text" name="pengarang" value="<?php echo htmlspecialchars($buku['pengarang']); ?>"><br>
        <label>Penerbit:</label><br>
        <input type="text" name="penerbit" value="<?php echo htmlspecialchars($buku['penerbit']); ?>"><br>
        <label>Tahun Terbit:</label><br>
        <input type="number" name="tahun_terbit" value="<?php echo htmlspecialchars($buku['tahun_terbit']); ?>"><br>
        <label>Rak Buku:</label><br>
        <input type="text" name="rak_buku" value="<?php echo htmlspecialchars($buku['rak_buku']); ?>"><br><br>
        <input type="submit" name="submit" value="Simpan">
    </form>

    <h2 class="mt-5">Daftar Buku</h2>
    <table class="table table-striped " border="1" >
        <thead>
            <tr class="font-weight-bold">
                <th>ID Buku</th>
                <th>Kode Buku</th>
                <th>Judul</th>
                <th>Pengarang</th>
                <th>Penerbit</th>
                <th>Tahun Terbit</th>
                <th>Rak Buku</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query untuk mengambil semua data buku
            $sql = "SELECT * FROM Buku";
            $stmt = $pdo->query($sql);

            // Menampilkan data buku dalam bentuk tabel
            while ($buku = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($buku['id_buku']) . "</td>";
                echo "<td>" . htmlspecialchars($buku['kode_buku']) . "</td>";
                echo "<td>" . htmlspecialchars($buku['judul']) . "</td>";
                echo "<td>" . htmlspecialchars($buku['pengarang']) . "</td>";
                echo "<td>" . htmlspecialchars($buku['penerbit']) . "</td>";
                echo "<td>" . htmlspecialchars($buku['tahun_terbit']) . "</td>";
                echo "<td>" . htmlspecialchars($buku['rak_buku']) . "</td>";
                echo "<td>";
                echo "<a href='buku.php?edit_id=" . htmlspecialchars($buku['id_buku']) . "' class='btn btn-warning btn-sm mr-2'>Edit</a>";
                echo "<a href='buku.php?delete_id=" . htmlspecialchars($buku['id_buku']) . "' class='btn btn-danger btn-sm'>Hapus</a>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

</div>

<script>
    function showAlert(message) {
        alert(message);
    }
</script>

<?php include '../includes/footer.php'; ?>
