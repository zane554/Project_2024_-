<?php
require_once 'config.php';

// Получаем категории из БД
$categories_stmt = $pdo->query("SELECT * FROM categories");
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем одобренные отзывы
$reviews_stmt = $pdo->query("SELECT * FROM reviews WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 3");
$reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Конкурс Прожектор - Дефиле и Фото</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css.css">
</head>
<body>
    <!-- Хедер -->
    <header class="header">
        <nav class="nav">
            <div class="nav-brand">Прожектор</div>
            <div class="nav-links">
                <a href="#about">О конкурсе</a>
                <a href="#categories">Категории</a>
                <a href="#reviews">Отзывы</a>
                <a href="#contact">Контакты</a>
                <a href="register.php" class="btn-register">Зарегистрироваться</a>
            </div>
        </nav>
    </header>

    <!-- Главный баннер -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Конкурс Прожектор</h1>
            <p class="hero-subtitle">В центре внимания</p>
            <p class="hero-description">Присоединяйтесь к конкурсу дефиле и фото. Покажите свой стиль и талант всему миру!</p>
            <a href="register.php" class="btn-primary">Зарегистрироваться</a>
        </div>
        <div class="hero-background"></div>
    </section>

    <!-- О конкурсе -->
    <section id="about" class="section">
        <div class="container">
            <h2>Зачем участвовать в «Прожекторе»?</h2>
            <div class="features">
                <div class="feature-card">
                    <h3>Признание и награды</h3>
                    <p>Получите заслуженное признание своего таланта и профессиональные награды от жюри.</p>
                </div>
                <div class="feature-card">
                    <h3>Профессиональная съёмка</h3>
                    <p>Работайте с опытными фотографами и получите качественное портфолио для карьеры.</p>
                </div>
                <div class="feature-card">
                    <h3>Новые возможности</h3>
                    <p>Откройте двери к новым проектам, контрактам и сотрудничеству в индустрии моды.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Категории конкурса -->
    <section id="categories" class="section bg-light">
        <div class="container">
            <h2>Категории конкурса</h2>
            <div class="categories-grid">
                <?php foreach ($categories as $category): ?>
                <div class="category-card">
                    <h3><?= $category['name'] ?></h3>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Отзывы -->
    <section id="reviews" class="section">
        <div class="container">
            <h2>Отзывы участников</h2>
            <div class="reviews-grid">
                <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <div class="review-text">"<?= $review['text'] ?>"</div>
                    <div class="review-author">
                        <strong><?= $review['author_name'] ?></strong>
                        <div class="review-rating">
                            <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                ⭐
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Контакты -->
    <section id="contact" class="section bg-light">
        <div class="container">
            <h2>Свяжитесь с нами</h2>
            <div class="contact-info">
                <p>📞 +7 (999) 123-45-67</p>
                <p>📧 info@projektor.ru</p>
                <p>📍 г. Москва, ул. Примерная, д. 10</p>
            </div>
        </div>
    </section>

    <!-- Футер -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Конкурс "Прожектор". Все права защищены.</p>
            <a href="admin.php" class="admin-link">Вход для администратора</a>
        </div>
    </footer>
</body>
</html>