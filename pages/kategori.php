<?php include '../includes/header.php'; ?>
<?php include '../config/database.php'; ?>
<link rel="stylesheet" href="../assets/css/buku.css">
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/form.css">
<link rel="stylesheet" href="../assets/css/style.css">
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

<div class="container">
    <h1>Manajemen Kategori</h1>
    
    <?php
    // Cek apakah form untuk menambah atau memperbarui kategori telah disubmit
    if (isset($_POST['submit'])) {
        $id_kategori = $_POST['id_kategori'];
        $nama_kategori = trim($_POST['nama_kategori']);
        $errors = [];

        // Validasi nama kategori
        if (empty($nama_kategori)) {
            $errors[] = "Nama kategori tidak boleh kosong.";
        }

        // Jika tidak ada error, proses data
        if (empty($errors)) {
            if ($id_kategori) {
                // Query untuk memperbarui data kategori
                $sql = "UPDATE Kategori SET nama_kategori = ? WHERE id_kategori = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nama_kategori, $id_kategori]);
                $message = "Kategori berhasil diperbarui.";
            } else {
                // Query untuk menambah data kategori
                $sql = "INSERT INTO Kategori (nama_kategori) VALUES (?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nama_kategori]);
                $message = "Kategori berhasil ditambahkan.";
            }
        } else {
            $message = implode('<br>', $errors);
        }
    }

    // Cek apakah ada ID kategori untuk dihapus
    if (isset($_GET['delete_id'])) {
        $id_kategori = $_GET['delete_id'];
        $sql = "DELETE FROM Kategori WHERE id_kategori = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_kategori]);
        $message = "Kategori berhasil dihapus.";
    }

    // Mengambil data kategori untuk diedit jika ada ID kategori di URL
    if (isset($_GET['edit_id'])) {
        $id_kategori = $_GET['edit_id'];
        $sql = "SELECT * FROM Kategori WHERE id_kategori = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_kategori]);
        $kategori = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $kategori = ['id_kategori' => '', 'nama_kategori' => ''];
    }
    ?>

    <?php if (!empty($message))
    { echo '<script>alert("' . $message . '");</script>'; } ?>

    <form action="kategori.php" method="post">
        <input type="hidden" name="id_kategori" value="<?php echo $kategori['id_kategori']; ?>">
        <label>Nama Kategori:</label><br>
        <input type="text" name="nama_kategori" value="<?php echo htmlspecialchars($kategori['nama_kategori']); ?>"><br><br>
        <input type="submit" name="submit" value="Simpan">
    </form>

    <h2>Daftar Kategori</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID Kategori</th>
                <th>Nama Kategori</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query untuk mengambil semua data kategori
            $sql = "SELECT * FROM Kategori";
            $stmt = $pdo->query($sql);

            // Menampilkan data kategori dalam bentuk tabel
            while ($kategori = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>{$kategori['id_kategori']}</td>";
                echo "<td>{$kategori['nama_kategori']}</td>";
                echo "<td>";
                echo "<a href='kategori.php?edit_id={$kategori['id_kategori']}'>Edit</a> | ";
                echo "<a href='kategori.php?delete_id={$kategori['id_kategori']}'>Hapus</a>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
