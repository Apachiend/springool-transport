<?php
session_start();

// Проверка авторизации и роли администратора
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 1) {
    header("Location: login_form.php");
    exit;
}

include('db.php'); // Подключаем базу данных

// Получение количества пользователей
try {
    $stmt_users_count = $conn->prepare("SELECT COUNT(*) as total_users FROM users");
    $stmt_users_count->execute();
    $users_count = $stmt_users_count->fetch(PDO::FETCH_ASSOC)['total_users'];
} catch (PDOException $e) {
    error_log("Ошибка при получении количества пользователей: " . $e->getMessage());
    $users_count = 0;
}

// Получение количества маршрутов
try {
    $stmt_routes_count = $conn->prepare("SELECT COUNT(*) as total_routes FROM routes");
    $stmt_routes_count->execute();
    $routes_count = $stmt_routes_count->fetch(PDO::FETCH_ASSOC)['total_routes'];
} catch (PDOException $e) {
    error_log("Ошибка при получении количества маршрутов: " . $e->getMessage());
    $routes_count = 0;
}

// Получение списка всех бронирований
try {
    $stmt_orders = $conn->prepare("
        SELECT 
            o.id, 
            u.fullname AS user_name, 
            r.departure_city, 
            r.arrival_city, 
            o.status, 
            o.created_at,
            o.number_of_passengers
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN routes r ON o.route_id = r.id
    ");
    $stmt_orders->execute();
    $orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Ошибка при получении бронирований: " . $e->getMessage());
    $orders = [];
}

// Получение списка всех пользователей с числом их бронирований
try {
    $stmt_users = $conn->prepare("
        SELECT 
            u.id,
            u.fullname,
            u.login,
            u.role,
            COUNT(o.id) AS total_orders
        FROM 
            users u
        LEFT JOIN 
            orders o ON u.id = o.user_id
        GROUP BY 
            u.id, u.fullname, u.login, u.role
    ");
    $stmt_users->execute();
    $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Ошибка при получении пользователей: " . $e->getMessage());
    $users = [];
}

// Получение списка всех маршрутов
try {
    $stmt_routes = $conn->prepare('SELECT * FROM routes');
    $stmt_routes->execute();
    $routes = $stmt_routes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Ошибка при получении маршрутов: " . $e->getMessage());
    $routes = [];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора - Springool</title>
    <style>
        :root {
            --primary: #2c4f9f;
            --accent: #ff3333; /* Цвет акцента (красный) */
            --dark-bg: #1a1a1a; /* Тёмный фон */
            --text-color: #fff; /* Цвет текста */
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
            display: flex;
            flex-direction: column;
        }

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

        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        .nav-links li a {
            color: #fff;
            text-decoration: none;
            padding: 10px;
            transition: all 0.3s ease;
        }

        .nav-links li a.active {
            color: red;
            border-bottom: 2px solid red;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        h1, h2 {
            margin-bottom: 1rem;
        }

            /* Улучшенный стиль для таблицы */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 10px;
        border: 1px solid #444;
        text-align: left;
    }

    th {
        background-color: #333;
        font-weight: bold;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #222; /* Тёмно-серый фон для чётных строк */
    }

    .btn {
        display: inline-block;
        padding: 6px 12px;
        margin-right: 5px;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-size: 14px;
        transition: opacity 0.3s ease;
    }

    .btn-success { background-color: #28a745; }
    .btn-warning { background-color: #ffc107; }
    .btn-danger { background-color: #dc3545; }

    .btn:hover {
        opacity: 0.9;
    }

        .no-data {
            text-align: center;
            color: #666;
            margin-top: 20px;
        }

        .footer {
            background: var(--dark-bg);
            padding: 1rem 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            border-top: 1px solid var(--accent);
        }

        /* Стиль для столбца "Действия" */
        td.actions {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>

<!-- Шапка -->
<header class="header">
    <nav>
        <ul class="nav-links">
            <li><a href="index.php">Главная</a></li>
            <li><a href="routes.php">Расписание</a></li>
            <li><a href="account.php">Личный кабинет</a></li>
            <li><a href="#" class="active">Панель администратора</a></li>
            <li><a href="logout.php">Выйти</a></li>
        </ul>
    </nav>
</header>

<!-- Основной контент -->
<div class="container">
    <h1>Панель администратора</h1>
    <p>Пользователи: <?= htmlspecialchars($users_count) ?> | Маршруты: <?= htmlspecialchars($routes_count) ?></p>

    <!-- БРОНИРОВАНИЯ -->
    <h2>Список бронирований</h2>
    <?php if (!empty($orders)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Пользователь</th>
                    <th>Маршрут</th>
                    <th>Статус</th>
                    <th>Дата создания</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['id']) ?></td>
                        <td><?= htmlspecialchars($order['user_name']) ?></td>
                        <td><?= htmlspecialchars($order['departure_city']) ?> → <?= htmlspecialchars($order['arrival_city']) ?></td>
                        <td><?= htmlspecialchars($order['status']) ?></td>
                        <td><?= htmlspecialchars($order['created_at']) ?></td>
                        <td class="actions">
                            <a href="admin_actions.php?action=update_order_status&id=<?= $order['id'] ?>&status=1" class="btn btn-success">Подтвердить</a>
                            <a href="admin_actions.php?action=update_order_status&id=<?= $order['id'] ?>&status=0" class="btn btn-warning">Ожидание</a>
                            <a href="admin_actions.php?action=update_order_status&id=<?= $order['id'] ?>&status=-1" class="btn btn-danger">Отменить</a>
                            <a href="admin_actions.php?action=delete_order&id=<?= $order['id'] ?>" class="btn btn-danger" onclick="return confirm('Вы уверены?')">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-data">Нет бронирований.</p>
    <?php endif; ?>

    <!-- ПОЛЬЗОВАТЕЛИ -->
    <h2>Список пользователей</h2>
    <a href="admin_actions.php?action=add_user" class="btn btn-success">Добавить пользователя</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Имя</th>
                <th>Логин</th>
                <th>Роль</th>
                <th>Количество бронирований</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['fullname']) ?></td>
                        <td><?= htmlspecialchars($user['login']) ?></td>
                        <td><?= $user['role'] === 1 ? 'Администратор' : 'Пользователь' ?></td>
                        <td><?= htmlspecialchars($user['total_orders']) ?></td>
                        <td class="actions">
                            <a href="admin_actions.php?action=edit_user&id=<?= $user['id'] ?>" class="btn btn-success">Редактировать</a>
                            <a href="admin_actions.php?action=delete_user&id=<?= $user['id'] ?>" class="btn btn-danger" onclick="return confirm('Вы уверены?')">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">Нет пользователей.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- МАРШРУТЫ -->
    <h2>Список маршрутов</h2>
    <a href="admin_actions.php?action=add_route" class="btn btn-success">Добавить маршрут</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Откуда</th>
                <th>Куда</th>
                <th>Доступные места</th>
                <th>Цена</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($routes)): ?>
                <?php foreach ($routes as $route): ?>
                    <tr>
                        <td><?= htmlspecialchars($route['id']) ?></td>
                        <td><?= htmlspecialchars($route['departure_city']) ?></td>
                        <td><?= htmlspecialchars($route['arrival_city']) ?></td>
                        <td><?= htmlspecialchars($route['available_seats']) ?></td>
                        <td><?= htmlspecialchars($route['price']) ?> руб.</td>
                        <td class="actions">
                            <a href="admin_actions.php?action=edit_route&id=<?= $route['id'] ?>" class="btn btn-success">Редактировать</a>
                            <a href="admin_actions.php?action=delete_route&id=<?= $route['id'] ?>" class="btn btn-danger" onclick="return confirm('Вы уверены?')">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">Нет маршрутов.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Подвал -->
<footer class="footer">
    <p>© 2025 Springool. Все права защищены.</p>
</footer>

</body>
</html>