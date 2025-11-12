<?php
require 'config.php';
session_start();

// Проверка авторизации владельца
$current_user = $_SESSION['user'] ?? null;
if (!$current_user || $current_user['role'] !== 'owner') {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$owner_id = $current_user['user_id'];
$service = $_GET['service'] ?? '';
$message = '';

// Получаем питомцев владельца
$stmt = $pdo->prepare("SELECT pet_id, name FROM pet WHERE owner_id = ?");
$stmt->execute([$owner_id]);
$pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем всех ветеринаров
$vets = $pdo->query("SELECT user_id, username FROM user WHERE role='vet'")->fetchAll(PDO::FETCH_ASSOC);

// Обработка формы записи
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pet_id = $_POST['pet_id'] ?? '';
    $vet_id = $_POST['vet_id'] ?? '';
    $appointment_date = $_POST['appointment_date'] ?? '';
    $appointment_time = $_POST['appointment_time'] ?? '';

    // Проверка выбранного питомца
    $valid_pet = false;
    foreach ($pets as $p) {
        if ($p['pet_id'] == $pet_id) {
            $valid_pet = true;
            $pet_name = $p['name'];
            break;
        }
    }

    // Проверка выбранного ветеринара
    $valid_vet = false;
    foreach ($vets as $v) {
        if ($v['user_id'] == $vet_id) {
            $valid_vet = true;
            $vet_name = $v['username'];
            break;
        }
    }

    // Вставка записи
    if ($valid_pet && $valid_vet && $appointment_date && $appointment_time) {
        $stmt = $pdo->prepare("
            INSERT INTO appointment (appointment_date, appointment_time, pet_id, vet_id, diagnosis, treatment)
            VALUES (?, ?, ?, ?, '', '')
        ");
        $stmt->execute([$appointment_date, $appointment_time, $pet_id, $vet_id]);

        $message = "✅ Вы успешно записаны на услугу <strong>" . htmlspecialchars($service) . "</strong> 
                    для питомца <strong>" . htmlspecialchars($pet_name) . "</strong> 
                    на " . htmlspecialchars($appointment_date) . " в " . htmlspecialchars($appointment_time) . " к ветеринару <strong>" . htmlspecialchars($vet_name) . "</strong>.";
    } else {
        $message = "❌ Пожалуйста, выберите питомца, ветеринара и корректную дату/время.";
    }
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
            <?php foreach ($vets as $vet): ?>
                <option value="<?= $vet['user_id'] ?>"><?= htmlspecialchars($vet['username']) ?></option>
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