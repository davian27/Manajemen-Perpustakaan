<?php include '../includes/header.php'; ?>
<?php include '../config/database.php'; ?>

<link rel="stylesheet" href="../assets/css/buku.css">
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/form.css">
<link rel="stylesheet" href="../assets/css/style.css">
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
            <li><a href="./history_peminjaman.php">History</a></li>
        </ul>
    </div>
</header>

<div class="container">
    <h1>Manajemen Genre</h1>

    <?php
    if (isset($_POST['submit'])) {
        $id_buku = $_POST['id_buku'];
        $id_kategori = $_POST['id_kategori'];
        $genre = trim($_POST['genre']);
        $id = $_POST['id'];
        $errors = [];

        // Validasi data
        if (empty($id_buku)) {
            $errors[] = "ID Buku tidak boleh kosong.";
        }
        if (empty($id_kategori)) {
            $errors[] = "ID Kategori tidak boleh kosong.";
        }
        if (empty($genre)) {
            $errors[] = "Genre tidak boleh kosong.";
        }

        // Jika tidak ada error, proses data
        if (empty($errors)) {
            if ($id) {
                // Query untuk memperbarui data genre
                $sql = "UPDATE buku_kategori SET id_buku = ?, id_kategori = ?, genre = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_buku, $id_kategori, $genre, $id]);
                $message = "Genre berhasil diperbarui.";
            } else {
                // Query untuk menambah data genre
                $sql = "INSERT INTO buku_kategori (id_buku, id_kategori, genre) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_buku, $id_kategori, $genre]);
                $message = "Genre berhasil ditambahkan.";
            }
        } else {
            $message = implode('<br>', $errors);
        }
    }

    if (isset($_GET['delete_id'])) {
        $id = $_GET['delete_id'];
        $sql = "DELETE FROM buku_kategori WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $message = "Genre berhasil dihapus.";
    }

    if (isset($_GET['edit_id'])) {
        $id = $_GET['edit_id'];
        $sql = "SELECT * FROM buku_kategori WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $genre = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $genre = ['id' => '', 'id_buku' => '', 'id_kategori' => '', 'genre' => ''];
    }

    if (!empty($message)) {
        echo '<script>alert("' . $message . '");</script>';
    }
    ?>

    <form action="genre.php" method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($genre['id']); ?>">
        <label>ID Buku:</label><br>
        <input type="number" name="id_buku" value="<?php echo htmlspecialchars($genre['id_buku']); ?>"><br>
        <label>Kategori:</label><br>
        <select name="id_kategori">
            <?php
            $sql = "SELECT * FROM Kategori";
            $stmt = $pdo->query($sql);
            while ($kategori = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $selected = ($kategori['id_kategori'] == $genre['id_kategori']) ? 'selected' : '';
                echo "<option value='{$kategori['id_kategori']}' $selected>{$kategori['nama_kategori']}</option>";
            }
            ?>
        </select><br>
        <label>Genre:</label><br>
        <input type="text" name="genre" value="<?php echo htmlspecialchars($genre['genre']); ?>"><br><br>
        <input type="submit" name="submit" value="Simpan">
    </form>

    <h2>Daftar Genre</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>ID Buku</th>
                <th>Kategori</th>
                <th>Genre</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT bk.id, bk.id_buku, k.nama_kategori, bk.genre FROM buku_kategori bk
                    JOIN Kategori k ON bk.id_kategori = k.id_kategori";
            $stmt = $pdo->query($sql);

            while ($genre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>{$genre['id']}</td>";
                echo "<td>{$genre['id_buku']}</td>";
                echo "<td>{$genre['nama_kategori']}</td>";
                echo "<td>{$genre['genre']}</td>";
                echo "<td>";
                echo "<a href='genre.php?edit_id={$genre['id']}'>Edit</a> | ";
                echo "<a href='genre.php?delete_id={$genre['id']}' onclick='return confirm(\"Apakah Anda yakin ingin menghapus genre ini?\")'>Hapus</a>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
