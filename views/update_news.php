<?php
// Тільки адміністратор може редагувати новини
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    echo "<main><div class='error-box'>Помилка доступу: У вас немає прав для редагування!</div></main>";
    exit;
}

// Зчитуємо ID з адресного рядка і ПРИМУСОВО робимо його цілим числом 
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$conn = new mysqli('localhost', 'root', '', 'chess_news_db');
if ($conn->connect_error) {
    die("Помилка підключення: " . $conn->connect_error);
}

$error_msg = '';
$success_msg = '';

// 1. ОБРОБКА ВІДПРАВЛЕННЯ ФОРМИ 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $visible = isset($_POST['visible']) ? (int)$_POST['visible'] : 0; // Зчитуємо статус видимості

    if (!empty($title) && !empty($content)) {
        // Використовуємо підготовлений запит для UPDATE
        $stmt = $conn->prepare("UPDATE news SET title = ?, content = ?, visible = ? WHERE id = ?");
        $stmt->bind_param("ssii", $title, $content, $visible, $id);

        if ($stmt->execute()) {
            $success_msg = "Новину успішно оновлено!";
        } else {
            $error_msg = "Помилка при оновленні: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error_msg = "Будь ласка, заповніть усі обов'язкові поля!";
    }
}

// 2. ОТРИМАННЯ СТАРИХ ДАНИХ (Щоб заповнити форму при першому відкритті сторінки)
$fetch_stmt = $conn->prepare("SELECT * FROM news WHERE id = ?");
$fetch_stmt->bind_param("i", $id);
$fetch_stmt->execute();
$result = $fetch_stmt->get_result();

// Перевіряємо, чи взагалі існує новина з таким ID
if ($result->num_rows !== 1) {
    echo "<main><h2>Помилка</h2><div class='error-box'>Такої сторінки не існує.</div></main>";
    $fetch_stmt->close();
    $conn->close();
    exit;
}

$news = $result->fetch_assoc();
$fetch_stmt->close();
$conn->close();
?>

<main>
    <h2>Редагування новини</h2>

    <?php if (!empty($error_msg)): ?>
        <div class="error-box"><p><?= htmlspecialchars($error_msg) ?></p></div>
    <?php endif; ?>

    <?php if (!empty($success_msg)): ?>
        <div class="success-msg">
            <p><?= htmlspecialchars($success_msg) ?></p>
            <br><a href="index.php?action=news" style="color: #333; font-weight: bold; text-decoration: underline;">&larr; Повернутися до новин</a>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=update_news&id=<?= $id ?>">
        <div class="form-group">
            <label for="title">Заголовок новини:</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($news['title']) ?>" required>
        </div>

        <div class="form-group">
            <label for="content">Текст новини:</label>
            <textarea id="content" name="content" rows="6" required style="width: 100%; max-width: 400px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><?= htmlspecialchars($news['content']) ?></textarea>
        </div>

        <div class="form-group">
            <label for="visible">Статус публікації (Тільки для адміна):</label>
            <select id="visible" name="visible" style="padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
                <option value="1" <?= $news['visible'] == 1 ? 'selected' : '' ?>>Опубліковано (Видно всім)</option>
                <option value="0" <?= $news['visible'] == 0 ? 'selected' : '' ?>>Приховано (Видно тільки адміну)</option>
            </select>
        </div>

        <button type="submit" class="btn-submit" style="background: #FFC107; color: #000;">Зберегти зміни</button>
    </form>
</main>