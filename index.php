<?php
session_start();
?>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>
<!-- Блок "Услуги" -->
<div style="
    width: 80%;
    max-width: 900px;
    margin: 30px auto 50px auto; /* сверху 30px, снизу 50px, по центру */
    padding: 20px;
    background-color: #f5f5f5; /* светлый фон */
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    font-family: Arial, Helvetica, sans-serif;
    text-align: left;
">
    <h2 style="
        font-size: 32px;
        margin-bottom: 15px;
        color: #333;
    ">
        Круглосуточные ветеринарные услуги
    </h2>
    <p style="
        font-size: 18px;
        line-height: 1.6;
        color: #555;
    ">
        Мы предлагаем широкий спектр услуг для ваших питомцев:<br>
        - УЗИ диагностика<br>
        - Измерение давления<br>
        - ЭКГ<br>
        - Вакцинация и чипирование<br>
        - Стерилизация и кастрация<br>
        - Проведение остеосинтеза при различных переломах<br>
        - Проведение различных манипуляций для грызунов с использованием газового наркоза<br>
        - И многое другое
    </p>
</div>
<!-- Блок "О нас" -->
<div style="
    width: 80%;
    max-width: 900px;
    margin: 30px auto 50px auto; /* сверху 30px, снизу 50px, по центру */
    padding: 20px;
    background-color: #f5f5f5; /* светлый фон */
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    font-family: Arial, Helvetica, sans-serif;
    text-align: left;
">
    <h2 style="
        font-size: 32px;
        margin-bottom: 15px;
        color: #333;
    ">
        О нас
    </h2>
    <p style="
        font-size: 18px;
        line-height: 1.6;
        color: #555;
    ">
        Добро пожаловать в наш зоомагазин! 🐾<br>
        Мы предлагаем качественные товары для ваших питомцев: корм, игрушки, аксессуары и многое другое.<br>
        Наша цель — забота о ваших любимцах и их здоровье.
    </p>
</div>
<!-- Блок "Наши врачи" -->
<div style="width: 90%; max-width: 1000px; margin: 50px auto; font-family: Arial, Helvetica, sans-serif;">
    <h2 style="font-size: 32px; margin-bottom: 20px; color: #333;">Наши врачи</h2>
    
    <div style="position: relative;">
        <!-- Кнопки стрелок -->
        <button id="prev" style="
            position: absolute; left: -40px; top: 50%;
            transform: translateY(-50%);
            font-size: 24px; cursor: pointer;
        ">&#8592;</button>
        <button id="next" style="
            position: absolute; right: -40px; top: 50%;
            transform: translateY(-50%);
            font-size: 24px; cursor: pointer;
        ">&#8594;</button>

        <!-- Горизонтальный контейнер -->
        <div id="doctors-container" style="
            display: flex;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding-bottom: 10px;
        ">
            <!-- Карточки врачей -->
            <div style="
                flex: 0 0 250px;
                margin-right: 20px;
                background-color: #f5f5f5;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                padding: 20px;
                text-align: center;
            ">
                <h3 style="font-size: 20px; margin-bottom: 10px;">Анна Смирнова</h3>
                <p style="font-size:16px; color:#555;">Терапевт, стаж 8 лет</p>
            </div>

            <div style="
                flex: 0 0 250px;
                margin-right: 20px;
                background-color: #f5f5f5;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                padding: 20px;
                text-align: center;
            ">
                <h3 style="font-size: 20px; margin-bottom: 10px;">Сергей Петров</h3>
                <p style="font-size:16px; color:#555;">Хирург, стаж 12 лет</p>
            </div>

            <div style="
                flex: 0 0 250px;
                margin-right: 20px;
                background-color: #f5f5f5;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                padding: 20px;
                text-align: center;
            ">
                <h3 style="font-size: 20px; margin-bottom: 10px;">Марина Кузнецова</h3>
                <p style="font-size:16px; color:#555;">Диагност, стаж 6 лет</p>
            </div>

            <div style="
                flex: 0 0 250px;
                margin-right: 20px;
                background-color: #f5f5f5;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                padding: 20px;
                text-align: center;
            ">
                <h3 style="font-size: 20px; margin-bottom: 10px;">Иван Волков</h3>
                <p style="font-size:16px; color:#555;">Анестезиолог, стаж 10 лет</p>
            </div>
        </div>
    </div>
</div>

<!-- JS для кнопок прокрутки -->
<script>
    const container = document.getElementById('doctors-container');
    const prevBtn = document.getElementById('prev');
    const nextBtn = document.getElementById('next');

    prevBtn.addEventListener('click', () => {
        container.scrollBy({ left: -260, behavior: 'smooth' });
    });
    nextBtn.addEventListener('click', () => {
        container.scrollBy({ left: 260, behavior: 'smooth' });
    });
</script>
</body>
</html>