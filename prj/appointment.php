<?php
require 'config.php';
session_start();

// === Универсальное определение авторизованного пользователя ===
$current_user = null;

// Поддержка новой и старой схем сессий
if (isset($_SESSION['user'])) {
    $current_user = $_SESSION['user'];
} elseif (isset($_SESSION['owner'])) {
    $current_user = $_SESSION['owner'];
}

// Проверка, что пользователь вошёл и является владельцем
if (!$current_user) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Определяем роль (новая — role, старая — считаем owner)
$role = $current_user['role'] ?? 'owner';

if ($role !== 'owner') {
    header("Location: index.php");
    exit;
}

$owner_id = $current_user['user_id'] ?? $current_user['owner_id'];
$service = $_GET['service'] ?? '';
$message = '';

// === Получаем питомцев ===
$stmt = $pdo->prepare("SELECT pet_id, name FROM pet WHERE owner_id = ?");
$stmt->execute([$owner_id]);
$pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// === Обработка формы ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pet_id = $_POST['pet_id'];
    $vet_id = $_POST['vet_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];

    $stmt = $pdo->prepare("
        INSERT INTO appointments (appointment_date, appointment_time, pet_id, vet_id, diagnosis, treatment)
        VALUES (?, ?, ?, ?, '', '')
    ");
    $stmt->execute([$appointment_date, $appointment_time, $pet_id, $vet_id]);

    $pet_name = '';
    foreach ($pets as $p) {
        if ($p['pet_id'] == $pet_id) {
            $pet_name = $p['name'];
            break;
        }
    }

    $message = "✅ Вы успешно записаны на услугу <strong>" . htmlspecialchars($service) . "</strong> 
                для питомца <strong>" . htmlspecialchars($pet_name) . "</strong> 
                на " . htmlspecialchars($appointment_date) . " в " . htmlspecialchars($appointment_time) . ".";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Запись на услугу</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2>Запись на услугу <?= htmlspecialchars($service) ?></h2>

    <?php if ($message): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="service" value="<?= htmlspecialchars($service) ?>">

        <label>Выберите питомца:</label>
        <select name="pet_id" required>
            <option value="">-- Выберите питомца --</option>
            <?php foreach ($pets as $pet): ?>
                <option value="<?= $pet['pet_id'] ?>"><?= htmlspecialchars($pet['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Выберите ветеринара:</label>
        <select name="vet_id" required>
            <option value="">-- Выберите врача --</option>
            <?php
            // Получаем всех ветеринаров
            $vets = $pdo->query("SELECT user_id, fullname FROM user WHERE role = 'vet'")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($vets as $vet):
            ?>
                <option value="<?= $vet['user_id'] ?>"><?= htmlspecialchars($vet['fullname']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Дата записи:</label>
        <input type="date" name="appointment_date" required>

        <label>Время записи:</label>
        <input type="time" name="appointment_time" required>

        <button type="submit">Записаться</button>
    </form>
</div>

</body>
</html>