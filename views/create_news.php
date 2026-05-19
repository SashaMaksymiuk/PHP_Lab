<?php
// Захист сторінки: якщо користувач не авторизований, викидаємо його на сторінку входу
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

$error_msg = '';
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (!empty($title) && !empty($content)) {
        // Підключення до БД
        $conn = new mysqli('localhost', 'root', '', 'chess_news_db');
        if ($conn->connect_error) {
            die("Помилка підключення: " . $conn->connect_error);
        }

        //  якщо адмін - visible = 1, якщо звичайний юзер - visible = 0
        $visible = (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) ? 1 : 0;
        $author_id = $_SESSION['user_id'];

        // Використовуємо підготовлені запити для захисту від SQL-ін'єкцій
        $stmt = $conn->prepare("INSERT INTO news (title, content, visible, author_id) VALUES (?, ?, ?, ?)");
        
        // "ssii" означає: string, string, integer, integer
        $stmt->bind_param("ssii", $title, $content, $visible, $author_id);

        if ($stmt->execute()) {
            $success_msg = "Новину успішно додано!";
            $title = '';
            $content = '';
        } else {
            $error_msg = "Помилка при додаванні: " . $conn->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        $error_msg = "Будь ласка, заповніть усі поля!";
    }
}
?>

<main>
    <h2>Додати нову інформацію</h2>

    <?php if (!empty($error_msg)): ?>
        <div class="error-box">
            <p><?= htmlspecialchars($error_msg) ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($success_msg)): ?>
        <div class="success-msg">
            <p><?= htmlspecialchars($success_msg) ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=create_news">
        <div class="form-group">
            <label for="title">Заголовок новини:</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($title ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="content">Текст новини:</label>
            <textarea id="content" name="content" rows="6" required style="width: 100%; max-width: 400px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><?= htmlspecialchars($content ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn-submit">Додати новину</button>
    </form>
</main>