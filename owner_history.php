<?php
require 'config.php';
session_start();

// === Проверяем авторизацию владельца ===
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$owner = $_SESSION['user'];
$owner_id = $owner['user_id'];

// === Получаем все приёмы ===
$stmt = $pdo->prepare("
    SELECT 
        a.appointment_id,
        a.appointment_date,
        a.appointment_time,
        p.name AS pet_name,
        u.username AS vet_name,
        a.diagnosis,
        a.treatment
    FROM appointment a
    JOIN pet p ON a.pet_id = p.pet_id
    JOIN user u ON a.vet_id = u.user_id
    WHERE p.owner_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$stmt->execute([$owner_id]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Разделяем приёмы на будущие и прошедшие
$future_appointments = [];
$past_appointments = [];

$now = new DateTime(); // текущее время
foreach ($appointments as $a) {
    $dateTime = new DateTime($a['appointment_date'] . ' ' . $a['appointment_time']);
    
    if ($dateTime >= $now) {
        $future_appointments[] = $a;
    } else {
        $past_appointments[] = $a;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мои приёмы и история</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }
        .container {
            max-width: 950px;
            margin: 40px auto;
            background: #fff;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        h3 {
            margin-top: 40px;
            color: #2a5d84;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f0f0f0;
        }
        .empty {
            text-align: center;
            color: #777;
            font-style: italic;
            margin-top: 10px;
        }
        .done {
            background-color: #e8f5e9;
        }
        .upcoming {
            background-color: #fffde7;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2>Мои записи и история приёмов</h2>
    <p>Здравствуйте, <strong><?= htmlspecialchars($owner['username']) ?></strong>!</p>

    <h3>📅 Предстоящие приёмы</h3>
    <?php if ($future_appointments): ?>
        <table>
            <tr>
                <th>Дата</th>
                <th>Время</th>
                <th>Питомец</th>
                <th>Ветеринар</th>
                <th>Статус</th>
            </tr>
            <?php foreach ($future_appointments as $a): ?>
                <tr class="upcoming">
                    <td><?= htmlspecialchars($a['appointment_date']) ?></td>
                    <td><?= (new DateTime($a['appointment_time']))->format('H:i') ?></td>
                    <td><?= htmlspecialchars($a['pet_name']) ?></td>
                    <td><?= htmlspecialchars($a['vet_name']) ?></td>
                    <td>Ожидается</td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p class="empty">Нет запланированных приёмов.</p>
    <?php endif; ?>

    <h3>📖 История приёмов</h3>
    <?php if ($past_appointments): ?>
        <table>
            <tr>
                <th>Дата</th>
                <th>Время</th>
                <th>Питомец</th>
                <th>Ветеринар</th>
                <th>Назначение</th>
            </tr>
            <?php foreach ($past_appointments as $a): ?>
                <tr class="done">
                    <td><?= htmlspecialchars($a['appointment_date']) ?></td>
                    <td><?= (new DateTime($a['appointment_time']))->format('H:i') ?></td>
                    <td><?= htmlspecialchars($a['pet_name']) ?></td>
                    <td><?= htmlspecialchars($a['vet_name']) ?></td>
                    <td><?= $a['treatment'] ? htmlspecialchars($a['treatment']) : '—' ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p class="empty">История приёмов пока пуста.</p>
    <?php endif; ?>
</div>

</body>
</html>
