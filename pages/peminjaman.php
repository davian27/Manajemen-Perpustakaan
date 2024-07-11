<?php include '../config/database.php'; ?>
<?php include '../includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <title>Manajemen Peminjaman</title>
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
            <li><a href="#">Peminjaman</a></li>
            <li><a href="./history_peminjaman.php">History</a></li>
        </ul>
    </div>
</header>

<div class="container mt-5">
    <h1>Manajemen Peminjaman</h1>

    <?php
    if (isset($_POST['submit'])) {
        $id_peminjaman = $_POST['id_peminjaman'];
        $id_anggota = $_POST['id_anggota'];
        $id_buku = $_POST['id_buku'];
        $tanggal_pinjam = $_POST['tanggal_pinjam'];
        $tanggal_kembali = $_POST['tanggal_kembali'];

        if ($id_peminjaman) {
            $sql = "UPDATE peminjaman SET id_anggota = ?, id_buku = ?, tanggal_pinjam = ?, tanggal_kembali = ? WHERE id_peminjaman = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_anggota, $id_buku, $tanggal_pinjam, $tanggal_kembali, $id_peminjaman]);
            $message = "Peminjaman berhasil diperbarui.";
        } else {
            $sql = "INSERT INTO peminjaman (id_anggota, id_buku, tanggal_pinjam, tanggal_kembali) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_anggota, $id_buku, $tanggal_pinjam, $tanggal_kembali]);
            $message = "Peminjaman berhasil ditambahkan.";
        }
    }

    if (isset($_GET['delete_id'])) {
        $id_peminjaman = $_GET['delete_id'];
        $sql = "DELETE FROM peminjaman WHERE id_peminjaman = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_peminjaman]);
        $message = "Peminjaman berhasil dihapus.";
    }

    if (isset($_GET['return_id'])) {
        $id_peminjaman = $_GET['return_id'];

        $sql = "SELECT * FROM peminjaman WHERE id_peminjaman = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_peminjaman]);
        $peminjaman = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($peminjaman) {
            $sql = "INSERT INTO history_peminjaman (id_peminjaman, id_anggota, id_buku, tanggal_pinjam, tanggal_kembali, tanggal_kembali_sebenarnya) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $peminjaman['id_peminjaman'],
                $peminjaman['id_anggota'],
                $peminjaman['id_buku'],
                $peminjaman['tanggal_pinjam'],
                $peminjaman['tanggal_kembali'],
                date('Y-m-d')
            ]);

            $sql = "DELETE FROM peminjaman WHERE id_peminjaman = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_peminjaman]);

            $message = "Buku berhasil dikembalikan.";
        }
    }

    if (isset($_GET['edit_id'])) {
        $id_peminjaman = $_GET['edit_id'];
        $sql = "SELECT * FROM peminjaman WHERE id_peminjaman = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_peminjaman]);
        $peminjaman = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $peminjaman = ['id_peminjaman' => '', 'id_anggota' => '', 'id_buku' => '', 'tanggal_pinjam' => '', 'tanggal_kembali' => ''];
    }
    ?>

    <?php if (!empty($message)) {
        echo '<script>alert("' . htmlspecialchars($message) . '");</script>';
    } ?>

    <form id="peminjamanForm" action="peminjaman.php" method="post" onsubmit="return validateDates()">
        <input type="hidden" name="id_peminjaman" value="<?php echo htmlspecialchars($peminjaman['id_peminjaman']); ?>">
        <div class="form-group">
            <label>Nama Anggota:</label>
            <select class="form-control select2" name="id_anggota" required>
                <option value="">Pilih Anggota</option>
                <?php
                $sql = "SELECT id_anggota, nama FROM anggota";
                $stmt = $pdo->query($sql);
                while ($anggota = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $selected = ($peminjaman['id_anggota'] == $anggota['id_anggota']) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($anggota['id_anggota']) . "' $selected>" . htmlspecialchars($anggota['nama']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label>Jenis Buku:</label>
            <select class="form-control select2" name="id_buku" required>
                <option value="">Pilih Buku</option>
                <?php
                $sql = "SELECT id_buku, judul FROM buku";
                $stmt = $pdo->query($sql);
                while ($buku = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $selected = ($peminjaman['id_buku'] == $buku['id_buku']) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($buku['id_buku']) . "' $selected>" . htmlspecialchars($buku['judul']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label>Tanggal Pinjam:</label>
            <input type="date" class="form-control" name="tanggal_pinjam" id="tanggal_pinjam" value="<?php echo htmlspecialchars($peminjaman['tanggal_pinjam']); ?>" required>
        </div>
        <div class="form-group">
            <label>Tanggal Kembali:</label>
            <input type="date" class="form-control" name="tanggal_kembali" id="tanggal_kembali" value="<?php echo htmlspecialchars($peminjaman['tanggal_kembali']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
    </form>

    <h2 class="mt-5">Daftar Peminjaman</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID Peminjaman</th>
                <th>ID Anggota</th>
                <th>Jenis Buku</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM peminjaman";
            $stmt = $pdo->query($sql);

            while ($peminjaman = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($peminjaman['id_peminjaman']) . "</td>";
                echo "<td>" . htmlspecialchars($peminjaman['id_anggota']) . "</td>";
                echo "<td>" . htmlspecialchars($peminjaman['id_buku']) . "</td>";
                echo "<td>" . htmlspecialchars($peminjaman['tanggal_pinjam']) . "</td>";
                echo "<td>" . htmlspecialchars($peminjaman['tanggal_kembali']) . "</td>";
                echo "<td>";
                echo "<a href='peminjaman.php?edit_id=" . htmlspecialchars($peminjaman['id_peminjaman']) . "' class='btn btn-warning btn-sm'>Edit</a> ";
                echo "<a href='peminjaman.php?delete_id=" . htmlspecialchars($peminjaman['id_peminjaman']) . "' class='btn btn-danger btn-sm'>Hapus</a> ";
                echo "<a href='peminjaman.php?return_id=" . htmlspecialchars($peminjaman['id_peminjaman']) . "' class='btn btn-success btn-sm'>Kembalikan</a>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('.select2').select2();
    });

    function validateDates() {
        const tanggalPinjam = new Date(document.getElementById('tanggal_pinjam').value);
        const tanggalKembali = new Date(document.getElementById('tanggal_kembali').value);
        
        if (tanggalKembali < tanggalPinjam) {
            alert('Tanggal Kembali tidak boleh kurang dari Tanggal Pinjam.');
            return false;
        }
        
        return true;
    }
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
