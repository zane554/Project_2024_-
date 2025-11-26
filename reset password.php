<?php
require_once 'config.php';

// Простой скрипт для сброса пароля администратора
$new_password = 'admin123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

try {
    // Очищаем таблицу пользователей
    $pdo->exec("DELETE FROM users");
    
    // Добавляем нового администратора
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
    $stmt->execute(['admin', $hashed_password]);
    
    echo "Пароль сброшен!<br>";
    echo "Логин: admin<br>";
    echo "Пароль: $new_password<br>";
    echo "<a href='login.php'>Перейти к входу</a>";
    
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>