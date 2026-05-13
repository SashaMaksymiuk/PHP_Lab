<main>
    <h2>Авторизація</h2>

    <?php
    $error_msg = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!empty($login) && !empty($password)) {
            // Підключення до БД (як у реєстрації)
            $conn = new mysqli('localhost', 'root', '', 'chess_news_db');
            
            if ($conn->connect_error) {
                die("Помилка підключення: " . $conn->connect_error);
            }

            // Шукаємо користувача за логіном
            $stmt = $conn->prepare("SELECT id, login, password, admin FROM users WHERE login = ?");
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $result = $stmt->get_result();

            // Якщо такий логін існує в базі
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Перевіряємо хеш пароля за допомогою password_verify!
                if (password_verify($password, $user['password'])) {
                    
                    // Пароль підійшов! Записуємо дані в сесію
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['login'] = $user['login'];
                    $_SESSION['is_admin'] = $user['admin'];

                    // Перенаправляємо на головну
                    header('Location: index.php?action=main');
                    exit;
                } else {
                    // Пароль неправильний
                    $error_msg = "Невірний логін або пароль";
                }
            } else {
                // Логін не знайдено
                $error_msg = "Невірний логін або пароль";
            }

            $stmt->close();
            $conn->close();
        } else {
            $error_msg = "Заповніть усі поля!";
        }
    }
    ?>

    <?php if (!empty($error_msg)): ?>
        <div class="error-box">
            <p><?= htmlspecialchars($error_msg) ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=login">
        <div class="form-group">
            <label for="login">Логін:</label>
            <input type="text" id="login" name="login" value="<?= htmlspecialchars($_POST['login'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password">
        </div>

        <button type="submit" class="btn-submit">Увійти</button>
    </form>
</main>