<?php
require 'config.php';
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // безопасно получаем значения из POST
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $phone = preg_replace('/\D/', '', trim($_POST['phone'] ?? ''));
    $address = trim($_POST['address'] ?? '');

    // Проверка, что логин — это e-mail
    if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
        $message = "⚠️ Введите корректный адрес электронной почты в поле логина.";
    }
    // Проверка телефона
    elseif (!preg_match('/^\d{10}$/', $phone)) {
        $message = "⚠️ Неверный формат номера. Введите 10 цифр после +7.";
    }
    // Проверка пароля
    elseif (strlen($password) < 6 || !preg_match('/\d/', $password)) {
        $message = "⚠️ Пароль должен быть минимум 6 символов и содержать хотя бы одну цифру.";
    } else {
        $phone = '+7' . $phone;
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Проверка логина (e-mail) на уникальность
            $check = $pdo->prepare("SELECT user_id FROM user WHERE login = ?");
            $check->execute([$login]);

            if ($check->rowCount() > 0) {
                $message = "⚠️ Пользователь с таким e-mail уже существует. Пожалуйста, используйте другой.";
            } else {
                // ✅ Вставляем владельца
                $stmt = $pdo->prepare("
                    INSERT INTO user (login, password, username, phone, address, role)
                    VALUES (?, ?, ?, ?, ?, 'owner')
                ");
                $stmt->execute([$login, $hashedPassword, $username, $phone, $address]);

                $userId = $pdo->lastInsertId();
                    $message = "✅ Регистрация успешна! Теперь можно <a href='login.php'>войти</a>.";
            }
        } catch (Exception $e) {
            $message = "❌ Ошибка при регистрации: " . htmlspecialchars($e->getMessage());
        }
    }

}
?>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация владельца</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2>Регистрация</h2>

    <form method="post">
        <label>E-mail (логин):</label>
        <input type="email" name="login" required placeholder="example@mail.ru">

        <label>Пароль:</label>
        <input type="password" name="password" required placeholder="Минимум 6 символов, 1 цифра">

        <label>Полное имя:</label>
        <input type="text" name="username" required>

        <label>Номер телефона:</label>
        <div style="display:flex; align-items:center;">
            <span>+7</span>
            <input type="text" name="phone" required placeholder="9123456789" style="margin-left:5px; flex:1;">
        </div>

        <label>Адрес:</label>
        <input type="text" name="address">

        <button type="submit">Зарегистрироваться</button>
    </form>

    <?php if ($message): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>
</div>

</body>
</html>