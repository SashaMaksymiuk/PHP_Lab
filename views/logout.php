<?php
// Очищаємо всі змінні сесії
$_SESSION = [];

// Знищуємо сесію фізично
session_destroy();

// Перенаправляємо на головну сторінку
header('Location: index.php?action=main');
exit;
?>