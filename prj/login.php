<?php
require 'config.php';
session_start();

$message = '';
$redirect = $_GET['redirect'] ?? 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $redirect = $_POST['redirect'] ?? 'index.php';

    try {
        // Получаем пользователя по логину
        $stmt = $pdo->prepare("SELECT * FROM user WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Проверяем наличие пользователя и пароль
        if ($user && password_verify($password, $user['password'])) {

            // Сохраняем пользователя в сессию
            $_SESSION['user'] = [
                'user_id' => $user['user_id'],
                'login' => $user['login'],
                'username' => $user['username'],
                'phone' => $user['phone'] ?? '',
                'address' => $user['address'] ?? '',
                'role' => $user['role']
            ];

            // Редирект по роли
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php"); // Админ → панель
            } elseif ($user['role'] === 'owner') {
                header("Location: " . $redirect);       // Владелец → редирект на страницу
            } else {
                header("Location: index.php");          // Другие роли → главная
            }
            exit;

        } else {
            $message = "❌ Неверный логин или пароль.";
        }
    } catch (Exception $e) {
        $message = "❌ Ошибка при входе: " . htmlspecialchars($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход в систему</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2>Вход в систему</h2>

    <form method="post">
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

        <label>E-mail или логин:</label>
        <input type="text" name="login" required placeholder="Введите e-mail">

        <label>Пароль:</label>
        <input type="password" name="password" required placeholder="Введите пароль">

        <button type="submit">Войти</button>
    </form>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
</div>

</body>
</html>