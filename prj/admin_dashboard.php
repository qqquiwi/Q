<?php
require 'config.php';
session_start();

// Проверяем, что вошёл админ
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$admin = $_SESSION['user']; // данные администратора
$message = '';

// Добавление нового ветеринара
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_vet'])) {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $username = trim($_POST['username'] ?? '');

    if ($login && $password && $username) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Проверяем, что логин ещё не занят
            $check = $pdo->prepare("SELECT user_id FROM user WHERE login = ?");
            $check->execute([$login]);
            if ($check->rowCount() > 0) {
                $message = "⚠️ Такой логин уже существует.";
            } else {
                // Вставляем нового ветеринара
                $stmt = $pdo->prepare("
                    INSERT INTO user (login, password, username, role)
                    VALUES (?, ?, ?, 'vet')
                ");
                $stmt->execute([$login, $hashedPassword, $username]);
                $message = "✅ Ветеринар успешно добавлен!";
            }
        } catch (Exception $e) {
            $message = "❌ Ошибка: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $message = "⚠️ Заполните все обязательные поля!";
    }
}

// Получаем всех пользователей
$users = $pdo->query("SELECT user_id, login, username, role FROM user ORDER BY user_id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель администратора</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2>Панель администратора</h2>
    <p>Привет, <?= htmlspecialchars($admin['username']) ?> | <a href="logout.php">Выйти</a></p>

    <?php if ($message): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <h3>Добавить ветеринара</h3>
    <form method="post">
        <input type="hidden" name="add_vet" value="1">
        <label>Логин:</label>
        <input type="text" name="login" required>
        <label>Пароль:</label>
        <input type="password" name="password" required>
        <label>Имя:</label>
        <input type="text" name="username" required>
        <button type="submit">Добавить</button>
    </form>

    <h3>Все пользователи</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Логин</th>
            <th>Имя</th>
            <th>Роль</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['user_id'] ?></td>
                <td><?= htmlspecialchars($user['login']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>