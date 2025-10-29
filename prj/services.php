<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Наши услуги</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .content h2 {
            text-align: center;
            font-size: 36px;
            margin-bottom: 30px;
            color: #333;
        }
        .accordion {
            background-color: #f5f5f5;
            color: #333;
            cursor: pointer;
            padding: 15px 20px;
            width: 100%;
            border: none;
            outline: none;
            text-align: left;
            font-size: 18px;
            transition: background-color 0.2s ease;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .accordion:hover {
            background-color: #e0e0e0;
        }
        .panel {
            padding: 0 20px 15px 20px;
            display: none;
            background-color: #fafafa;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .panel ul {
            list-style: none;
            padding-left: 0;
            margin: 10px 0;
        }
        .panel li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            font-size: 16px;
            color: #555;
        }
        .panel li button {
            padding: 5px 12px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .panel li button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="content">
    <h2>Наши услуги</h2>

    <!-- Аккордеон с услугами -->
    <button class="accordion">Ветеринарные процедуры</button>
    <div class="panel">
        <ul>
            <li>
                Вакцинация
                <!-- Пример кнопки -->
<button onclick="checkLogin('Вакцинация')">Записаться</button>

<script>
function checkLogin(service) {
    <?php if(isset($_SESSION['owner'])): ?>
        // Пользователь вошел — переход на страницу записи
        window.location.href = 'appointment.php?service=' + encodeURIComponent(service);
    <?php else: ?>
        // Не вошел — переходим на login.php и потом на запись
        window.location.href = 'login.php?redirect=appointment.php?service=' + encodeURIComponent(service);
    <?php endif; ?>
}
</script>
            </li>
            <li>
                Чипирование
                <button onclick="location.href='appointment.php?service=Чипирование'">Записаться</button>
                
            </li>
            <li>
                Стерилизация и кастрация
                <button onclick="location.href='appointment.php?service=Стерилизация и кастрация'">Записаться</button>
            </li>
            <li>
                Остеосинтез при переломах
                <button onclick="location.href='appointment.php?service=Остеосинтез'">Записаться</button>
            </li>
            <li>
                Общие манипуляции для грызунов
                <button onclick="location.href='appointment.php?service=Манипуляции для грызунов'">Записаться</button>
            </li>
        </ul>
    </div>

    <button class="accordion">Диагностические услуги</button>
    <div class="panel">
        <ul>
            <li>
                УЗИ диагностика
                <button onclick="location.href='appointment.php?service=УЗИ диагностика'">Записаться</button>
            </li>
            <li>
                ЭКГ
                <button onclick="location.href='appointment.php?service=ЭКГ'">Записаться</button>
            </li>
            <li>
                Измерение давления
                <button onclick="location.href='appointment.php?service=Измерение давления'">Записаться</button>
            </li>
            <li>
                Лабораторные анализы
                <button onclick="location.href='appointment.php?service=Лабораторные анализы'">Записаться</button>
            </li>
        </ul>
    </div>

    <!-- Можно добавить другие категории аналогично -->
</div>

<script>
    const acc = document.getElementsByClassName("accordion");
    for (let i = 0; i < acc.length; i++) {acc[i].addEventListener("click", function() {
            this.classList.toggle("active");
            const panel = this.nextElementSibling;
            if (panel.style.display === "block") {
                panel.style.display = "none";
            } else {
                panel.style.display = "block";
            }
        });
    }
</script>

</body>
</html>