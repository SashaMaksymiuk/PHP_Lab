<main>
    <h2>Список усіх новин</h2>

    <?php
    // Підключаємося до бази
    $conn = new mysqli('localhost', 'root', '', 'chess_news_db');
    if ($conn->connect_error) {
        die("Помилка підключення: " . $conn->connect_error);
    }

    // Перевіряємо, чи поточний користувач - адміністратор
    $is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

    if ($is_admin) {
        // Адмін бачить ВСІ новини. ORDER BY date DESC - сортує від найновіших до найстаріших
        $sql = "SELECT * FROM news ORDER BY date DESC";
    } else {
        // Усі інші бачать ТІЛЬКИ ті, де visible = 1
        $sql = "SELECT * FROM news WHERE visible = 1 ORDER BY date DESC";
    }

    // Виконуємо запит
    $result = $conn->query($sql);

    // 2. Виводимо новини, якщо вони є
    if ($result->num_rows > 0) {
        // Цикл while бере по одному рядку з бази, поки вони не закінчаться
        while ($row = $result->fetch_assoc()) {
            
            // Малюємо рамочку для кожної новини
            echo "<div style='border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; border-radius: 5px; background: #fff;'>";
            
            // Підказочка для адміна, якщо новина прихована
            if ($is_admin && $row['visible'] == 0) {
                echo "<span style='color: red; font-size: 12px; font-weight: bold;'>[ПРИХОВАНА НОВИНА - бачить тільки адмін]</span><br>";
            }
            
            // Виводимо заголовок і дату
            echo "<h3 style='margin-top: 5px; margin-bottom: 5px;'>" . htmlspecialchars($row['title']) . "</h3>";
            echo "<p style='font-size: 12px; color: #888; margin-top: 0;'>Дата публікації: " . $row['date'] . "</p>";
            
            // Виводимо скорочений текст (щоб велика новина не розтягувала весь список)
            $short_content = mb_strimwidth($row['content'], 0, 150, "...");
            echo "<p>" . htmlspecialchars($short_content) . "</p>";

            // 3. БЛОК З КНОПКАМИ 
            echo "<div style='margin-top: 15px;'>";
            
            // Кнопку "Перегляд" бачать абсолютно всі
            echo "<a href='index.php?action=view_news&id=" . $row['id'] . "' style='padding: 5px 10px; background: #2196F3; color: white; text-decoration: none; border-radius: 3px; margin-right: 10px;'>Перегляд</a>";
            
            // Кнопки "Редагувати" та "Видалити" бачить ТІЛЬКИ адмін
            if ($is_admin) {
                echo "<a href='index.php?action=update_news&id=" . $row['id'] . "' style='padding: 5px 10px; background: #FFC107; color: black; text-decoration: none; border-radius: 3px; margin-right: 10px;'>Редагувати</a>";
                
                // Тут використано JavaScript метод confirm()
                echo "<a href='index.php?action=delete_news&id=" . $row['id'] . "' onclick='return confirm(\"Ви дійсно хочете видалити цю новину?\");' style='padding: 5px 10px; background: #F44336; color: white; text-decoration: none; border-radius: 3px;'>Видалити</a>";
            }
            
            echo "</div>"; 
            echo "</div>"; 
        }
    } else {
        echo "<p>Наразі жодної новини немає. Станьте першим, хто її додасть!</p>";
    }

    $conn->close();
    ?>
</main>