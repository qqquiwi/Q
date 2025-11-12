<?php
require 'config.php';
session_start();

// Проверяем, что вошёл ветеринар
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'vet') {
    header("Location: login.php");
    exit;
}

$vet = $_SESSION['user'];
$message = "";

// === Обновление диагноза и лечения ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $diagnosis = trim($_POST['diagnosis'] ?? '');
    $treatment = trim($_POST['treatment'] ?? '');

    try {
        $stmt = $pdo->prepare("
            UPDATE appointment
            SET diagnosis = ?, treatment = ?
            WHERE appointment_id = ? AND vet_id = ?
        ");
        $stmt->execute([$diagnosis, $treatment, $appointment_id, $vet['user_id']]);
        $message = "✅ Данные по приёму обновлены успешно!";
    } catch (Exception $e) {
        $message = "❌ Ошибка при обновлении: " . htmlspecialchars($e->getMessage());
    }
}

// === Получаем записи для этого ветеринара ===
$stmt = $pdo->prepare("
    SELECT 
        a.appointment_id,
        a.appointment_date,
        a.appointment_time,
        a.diagnosis,
        a.treatment,
        p.name AS pet_name,
        p.species,
        p.breed,
        o.username AS owner_name,
        o.phone AS owner_phone
    FROM appointment a
    JOIN pet p ON a.pet_id = p.pet_id
    JOIN user o ON p.owner_id = o.user_id
    WHERE a.vet_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$stmt->execute([$vet['user_id']]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Записи ветеринара</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background-color: #f9f9f9; padding: 20px; }
        .container { max-width: 1000px; margin: 30px auto; background: #f5f5f5; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 25px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th, table td { border: 1px solid #ccc; padding: 10px; text-align: left; vertical-align: top; }
        table th { background-color: #e0e0e0; }
        textarea { width: 100%; height: 60px; border-radius: 6px; border: 1px solid #ccc; padding: 8px; }
        button { background-color: #4CAF50; color: #fff; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #45a049; }
        .message { margin-top: 15px; padding: 10px; background-color: #e0ffe0; border: 1px solid #4CAF50; border-radius: 6px; color: #2e7d32; }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2>Мои записи</h2>
    <p>Привет, <?= htmlspecialchars($vet['username']) ?> | <a href="logout.php">Выйти</a></p>

    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <?php if ($appointments): ?>
        <table>
            <tr>
                <th>Дата</th>
                <th>Время</th>
                <th>Питомец</th>
                <th>Владелец</th>
                <th>Диагноз</th>
                <th>Лечение</th>
                <th>Действие</th>
            </tr>
            <?php foreach ($appointments as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['appointment_date']) ?></td>
                    <td><?= htmlspecialchars($a['appointment_time']) ?></td>
                    <td>
                        <?= htmlspecialchars($a['pet_name']) ?><br>
                        <small><?= htmlspecialchars($a['species']) ?> — <?= htmlspecialchars($a['breed']) ?></small>
                    </td>
                    <td>
                        <?= htmlspecialchars($a['owner_name']) ?><br>
                        <small><?= htmlspecialchars($a['owner_phone']) ?></small>
                    </td>
                    <form method="post"><input type="hidden" name="appointment_id" value="<?= $a['appointment_id'] ?>">
                        <td><textarea name="diagnosis"><?= htmlspecialchars($a['diagnosis']) ?></textarea></td>
                        <td><textarea name="treatment"><?= htmlspecialchars($a['treatment']) ?></textarea></td>
                        <td><button type="submit">💾 Сохранить</button></td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Пока нет записей.</p>
    <?php endif; ?>
</div>

</body>