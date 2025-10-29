<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <h1>Алиска сосиска</h1>
    <nav>
        <a href="index.php">Главная</a>
        <a href="services.php">Наши услуги</a>

        <?php if (isset($_SESSION['user'])): ?>
            <?php if ($_SESSION['user']['role'] === 'owner'): ?>
                <!-- Меню владельца -->
                <a href="pet.php">Мои питомцы</a>
                <a href="appointments.php">Мои записи</a>
                <a href="logout.php">Выход</a>

            <?php elseif ($_SESSION['user']['role'] === 'vet'): ?>
                <!-- Меню ветеринара -->
                <a href="appointments.php">Записи</a>
                <a href="patients.php">Пациенты</a>
                <a href="logout.php">Выход</a>

            <?php elseif ($_SESSION['user']['role'] === 'admin'): ?>
                <!-- Меню администратора -->
                <a href="admin_dashboard.php">Панель</a>
                <a href="admin_vets.php">Ветеринары</a>
                <a href="admin_owners.php">Владельцы и питомцы</a>
                <a href="admin_stats.php">Статистика</a>
                <a href="logout.php">Выход</a>

            <?php endif; ?>
        <?php else: ?>
            <!-- Меню гостя -->
            <a href="register.php">Регистрация</a>
            <a href="login.php">Вход</a>
        <?php endif; ?>
    </nav>
</header>