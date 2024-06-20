<?php
include 'includes/db.php';

$category_id = $_GET['category_id'] ?? null;
$referer = 'index.php'; // Set referer to index.php

if (!$category_id) {
    header('Location: all_categories.php');
    exit();
}

// Fetch category name
$category_query = "SELECT name FROM categories WHERE id = $category_id";
$category_result = mysqli_query($conn, $category_query);
$category = mysqli_fetch_assoc($category_result);

if (!$category) {
    echo "Kategori tidak ditemukan.";
    exit();
}

// Fetch articles in the category
$articles_query = "SELECT * FROM articles WHERE category_id = $category_id";
$articles_result = mysqli_query($conn, $articles_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Articles in <?php echo htmlspecialchars($category['name']); ?> - ALVR BLOG</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="category-articles-page">
    <header>
        <div class="container">
            <div class="logo-container">
                <h1 class="site-title">Alvr</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Kembali</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="article-section">
            <div class="article-header">
                <h2 class="article-title">Articles in <?php echo htmlspecialchars($category['name']); ?></h2>
                <!-- <p class="article-description">Daftar artikel dalam kategori <?php echo htmlspecialchars($category['name']); ?></p> -->
            </div>
            <div class="article-container">
                <?php
                if (mysqli_num_rows($articles_result) > 0) {
                    while ($row = mysqli_fetch_assoc($articles_result)) {
                        echo '<div class="article-card">';
                        echo '<img src="uploads/' . $row['image'] . '" alt="' . $row['title'] . '">';
                        echo '<h3>' . $row['title'] . '</h3>';
                        echo '<p>' . substr($row['content'], 0, 100) . '...</p>';
                        echo '<a href="article_detail.php?id=' . $row['id'] . '&referer=category_articles.php?category_id=' . $category_id . '">Selengkapnya ⟶</a>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>Tidak ada artikel dalam kategori ini.</p>';
                }
                ?>
            </div>
        </section>
    </main>
</body>
</html>
