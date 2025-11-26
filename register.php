<?php
require_once 'config.php';

$categories_stmt = $pdo->query("SELECT * FROM categories");
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Валидация и санитизация данных
        $full_name = sanitize_input($_POST['full_name']);
        $phone = sanitize_input($_POST['phone']);
        $age = (int)$_POST['age'];
        $selected_categories = $_POST['categories'] ?? [];

        // Валидация
        if (empty($full_name) || empty($phone) || empty($age) || empty($selected_categories)) {
            throw new Exception('Все поля обязательны для заполнения');
        }

        if ($age < 1 || $age > 100) {
            throw new Exception('Некорректный возраст');
        }

        // Обработка загрузки фото
        $photo_path = '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photo_ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $photo_name = uniqid() . '.' . $photo_ext;
            $photo_path = 'uploads.photo' . $photo_name;
            
            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
                throw new Exception('Ошибка загрузки фото');
            }
        }

        // Обработка загрузки музыки
        $music_path = '';
        if (isset($_FILES['music']) && $_FILES['music']['error'] === UPLOAD_ERR_OK) {
            $music_ext = pathinfo($_FILES['music']['name'], PATHINFO_EXTENSION);
            $music_name = uniqid() . '.' . $music_ext;
            $music_path = 'uploads.music' . $music_name;
            
            if (!move_uploaded_file($_FILES['music']['tmp_name'], $music_path)) {
                throw new Exception('Ошибка загрузки музыки');
            }
        }

        // Запись в БД
        $stmt = $pdo->prepare("INSERT INTO applications (full_name, phone, age, photo_path, music_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $phone, $age, $photo_path, $music_path]);
        $application_id = $pdo->lastInsertId();

        // Связь с категориями
        $stmt = $pdo->prepare("INSERT INTO application_category (application_id, category_id) VALUES (?, ?)");
        foreach ($selected_categories as $cat_id) {
            $stmt->execute([$application_id, $cat_id]);
        }

        $message = '<div class="success">Заявка успешно отправлена!</div>';
        
    } catch (Exception $e) {
        $message = '<div class="error">Ошибка: ' . $e->getMessage() . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Конкурс Прожектор</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css.css">
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="nav-brand">
                <a href="index.php">Прожектор</a>
            </div>
            <div class="nav-links">
                <a href="index.php">Главная</a>
                <a href="index.php#about">О конкурсе</a>
                <a href="index.php#contact">Контакты</a>
            </div>
        </nav>
    </header>

    <section class="section">
        <div class="container">
            <h1>Регистрация на конкурс</h1>
            
            <?= $message ?>

            <form method="POST" enctype="multipart/form-data" class="registration-form">
                <div class="form-group">
                    <label for="full_name">ФИО *</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>

                <div class="form-group">
                    <label for="phone">Номер телефона *</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>

                <div class="form-group">
                    <label for="age">Возраст *</label>
                    <input type="number" id="age" name="age" min="1" max="100" required>
                </div>

                <div class="form-group">
                    <label>Выберите конкурсы для участия *</label>
                    <div class="categories-checkbox">
                        <?php foreach ($categories as $category): ?>
                        <label class="checkbox-label">
                            <input type="checkbox" name="categories[]" value="<?= $category['id'] ?>">
                            <?= $category['name'] ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="photo">Загрузите фото *</label>
                    <input type="file" id="photo" name="photo" accept="image/*" required>
                </div>

                <div class="form-group">
                    <label for="music">Загрузите музыку (для дефиле)</label>
                    <input type="file" id="music" name="music" accept="audio/*">
                </div>

                <button type="submit" class="btn-primary">Отправить заявку</button>
            </form>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Конкурс "Прожектор". Все права защищены.</p>
        </div>
    </footer>
</body>
</html>