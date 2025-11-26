<?php
require_once 'config.php';

// Если уже авторизован, перенаправляем в админку
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];

    // Простая проверка - для тестирования
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Неверное имя пользователя или пароль';
    }
    
    // Альтернативная проверка из базы данных
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Проверяем пароль
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $user['username'];
                header('Location: admin.php');
                exit;
            }
        }
        
        $error = 'Неверное имя пользователя или пароль';
        
    } catch (Exception $e) {
        $error = 'Ошибка базы данных: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход для администратора</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css.css">
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <h1>Вход для администратора</h1>
            
            <?php if ($error): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="username">Имя пользователя</label>
                    <input type="text" id="username" name="username" value="admin" required>
                </div>

                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" value="admin123" required>
                </div>

                <button type="submit" class="btn-primary">Войти</button>
            </form>

            <div class="test-credentials">
                <p><strong>Тестовые данные:</strong></p>
                <p>Логин: admin</p>
                <p>Пароль: admin123</p>
            </div>

            <a href="index.php" class="back-link">← На главную</a>
        </div>
    </div>
</body>
</html>