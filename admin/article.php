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
$article_id = 0;

// Ambil user_id dan username dari tabel users berdasarkan email
$user_query = "SELECT id, username FROM users WHERE email='$email'";
$user_result = mysqli_query($conn, $user_query);
$users = mysqli_fetch_assoc($user_result);
$users_id = $users['id'];
$username = $users['username'];

// Fetch categories
$categories_result = mysqli_query($conn, "SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save'])) {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $author = $username; // Menggunakan username sebagai author
        $image = $_FILES['image']['name'];
        $category_id = $_POST['category'];
        $target = __DIR__ . "/../uploads/" . basename($image);

        if (!empty($title) && !empty($content) && move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $query = "INSERT INTO articles (title, content, image, category_id, author, created_at) VALUES ('$title', '$content', '$image', '$category_id', '$author', NOW())";
            if (mysqli_query($conn, $query)) {
                $message = "Artikel berhasil ditambahkan.";
            } else {
                $message = "Gagal menambahkan artikel.";
            }
        } else {
            $message = "Semua field harus diisi dan gambar harus diupload.";
        }
    } elseif (isset($_POST['update'])) {
        $article_id = $_POST['article_id'];
        $title = $_POST['title'];
        $content = $_POST['content'];
        $image = $_FILES['image']['name'];
        $category_id = $_POST['category'];
        $target = __DIR__ . "/../uploads/" . basename($image);

        if (!empty($title) && !empty($content) && (!empty($image) && move_uploaded_file($_FILES['image']['tmp_name'], $target))) {
            $query = "UPDATE articles SET title='$title', content='$content', image='$image', category_id='$category_id' WHERE id='$article_id'";
            if (mysqli_query($conn, $query)) {
                $message = "Artikel berhasil diupdate.";
            } else {
                $message = "Gagal mengupdate artikel.";
            }
        } elseif (!empty($title) && !empty($content)) {
            $query = "UPDATE articles SET title='$title', content='$content', category_id='$category_id' WHERE id='$article_id'";
            if (mysqli_query($conn, $query)) {
                $message = "Artikel berhasil diupdate.";
            } else {
                $message = "Gagal mengupdate artikel.";
            }
        } else {
            $message = "Judul dan isi artikel tidak boleh kosong.";
        }
    }
}

if (isset($_GET['edit'])) {
    $article_id = $_GET['edit'];
    $edit_state = true;
    $rec = mysqli_query($conn, "SELECT * FROM articles WHERE id='$article_id'");
    $record = mysqli_fetch_array($rec);
    $title = $record['title'];
    $content = $record['content'];
    $image = $record['image'];
    $category_id = $record['category_id'];
}

if (isset($_GET['del'])) {
    $article_id = $_GET['del'];
    mysqli_query($conn, "DELETE FROM articles WHERE id='$article_id'");
    $message = "Artikel berhasil dihapus.";
}

// Mengambil artikel hanya milik user yang sedang login
$result = mysqli_query($conn, "SELECT articles.*, categories.name AS category_name 
                               FROM articles 
                               JOIN categories ON articles.category_id = categories.id 
                               WHERE articles.author = '$username'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin_style.css">
    <title>Article</title>
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
                    <li><a href="category.php"><span class="icon"><i class="fas fa-folder-plus"></i></span> Category</a></li>
                    <li><a href="article.php" class="active"><span class="icon"><i class="fas fa-file-alt"></i></span> Article</a></li>
                </ul>
            </nav>
            <div class="logout-section">
                <a href="../admin/logout.php"><span class="icon"><i class="fas fa-sign-out-alt"></i></span> Log Out</a>
                <p>Login as : <?php echo $_SESSION['email']; ?></p>
            </div>
        </div>
        <div class="admin-main">
            <h2><?php echo $edit_state ? 'Edit Artikel' : 'Tambah Artikel Baru'; ?></h2>
            <div class="message"><?php echo $message; ?></div>
            <div class="form-container">
                <form action="article.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="article_id" value="<?php echo $article_id; ?>">
                    <label for="title">Judul Artikel:</label>
                    <input type="text" id="title" name="title" value="<?php echo $edit_state ? $title : ''; ?>" required>
                    <label for="category">Kategori:</label>
                    <select id="category" name="category" required>
                        <option value="">Pilih Kategori</option>
                        <?php while ($row = mysqli_fetch_assoc($categories_result)) : ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo ($edit_state && $category_id == $row['id']) ? 'selected' : ''; ?>>
                                <?php echo $row['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <label for="content">Isi Artikel:</label>
                    <textarea id="content" name="content" rows="10" required><?php echo $edit_state ? $content : ''; ?></textarea>
                    <label for="image">Upload Gambar:</label>
                    <input type="file" id="image" name="image" <?php echo $edit_state ? '' : 'required'; ?>>
                    <button type="submit" name="<?php echo $edit_state ? 'update' : 'save'; ?>"><?php echo $edit_state ? 'Update' : 'Tambah'; ?> Artikel</button>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Judul Artikel</th>
                        <th>Kategori</th>
                        <th>Isi Artikel</th>
                        <th>Gambar</th>
                        <th>Penulis</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?php echo $row['title']; ?></td>
                            <td><?php echo $row['category_name']; ?></td>
                            <td><?php echo substr($row['content'], 0, 100); ?>...</td>
                            <td><img src="../uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>"></td>
                            <td><?php echo $row['author']; ?></td>
                            <td><?php echo date("d M Y", strtotime($row['created_at'])); ?></td>
                            <td>
                                <div class="actions">
                                    <a href="article.php?edit=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                                    <a href="article.php?del=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus artikel ini?');">Hapus</a>
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
