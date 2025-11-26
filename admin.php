<?php
require_once 'config.php';

// Улучшенная проверка авторизации
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Простая проверка для отладки
echo "<pre>";
echo "Сессия: ";
print_r($_SESSION);
echo "</pre>";

// Получаем заявки
try {
    $applications_stmt = $pdo->query("
        SELECT a.*, GROUP_CONCAT(c.name) as category_names 
        FROM applications a 
        LEFT JOIN application_category ac ON a.id = ac.application_id 
        LEFT JOIN categories c ON ac.category_id = c.id 
        GROUP BY a.id 
        ORDER BY a.created_at DESC
    ");
    $applications = $applications_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $applications = [];
    echo "<div class='error'>Ошибка загрузки заявок: " . $e->getMessage() . "</div>";
}

// Получаем отзывы
try {
    $reviews_stmt = $pdo->query("SELECT * FROM reviews ORDER BY created_at DESC");
    $reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $reviews = [];
    echo "<div class='error'>Ошибка загрузки отзывов: " . $e->getMessage() . "</div>";
}

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_review'])) {
        $review_id = (int)$_POST['review_id'];
        $stmt = $pdo->prepare("UPDATE reviews SET is_approved = 1 WHERE id = ?");
        $stmt->execute([$review_id]);
        header('Location: admin.php');
        exit;
    }
    
    if (isset($_POST['delete_review'])) {
        $review_id = (int)$_POST['review_id'];
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$review_id]);
        header('Location: admin.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css.css">
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="nav-brand">Панель администратора</div>
            <div class="nav-links">
                <span>Добро пожаловать, <?= $_SESSION['admin_username'] ?? 'Admin' ?></span>
                <a href="logout.php" class="btn-logout">Выйти</a>
                <a href="index.php" class="btn-secondary">На сайт</a>
            </div>
        </nav>
    </header>

    <div class="admin-container">
        <!-- Заявки -->
        <section class="admin-section">
            <h2>Заявки на конкурс (<?= count($applications) ?>)</h2>
            <?php if (empty($applications)): ?>
                <p>Заявок пока нет</p>
            <?php else: ?>
                <div class="applications-list">
                    <?php foreach ($applications as $application): ?>
                    <div class="application-card">
                        <h3><?= htmlspecialchars($application['full_name']) ?></h3>
                        <p><strong>Телефон:</strong> <?= htmlspecialchars($application['phone']) ?></p>
                        <p><strong>Возраст:</strong> <?= $application['age'] ?></p>
                        <p><strong>Категории:</strong> <?= $application['category_names'] ?></p>
                        <p><strong>Дата подачи:</strong> <?= $application['created_at'] ?></p>
                        <?php if ($application['photo_path']): ?>
                            <p><strong>Фото:</strong> <a href="<?= $application['photo_path'] ?>" target="_blank">Просмотреть</a></p>
                        <?php endif; ?>
                        <?php if ($application['music_path']): ?>
                            <p><strong>Музыка:</strong> <a href="<?= $application['music_path'] ?>" target="_blank">Скачать</a></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Управление отзывами -->
        <section class="admin-section">
            <h2>Управление отзывами (<?= count($reviews) ?>)</h2>
            <?php if (empty($reviews)): ?>
                <p>Отзывов пока нет</p>
            <?php else: ?>
                <div class="reviews-list">
                    <?php foreach ($reviews as $review): ?>
                    <div class="review-card admin-review">
                        <div class="review-text">"<?= htmlspecialchars($review['text']) ?>"</div>
                        <div class="review-author">
                            <strong><?= htmlspecialchars($review['author_name']) ?></strong>
                            <div class="review-rating">
                                <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                    ⭐
                                <?php endfor; ?>
                            </div>
                            <div class="review-status">
                                Статус: <?= $review['is_approved'] ? 'Одобрен' : 'На модерации' ?>
                            </div>
                        </div>
                        <?php if (!$review['is_approved']): ?>
                        <div class="review-actions">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                <button type="submit" name="approve_review" class="btn-success">Одобрить</button>
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                <button type="submit" name="delete_review" class="btn-danger">Удалить</button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>