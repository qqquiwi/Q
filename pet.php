<?php
require 'config.php';
session_start();

// Проверка авторизации владельца
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$owner_id = $_SESSION['user']['user_id']; // заменяем $_SESSION['owner']['owner_id']
$message = '';

// Добавление нового питомца
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $species = trim($_POST['species']);
    $breed = trim($_POST['breed']);
    $weight = trim($_POST['weight']);
    $gender = $_POST['gender'];

    if ($name && $species && $breed && $weight && $gender) {
        $stmt = $pdo->prepare("INSERT INTO pet (name, species, breed, weight, gender, owner_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $species, $breed, $weight, $gender, $owner_id]);
        $message = "✅ Питомец <strong>" . htmlspecialchars($name) . "</strong> добавлен успешно!";
    } else {
        $message = "❌ Пожалуйста, заполните все поля.";
    }
}

// Получаем список питомцев владельца
$stmt = $pdo->prepare("SELECT * FROM pet WHERE owner_id = ?");
$stmt->execute([$owner_id]);
$pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мои питомцы</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background-color: #f9f9f9; padding: 20px; }
        .container { max-width: 800px; margin: 50px auto; background: #f5f5f5; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 25px; font-size: 28px; }
        form label { display: block; margin-top: 15px; font-weight: bold; color: #444; }
        form input, form select { width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #ccc; font-size: 16px; }
        form button { margin-top: 20px; padding: 12px; background-color: #4CAF50; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; width: 100%; }
        form button:hover { background-color: #45a049; }
        .message { margin-top: 20px; padding: 12px; background-color: #e0ffe0; border: 1px solid #4CAF50; border-radius: 8px; color: #2e7d32; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        table th, table td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        table th { background-color: #f0f0f0; }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2>Мои питомцы</h2>

    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="post">
        <label>Имя:</label>
        <input type="text" name="name" required>

        <label>Вид:</label>
        <input type="text" name="species" required>

        <label>Порода:</label>
        <input type="text" name="breed" required>

        <label>Вес (кг):</label>
        <input type="number" step="0.1" name="weight" required>

        <label>Пол:</label>
        <select name="gender" required>
            <option value="">-- Выберите пол --</option>
            <option value="М">Мужской</option>
            <option value="Ж">Женский</option>
        </select>

        <button type="submit">Добавить питомца</button>
    </form>

    <?php if ($pets): ?>
        <h3>Список питомцев:</h3>
        <table>
            <tr>
                <th>Имя</th>
                <th>Вид</th>
                <th>Порода</th>
                <th>Вес</th>
                <th>Пол</th>
            </tr>
            <?php foreach ($pets as $pet): ?>
                <tr>
                    <td><?= htmlspecialchars($pet['name']) ?></td>
                    <td><?= htmlspecialchars($pet['species']) ?></td>
                    <td><?= htmlspecialchars($pet['breed']) ?></td>
                    <td><?= htmlspecialchars($pet['weight']) ?></td>
                    <td><?= htmlspecialchars($pet['gender']) ?></td>
                </tr>
            <?php endforeach; ?>
            </table>
    <?php else: ?>
        <p>У вас пока нет добавленных питомцев.</p>
    <?php endif; ?>
</div>

</body>
</html>