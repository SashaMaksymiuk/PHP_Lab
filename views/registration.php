<main>
    <h2>Реєстрація нового користувача</h2>

    <?php
    $errors = []; 
    
    $login = $email = $address = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_repeat = $_POST['password_repeat'] ?? '';
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if (!preg_match('/^[a-zA-Zа-яА-ЯіІїЇєЄґҐ0-9_\-]{4,}$/u', $login)) {
            $errors[] = "Логін має містити не менше 4 символів (лише літери, цифри, '_' та '-').";
        }

        if (!preg_match('/^(?=.*[a-zа-яієїґ])(?=.*[A-ZА-ЯІЄЇҐ])(?=.*\d).{7,}$/u', $password)) {
            $errors[] = "Пароль має бути не менше 7 символів, містити обов'язково великі та малі літери, а також цифри.";
        }

        if ($password !== $password_repeat) {
            $errors[] = "Введені паролі не співпадають.";
        }

        if (!preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
            $errors[] = "Введіть коректну електронну пошту (наприклад, user@mail.com).";
        }

        if (!empty($address)) {
            if (mb_strlen($address, 'UTF-8') > 255) {
                $errors[] = "Адреса не може перевищувати 255 символів.";
            } 
            
            elseif (!preg_match('/^[a-zA-Zа-яА-ЯіІїЇєЄґҐ\s\.\-\'’]+,\s*\d+[a-zA-Zа-яА-ЯіІїЇєЄґҐ0-9\/\-]*$/u', $address)) {
                $errors[] = "Невірний формат адреси. Вона повинна мати вигляд: Назва вулиці, номер (наприклад: вул. Університетська, 28/18 або пров. Індустріальний, 28А).";
            }
        }

        if (empty($errors)) {
            header('Location: index.php?action=registration_successful');
            exit; 
        }
    }
    ?>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=registration">
        <div class="form-group">
            <label for="login">Логін:</label>
            <input type="text" id="login" name="login" value="<?= htmlspecialchars($login) ?>">
        </div>

        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password">
        </div>

        <div class="form-group">
            <label for="password_repeat">Повторіть пароль:</label>
            <input type="password" id="password_repeat" name="password_repeat">
        </div>

        <div class="form-group">
            <label for="email">Електронна пошта:</label>
            <input type="text" id="email" name="email" value="<?= htmlspecialchars($email) ?>">
        </div>

        <div class="form-group">
            <label for="address">Адреса (необов'язково):</label>
            <input type="text" id="address" name="address" placeholder="Наприклад: вул. Університетська, 28" value="<?= htmlspecialchars($address) ?>">
        </div>

        <button type="submit" class="btn-submit">Зареєструватися</button>
    </form>
</main>