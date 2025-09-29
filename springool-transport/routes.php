<?php
session_start(); // Запускаем сессию
// Подключаем конфигурацию базы данных
require_once 'config.php';

// Обработка параметров поиска
$search_params = [];
$where = [];
if (!empty($_GET['from'])) {
    $where[] = "departure_city LIKE ?";
    $search_params[] = '%' . $_GET['from'] . '%';
}
if (!empty($_GET['to'])) {
    $where[] = "arrival_city LIKE ?";
    $search_params[] = '%' . $_GET['to'] . '%';
}

// Добавляем условие для фильтрации маршрутов с 0 доступными местами
$where[] = "available_seats > 0";

// Формируем SQL-запрос
$query = "SELECT * FROM routes";
if (!empty($where)) {
    $query .= " WHERE " . implode(' AND ', $where);
}

// Выполняем запрос
$stmt = $pdo->prepare($query);
$stmt->execute($search_params);
$routes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Springool - Расписание рейсов</title>
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

        /* Основной контент */
        .content-section {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        .section-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--accent);
            padding-bottom: 0.5rem;
        }

        /* Форма поиска */
        .search-form {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* Три колонки для "Откуда", "Куда" и "Цена" */
            gap: 1rem; /* Расстояние между элементами */
            width: 100%; /* Занимает всю ширину контейнера */
        }

        .search-form input[type="text"],
        .search-form input[type="number"] {
            padding: 0.8rem;
            background: transparent;
            border: none;
            border-radius: 4px;
            color: var(--text-color);
            width: 100%; /* Занимает всю ширину ячейки */
        }

        .search-form button {
            background: var(--accent);
            color: white;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.3s;
        }

        .search-form button:hover {
            opacity: 0.8;
        }

        /* Таблица расписания */
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }

        .schedule-table th,
        .schedule-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .schedule-table th {
            background: rgba(255, 255, 255, 0.05);
            font-weight: bold;
        }

        .schedule-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: scale(1.01);
            transition: transform 0.2s;
        }

        .book-btn {
            background: var(--accent);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 0.5rem;
            cursor: pointer;
        }

        /* Алерты */
        .alert {
            padding: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .alert.error {
            background: rgba(255, 0, 0, 0.1);
        }

        /* Футер */
        .footer {
            background: var(--dark-bg);
            padding: 1rem 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            border-top: 1px solid var(--accent);
        }
    </style>
</head>
<body>
    <!-- Шапка -->
    <div class="header">
        <ul class="nav">
            <li><a href="index.php" class="nav-link">Главная</a></li>
            <li><a href="routes.php" class="nav-link active">Расписание</a></li>
            <?php if (isset($_SESSION['user'])): ?>
                <!-- Для авторизованных пользователей -->
                <li><a href="account.php" class="nav-link">Личный кабинет</a></li>
                <li><a href="logout.php" class="nav-link">Выйти</a></li>
            <?php else: ?>
                <!-- Для гостей -->
                <li><a href="register_form.php" class="nav-link">Регистрация</a></li>
                <li><a href="login_form.php" class="nav-link">Вход</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Форма поиска -->
    <div class="content-section">
        <h2 class="section-title">Поиск рейсов</h2>
        <form class="search-form" action="routes.php" method="GET">
            <input type="text" name="from" placeholder="Откуда" 
                value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">
            <input type="text" name="to" placeholder="Куда" 
                value="<?= htmlspecialchars($_GET['to'] ?? '') ?>">
            <button type="submit" class="btn">Найти</button>
        </form>
    </div>

    <!-- Результаты поиска -->
    <div class="content-section">
        <h2 class="section-title">Расписание рейсов</h2>
        <?php if(empty($routes)): ?>
            <div class="alert error">
                Нет доступных рейсов по заданным параметрам
            </div>
        <?php else: ?>
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Направление</th>
                        <th>Время отправления</th>
                        <th>Время прибытия</th>
                        <th>Цена</th>
                        <th>Доступно мест</th>
                        <th>Количество пассажиров</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($routes as $route): ?>
                    <tr>
                        <td><?= htmlspecialchars("{$route['departure_city']} → {$route['arrival_city']}") ?></td>
                        <td><?= htmlspecialchars($route['departure_time']) ?></td>
                        <td><?= htmlspecialchars($route['arrival_time']) ?></td>
                        <td><?= number_format($route['price'], 0, '.', ' ') ?> ₽</td>
                        <td><?= htmlspecialchars($route['available_seats']) ?> мест</td>
                        <td>
                            <!-- Поле для выбора количества пассажиров -->
                            <input type="number" min="1" max="<?= $route['available_seats'] ?>" value="1" class="passenger-input">
                        </td>
                        <td>
                            <!-- Кнопка "Выбрать" -->
                            <a href="#" class="book-btn" data-route-id="<?= $route['id'] ?>">Выбрать</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="footer">
        <small>© 2025 Springool. Все права защищены.</small>
    </div>

    <!-- JavaScript для обработки бронирования -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Находим все кнопки "Выбрать"
        const bookButtons = document.querySelectorAll('.book-btn');
        bookButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault(); // Отменяем стандартное действие ссылки
                // Получаем ID маршрута из атрибута data-route-id
                const routeId = this.getAttribute('data-route-id');
                // Находим поле для выбора количества пассажиров
                const passengerInput = this.closest('tr').querySelector('.passenger-input');
                const numberOfPassengers = passengerInput.value;
                // Перенаправляем пользователя на booking.php с параметрами
                window.location.href = `booking.php?route_id=${routeId}&passengers=${numberOfPassengers}`;
            });
        });
    });
    </script>
</body>
</html>