<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ALVR BLOG</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <header>
        <div class="container">
            <div class="logo-container">
                <h1 class="site-title">ALVR BLOG</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="#home" class="active">Home</a></li>
                    <li><a href="#article">Article</a></li>
                    <li><a href="#" id="write-link">Write</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <section class="hero" id="home">
            <div class="container-hero">
                <h2>Welcome To ALVR BLOG</h2>
                <p>read more article now</p>
                <a href="all_articles.php?referer=index.php" class="btn">read article</a>
            </div>
        </section>
        <section class="hero-image-section">
            <div class="hero-image-wrapper">
                <img src="images/emyuu.jpg" alt="emyuu" class="hero-image">
                <div class="category-sidebar">
                    <h3>Kategori</h3>
                    <ul class="category-list">
                        <?php
                        include 'includes/db.php';
                        $query = "SELECT * FROM categories LIMIT 6";
                        $result = mysqli_query($conn, $query);

                        if ($result) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<li><a href="category_articles.php?category_id=' . $row['id'] . '">' . $row['name'] . '</a></li>';
                            }
                        } else {
                            echo '<p>Failed to load categories.</p>';
                        }
                        ?>
                    </ul>
                    <a href="all_articles.php?referer=index.php" class="btn-more-articles">more article</a>
                </div>
            </div>
        </section>


        <section class="article-section" id="article">
            <div class="article-header">
                <div class="article-text">
                    <h2 class="article-title">Article</h2>
                    <p class="article-description">read more article</p>
                </div>
                <a href="all_articles.php?referer=index.php" class="article-more">More</a>
            </div>
            <div class="article-container">
                <?php
                $query = "SELECT articles.*, categories.name AS category_name 
                          FROM articles 
                          JOIN categories ON articles.category_id = categories.id 
                          LIMIT 6";
                $result = mysqli_query($conn, $query);

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<div class="article-card">';
                        echo '<img src="uploads/' . $row['image'] . '" alt="' . $row['title'] . '">';
                        echo '<h3>' . $row['title'] . '</h3>';
                        echo '<p>' . substr($row['content'], 0, 100) . '...</p>';
                        echo '<a href="article_detail.php?id=' . $row['id'] . '">Read More ⟶</a>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>Failed to load articles.</p>';
                }
                ?>
            </div>
        </section>
    </main>

    <script>
        document.querySelectorAll('nav ul li a').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                if (this.id !== 'write-link') {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                } else {
                    window.location.href = 'admin/dashboard.php';
                }
            });
        });

        const sections = document.querySelectorAll('section');
        const navLi = document.querySelectorAll('nav ul li a');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (pageYOffset >= (sectionTop - sectionHeight / 3)) {
                    current = section.getAttribute('id');
                }
            });

            navLi.forEach(a => {
                a.classList.remove('active');
                if (a.getAttribute('href').includes(current)) {
                    a.classList.add('active');
                }
            });
        });
    </script>
</body>

</html>