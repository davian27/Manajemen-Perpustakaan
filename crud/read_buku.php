<?php include '../config/database.php'; ?>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h1>Daftar Buku</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID Buku</th>
                <th>Judul</th>
                <th>Pengarang</th>
                <th>Penerbit</th>
                <th>Tahun Terbit</th>
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
                echo "<td>{$buku['id_buku']}</td>";
                echo "<td>{$buku['judul']}</td>";
                echo "<td>{$buku['pengarang']}</td>";
                echo "<td>{$buku['penerbit']}</td>";
                echo "<td>{$buku['tahun_terbit']}</td>";
                echo "<td>";
                echo "<a href='update_buku.php?id_buku={$buku['id_buku']}'>Edit</a> | ";
                echo "<a href='delete_buku.php?id_buku={$buku['id_buku']}'>Hapus</a>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
