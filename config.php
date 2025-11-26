<?php
session_start();

// Включить отображение ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'users';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}

function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

// Функция для создания папок
function create_upload_dirs() {
    $upload_dirs = ['uploads/photos', 'uploads/music'];
    foreach ($upload_dirs as $dir) {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                die("Не удалось создать папку: $dir");
            }
        }
    }
}

// Создаем папки при подключении
create_upload_dirs();
?>