<?php
session_start();
if (!isset($_SESSION['email'])) {
    echo "Session tidak ada";
    exit();
}
$email = $_SESSION['email'];

// Koneksi ke database
include('../includes/db.php');
$message = "";
$edit_state = false;
$category_id = 0;

// Ambil user_id dari tabel users berdasarkan email
$users_query = "SELECT id FROM users WHERE email='$email'";
$users_result = mysqli_query($conn, $users_query);
$users = mysqli_fetch_assoc($users_result);
$users_id = $users['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save'])) {
        $category_name = $_POST['category_name'];

        // Cek apakah kategori sudah ada
        $check_query = "SELECT * FROM categories WHERE name='$category_name'";
        $check_result = mysqli_query($conn, $check_query);
        if (mysqli_num_rows($check_result) > 0) {
            $message = "Kategori dengan nama yang sama sudah ada.";
        } else {
            if (!empty($category_name)) {
                $query = "INSERT INTO categories (name, user_id) VALUES ('$category_name', '$users_id')";
                if (mysqli_query($conn, $query)) {
                    $message = "Kategori berhasil ditambahkan.";
                } else {
                    $message = "Gagal menambahkan kategori.";
                }
            } else {
                $message = "Nama kategori tidak boleh kosong.";
            }
        }
    } elseif (isset($_POST['update'])) {
        $category_id = $_POST['category_id'];
        $category_name = $_POST['category_name'];

        if (!empty($category_name)) {
            $query = "UPDATE categories SET name='$category_name' WHERE id=$category_id";
            if (mysqli_query($conn, $query)) {
                $message = "Kategori berhasil diupdate.";
            } else {
                $message = "Gagal mengupdate kategori.";
            }
        } else {
            $message = "Nama kategori tidak boleh kosong.";
        }
    }
}

if (isset($_GET['edit'])) {
    $category_id = $_GET['edit'];
    $edit_state = true;
    $rec = mysqli_query($conn, "SELECT * FROM categories WHERE id=$category_id");
    $record = mysqli_fetch_array($rec);
    $category_name = $record['name'];
}

if (isset($_GET['del'])) {
    $category_id = $_GET['del'];
    mysqli_query($conn, "DELETE FROM categories WHERE id=$category_id");
    $message = "Kategori berhasil dihapus.";
}

// Mengambil kategori hanya milik user yang sedang login
$result = mysqli_query($conn, "SELECT * FROM categories WHERE user_id = '$users_id'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin_style.css">
    <title>Category</title>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <div class="logo-container">
                <h2 class="site-title">Alvr</h2>
            </div>
            <nav>
                <ul>
                    <li><a href="dashboard.php"><span class="icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard</a></li>
                    <li><a href="category.php" class="active"><span class="icon"><i class="fas fa-folder-plus"></i></span> Category</a></li>
                    <li><a href="article.php"><span class="icon"><i class="fas fa-file-alt"></i></span> Article</a></li>
                </ul>
            </nav>
            <div class="logout-section">
                <a href="../admin/logout.php"><span class="icon"><i class="fas fa-sign-out-alt"></i></span> Log Out</a>
                <p>Login as : <?php echo $_SESSION['email']; ?></p>
            </div>
        </div>
        <div class="admin-main">
            <h2><?php echo $edit_state ? 'Edit Kategori' : 'Tambah Kategori Baru'; ?></h2>
            <div class="message"><?php echo $message; ?></div>
            <div class="form-container">
                <form action="category.php" method="POST">
                    <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
                    <label for="category_name">Nama Kategori:</label>
                    <input type="text" id="category_name" name="category_name" value="<?php echo $edit_state ? $category_name : ''; ?>" required>
                    <button type="submit" name="<?php echo $edit_state ? 'update' : 'save'; ?>"><?php echo $edit_state ? 'Update' : 'Tambah'; ?> Kategori</button>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nama Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?php echo $row['name']; ?></td>
                            <td>
                                <div class="actions">
                                    <a href="category.php?edit=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                                    <a href="category.php?del=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">Hapus</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
