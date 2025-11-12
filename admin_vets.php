<?php
require 'config.php';
session_start();

// Проверка, что вошёл админ
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$admin = $_SESSION['user'];
$message = '';

// === Добавление нового ветеринара ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Добавление
    if (isset($_POST['add_vet'])) {
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';
        $username = trim($_POST['username'] ?? '');
        $phone = preg_replace('/\D/', '', trim($_POST['phone'] ?? '')); // только цифры
        $specialization = trim($_POST['specialization'] ?? '');

        if ($login && $password && $username && $phone && $specialization) {
            if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
                $message = "⚠️ Логин должен быть адресом электронной почты.";
            } elseif (!preg_match('/^\d{10}$/', $phone)) {
                $message = "⚠️ Неверный формат номера. Введите 10 цифр без +7.";
            } else {
                $phone = '+7' . $phone;
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                try {
                    $check = $pdo->prepare("SELECT user_id FROM user WHERE login = ?");
                    $check->execute([$login]);
                    if ($check->rowCount() > 0) {
                        $message = "⚠️ Такой логин уже существует.";
                    } else {
                        $stmt = $pdo->prepare("
                            INSERT INTO user (login, password, username, role, phone, address, specialization)
                            VALUES (?, ?, ?, 'vet', ?, '', ?)
                        ");
                        $stmt->execute([$login, $hashedPassword, $username, $phone, $specialization]);
                        $message = "✅ Ветеринар успешно добавлен!";
                    }
                } catch (Exception $e) {
                    $message = "❌ Ошибка: " . htmlspecialchars($e->getMessage());
                }
            }
        } else {
            $message = "⚠️ Заполните все поля!";
        }
    }

    // Редактирование
    if (isset($_POST['edit_vet'])) {
        $vet_id = intval($_POST['vet_id']);
        $username = trim($_POST['username'] ?? '');
        $login = trim($_POST['login'] ?? '');
        $phone = preg_replace('/\D/', '', trim($_POST['phone'] ?? '')); // только цифры
        $specialization = trim($_POST['specialization'] ?? '');

        if ($username && $login && $phone && $specialization) {
            if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
                $message = "⚠️ Логин должен быть адресом электронной почты.";
            } elseif (!preg_match('/^\d{10}$/', $phone)) {
                $message = "⚠️ Неверный формат номера. Введите 10 цифр без +7.";
            } else {
                $phone = '+7' . $phone; // добавляем +7 перед сохранением
                $stmt = $pdo->prepare("
                    UPDATE user
                    SET username = ?, login = ?, phone = ?, specialization = ?
                    WHERE user_id = ? AND role = 'vet'
                ");
                $stmt->execute([$username, $login, $phone, $specialization, $vet_id]);
                $message = "✅ Ветеринар успешно обновлён!";
            }
        } else {
            $message = "⚠️ Заполните все поля для редактирования!";
        }
    }
}

// Удаление
if (isset($_GET['delete'])) {
    $vet_id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM user WHERE user_id = ? AND role = 'vet'");
    $stmt->execute([$vet_id]);
    $message = "🗑️ Ветеринар удалён.";
}

// Получаем всех ветеринаров
$vets = $pdo->query("SELECT user_id, login, username, phone, specialization FROM user WHERE role='vet' ORDER BY user_id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Управление ветеринарами</title>
<style>
body { 
    font-family: Arial, sans-serif; 
    background: #f7f7f7; 
    margin: 0; 
    padding: 0; 
}
header { 
    background-color: #FFC0CB; 
    color: white; 
    padding: 15px 0; 
    text-align: center; 
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
header h1 {
    margin: 0; 
    font-size: 24px;
}
nav { 
    margin-top: 10px; 
}
nav a { 
    color: white; 
    text-decoration: none; 
    margin: 0 10px; 
    font-weight: bold; 
    transition: opacity 0.2s; 
}
nav a:hover { 
    opacity: 0.8; 
}
.container { 
    max-width: 1000px; 
    margin: 30px auto; 
    background: white; 
    padding: 30px; 
    border-radius: 10px; 
    box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
}
form { 
    margin-bottom: 30px; 
}
label { 
    display:block; 
    margin:10px 0 5px; 
    font-weight: bold;
}
input, select { 
    width: 95%; 
    padding: 8px; 
    box-sizing: border-box; 
    margin-bottom: 10px;
}
table { 
    width:100%; 
    border-collapse: collapse; 
    margin-top:20px; 
}
table th, table td { 
    border:1px solid #ccc; 
    padding:8px; 
    text-align:left; 
}
table th { 
    background:#f0f0f0; 
}
.button { 
    padding:6px 12px; 
    border:none; 
    border-radius:5px; 
    cursor:pointer; 
    color:white; 
    text-decoration:none; 
    display:inline-block; 
    margin:2px 2px 2px 0; 
    font-size: 14px;
}
.add { background:#4CAF50; }
.edit { background:#2196F3; }
.delete { background:#f44336; }
.button:hover { opacity:0.9; }
.message { 
    margin:15px 0; 
    color:green; 
    font-weight:bold; 
}
</style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
<h2>Управление ветеринарами</h2>
<p>Привет, <?= htmlspecialchars($admin['username']) ?> | <a href="logout.php">Выход</a></p>

<?php if ($message): ?>
<p class="message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<h3>Добавить нового ветеринара</h3>
<form method="post">
<input type="hidden" name="add_vet" value="1">
<label>Имя:</label><input type="text" name="username" required>
<label>Специализация:</label><input type="text" name="specialization" required>
<label>Логин (почта):</label><input type="email" name="login" required>
<label>Телефон (10 цифр без +7):</label><input type="text" name="phone" required>
<label>Пароль:</label><input type="password" name="password" required>
<button type="submit" class="button add">Добавить</button>
</form>

<h3>Список ветеринаров</h3>
<table>
<tr>
<th>ID</th><th>Имя</th><th>Специализация</th><th>Логин</th><th>Телефон</th><th>Действие</th>
</tr>
<?php foreach($vets as $vet): ?>
<tr>
<form method="post">
<td><?= $vet['user_id'] ?><input type="hidden" name="vet_id" value="<?= $vet['user_id'] ?>"></td>
<td><input type="text" name="username" value="<?= htmlspecialchars($vet['username']) ?>"></td>
<td><input type="text" name="specialization" value="<?= htmlspecialchars($vet['specialization']) ?>"></td>
<td><input type="email" name="login" value="<?= htmlspecialchars($vet['login']) ?>"></td>
<td><input type="text" name="phone" value="<?= htmlspecialchars(substr($vet['phone'],2)) ?>"></td>
<td>
<button type="submit" name="edit_vet" class="button edit">Сохранить</button>
<a href="?delete=<?= $vet['user_id'] ?>" class="button delete" onclick="return confirm('Удалить ветеринара?')">Удалить</a>
</td>
</form>
</tr>
<?php endforeach; ?>
</table>
</div>
</body>
</html>