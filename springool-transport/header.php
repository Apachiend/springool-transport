<?php
// header.php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Springool - Пассажирские перевозки</title>
    <style>
        :root {
            --primary: #2c4f9f;
            --accent: #ff3333;
            --dark-bg: #1a1a1a;
            --text-color: #fff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Comic Sans MS', cursive;
        }

        body {
            background: linear-gradient(45deg, #333, #111);
            min-height: 100vh;
            color: var(--text-color);
            position: relative;
        }

        /* Шапка */
        .header {
            background: var(--dark-bg);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-link {
            color: var(--text-color);
            text-decoration: none;
            position: relative;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--accent);
        }

        /* Уведомления */
        .alert {
            background: rgba(var(--accent), 0.1);
            color: var(--accent);
            padding: 1rem;
            margin: 1rem 2rem;
            border-radius: 5px;
        }

        /* Основной контент */
        .content-section {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
        }

        /* Стили для расписания */
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .schedule-table th,
        .schedule-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .schedule-table th {
            background: rgba(255,255,255,0.05);
            font-weight: bold;
        }

        .schedule-table tr:hover {
            background: rgba(255,255,255,0.05);
            transform: scale(1.01);
            transition: transform 0.2s;
        }

        /* Фильтры */
        .filter-container {
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
        }

        .filter-container input {
            flex: 1;
            padding: 0.8rem;
            background: rgba(255,255,255,0.1);
            border: none;
            color: var(--text-color);
        }

        .filter-container button {
            padding: 0.8rem 2rem;
            background: var(--accent);
            border: none;
            color: white;
            cursor: pointer;
            transition: transform 0.2s;
        }
    </style>
</head>
<body>
    <?php if (isset($_GET['message'])): ?>
        <?php if ($_GET['message'] == 'login_success'): ?>
            <div class="alert success">
                Успешно вошли в аккаунт!
            </div>
        <?php elseif ($_GET['message'] == 'register_success'): ?>
            <div class="alert success">
                Регистрация завершена!
            </div>
        <?php elseif ($_GET['message'] == 'error'): ?>
            <div class="alert error">
                Ошибка входа. Попробуйте еще раз.
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Шапка -->
    <div class="header">
        <ul class="nav">
            <li><a href="index.php" class="nav-link">Главная</a></li>
            <li><a href="footers.php" class="nav-link">Расписание</a></li>
            <li><a href="register_form.php" class="nav-link">Регистрация</a></li>
            <li><a href="login_form.php" class="nav-link">Вход</a></li>
        </ul>
    </div>