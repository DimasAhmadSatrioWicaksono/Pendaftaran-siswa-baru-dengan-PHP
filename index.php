<?php
// --- LOGIKA CRUD SEDERHANA DENGAN FILE CSV --- //
$data_file = "data.csv";

// Hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $rows = file($data_file);
    unset($rows[$id]);
    file_put_contents($data_file, $rows);
    header("Location: pendaftaran.php");
    exit();
}

// Proses Simpan atau Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $jk = $_POST['jenis_kelamin'];
    $agama = $_POST['agama'];
    $data = "$nama,$alamat,$jk,$agama\n";

    if (isset($_POST['id_edit']) && $_POST['id_edit'] !== '') {
        // Edit data
        $id = $_POST['id_edit'];
        $rows = file($data_file);
        $rows[$id] = $data;
        file_put_contents($data_file, $rows);
    } else {
        // Tambah data baru
        file_put_contents($data_file, $data, FILE_APPEND);
    }

    header("Location: pendaftaran.php");
    exit();
}

// Ambil data jika sedang edit
$data_edit = ["", "", "", ""];
$edit_id = "";
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $rows = file($data_file);
    if (isset($rows[$edit_id])) {
        $data_edit = str_getcsv($rows[$edit_id]);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pendaftaran Siswa SMK Coding</title>
    <style>
        body {
            background: #ecf0f1;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            padding: 40px;
        }
        .container {
            background: white;
            padding: 30px;
            width: 800px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
        }
        form {
            margin-bottom: 30px;
        }
        input[type=text], textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type=submit] {
            background: #3498db;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type=submit]:hover {
            background: #2980b9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #3498db;
            color: white;
        }
        .actions a {
            margin: 0 5px;
            color: #3498db;
            text-decoration: none;
        }
        .actions a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Pendaftaran Siswa Baru - SMK Coding</h2>

    <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <input type="hidden" name="id_edit" value="<?= htmlspecialchars($edit_id) ?>">

        <label>Nama:</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($data_edit[0]) ?>" required>

        <label>Alamat:</label>
        <textarea name="alamat" required><?= htmlspecialchars($data_edit[1]) ?></textarea>

        <label>Jenis Kelamin:</label><br>
        <label><input type="radio" name="jenis_kelamin" value="Laki-laki" <?= ($data_edit[2] == 'Laki-laki') ? 'checked' : '' ?> required> Laki-laki</label>
        <label><input type="radio" name="jenis_kelamin" value="Perempuan" <?= ($data_edit[2] == 'Perempuan') ? 'checked' : '' ?> required> Perempuan</label>
        <br><br>

        <label>Agama:</label>
        <select name="agama" required>
            <?php
            $agamas = ["Islam", "Kristen", "Katolik", "Hindu", "Budha", "Konghucu"];
            foreach ($agamas as $a) {
                $selected = ($data_edit[3] == $a) ? "selected" : "";
                echo "<option $selected>$a</option>";
            }
            ?>
        </select>

        <input type="submit" value="<?= $edit_id !== "" ? "Update" : "Daftar" ?>">
    </form>

    <h3>Daftar Siswa:</h3>
    <table>
        <tr>
            <th>No</th><th>Nama</th><th>Alamat</th><th>JK</th><th>Agama</th><th>Tindakan</th>
        </tr>
        <?php
        if (file_exists($data_file)) {
            $rows = file($data_file);
            foreach ($rows as $i => $row) {
                $data = str_getcsv($row);
                echo "<tr>
                        <td>".($i+1)."</td>
                        <td>".htmlspecialchars($data[0])."</td>
                        <td>".htmlspecialchars($data[1])."</td>
                        <td>".htmlspecialchars($data[2])."</td>
                        <td>".htmlspecialchars($data[3])."</td>
                        <td class='actions'>
                            <a href='pendaftaran.php?edit=$i'>Edit</a> |
                            <a href='pendaftaran.php?hapus=$i' onclick=\"return confirm('Yakin ingin menghapus?')\">Hapus</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>Belum ada data</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>