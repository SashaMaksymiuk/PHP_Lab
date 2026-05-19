<main>
    <?php
    // Зчитуємо ID і ПРИМУСОВО робимо його числом (int)
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0; 
    
    $conn = new mysqli('localhost', 'root', '', 'chess_news_db');
    if ($conn->connect_error) {
        die("Помилка підключення: " . $conn->connect_error);
    }

    $is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

    // Якщо адмін - може дивитися навіть приховані (visible = 0)
    // Якщо гість/юзер - може дивитися тільки опубліковані (visible = 1)
    if ($is_admin) {
        $stmt = $conn->prepare("SELECT * FROM news WHERE id = ?");
    } else {
        $stmt = $conn->prepare("SELECT * FROM news WHERE id = ? AND visible = 1");
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Перевіряємо, чи існує така новина
    if ($result->num_rows === 1) {
        $news = $result->fetch_assoc();
        echo "<h2 style='margin-bottom: 5px;'>" . htmlspecialchars($news['title']) . "</h2>";
        echo "<p style='color: #888; font-size: 13px; margin-top: 0;'>Дата: " . $news['date'] . " | Автор ID: " . $news['author_id'] . "</p>";
        
        // Функція nl2br зберігає абзаци (переноси рядків), щоб текст не злипався
        echo "<div style='font-size: 16px; line-height: 1.6; margin-top: 20px;'>" . nl2br(htmlspecialchars($news['content'])) . "</div>";
        
    } else {
        echo "<h2>Помилка</h2>";
        echo "<div class='error-box'>Такої сторінки не існує.</div>";
    }

    $stmt->close();
    $conn->close();
    ?>
    
    <br><br>
    <a href="index.php?action=news" style="padding: 10px 15px; background: #333; color: white; text-decoration: none; border-radius: 4px;">&larr; Назад до списку</a>
</main>