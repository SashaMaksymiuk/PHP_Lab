<main>
    <?php
    // Сюди може зайти тільки адміністратор!
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
        echo "<div class='error-box'>Помилка доступу: У вас немає прав для видалення!</div>";
        exit;
    }

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0; 
    
    $conn = new mysqli('localhost', 'root', '', 'chess_news_db');

    // Спочатку перевіряємо, чи взагалі існує новина з таким ID
    $check_stmt = $conn->prepare("SELECT id FROM news WHERE id = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 1) {
        // Якщо існує - видаляємо
        $del_stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
        $del_stmt->bind_param("i", $id);
        
        if ($del_stmt->execute()) {
            echo "<div class='success-msg'>";
            echo "<h2>Успіх!</h2>";
            echo "<p>Новину успішно видалено.</p>";
            echo "</div>";
        } else {
            echo "<div class='error-box'>Помилка при видаленні бази: " . $conn->error . "</div>";
        }
        $del_stmt->close();
    } else {
        // якщо новини в базі не існує
        echo "<h2>Помилка</h2>";
        echo "<div class='error-box'>Такої сторінки не існує.</div>";
    }

    $check_stmt->close();
    $conn->close();
    ?>

    <br><br>
    <a href="index.php?action=news" style="padding: 10px 15px; background: #333; color: white; text-decoration: none; border-radius: 4px;">Повернутися до списку</a>
</main