<?php
session_start(); // Запускаем сессию

// Если пользователь не авторизован, перенаправляем на страницу входа
if (!isset($_SESSION['user'])) {
    header("Location: login_form.php");
    exit();
}

include('db.php'); // Подключаем базу данных

// Получаем данные пользователя из сессии
$user = $_SESSION['user'];

// Функция для отображения статуса бронирования
function getUserStatusLabel($status) {
    // Если значение NULL или пустое
    if ($status === null || $status === '') {
        return '<span style="color: gray;">Не указан</span>';
    }

    // Проверяем, что $status является числом
    if (!is_numeric($status)) {
        return '<span style="color: red;">Ошибка: Некорректный тип</span>';
    }

    // Приводим к целому числу
    $status = (int)$status;

    switch ($status):
        case -1: return '<span style="color: red;">Отменено</span>';
        case 0:  return '<span style="color: orange;">На ожидании</span>';
        case 1:  return '<span style="color: green;">Подтверждено</span>';
        default: return '<span style="color: yellow;">Неизвестный статус (' . htmlspecialchars((string)$status) . ')</span>';
    endswitch;
}

// Получаем список бронирований пользователя из базы данных
try {
    $stmt = $conn->prepare("
        SELECT o.id, o.number_of_passengers, o.status, o.created_at, 
               r.departure_city, r.arrival_city
        FROM orders o
        JOIN routes r ON o.route_id = r.id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$user['id']]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
    $bookings = []; // В случае ошибки используем пустой массив
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - Springool</title>
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

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        h2, h3 {
            margin-bottom: 1rem;
        }

        p {
            margin-bottom: 0.5rem;
        }

        .booking-list {
            list-style: none;
            margin-top: 1rem;
        }

        .booking-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .booking-details {
            flex-grow: 1;
        }
        /* Стили для таблицы */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table th,
table td {
    padding: 10px;
    border: 1px solid #444;
    text-align: left;
}

table th {
    background-color: #333;
    font-weight: bold;
    color: white;
}

/* Стили для столбца "Действия" */
td.actions {
    display: flex;
    gap: 10px; /* Отступ между кнопками */
}

/* Стили для кнопок */
.btn {
    display: inline-block;
    padding: 6px 12px;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); /* Тень */
}

.btn-success {
    background-color: #28a745;
}

.btn-danger {
    background-color: #dc3545;
}

/* Hover эффекты */
.btn:hover {
    opacity: 0.9;
}

.btn-success:hover {
    background-color: #218868;
}

.btn-danger:hover {
    background-color: #c82333;
}   

        .cancel-btn {
            background: var(--accent);
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .cancel-btn:hover {
            transform: scale(1.05);
        }

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
    <div class="header">
        <ul class="nav">
            <li><a href="index.php" class="nav-link <?= ($_SERVER['PHP_SELF'] == '/index.php') ? 'active' : '' ?>">Главная</a></li>
            <li><a href="routes.php" class="nav-link <?= ($_SERVER['PHP_SELF'] == '/routes.php') ? 'active' : '' ?>">Расписание</a></li>
            <li><a href="account.php" class="nav-link <?= ($_SERVER['PHP_SELF'] == '/account.php') ? 'active' : '' ?>">Личный кабинет</a></li>
            <li><a href="logout.php" class="nav-link">Выйти</a></li>
        </ul>
    </div>

    <div class="container">
        <h2>Личный кабинет</h2>
        <p>Имя: <?= htmlspecialchars($user['fullname']) ?></p>
        <p>Логин: <?= htmlspecialchars($user['login']) ?></p>

        <h3>Мои бронирования</h3>
        <?php if (!empty($bookings)): ?>
            <ul class="booking-list">
            <?php if (!empty($bookings)): ?>
                <ul class="booking-list">
                <?php foreach ($bookings as $booking): ?>
            <li class="booking-item">
                <div class="booking-details">
                    <strong>Откуда:</strong> <?= htmlspecialchars($booking['departure_city']) ?> →
                    <strong>Куда:</strong> <?= htmlspecialchars($booking['arrival_city']) ?><br>
                    <strong>Пассажиров:</strong> <?= htmlspecialchars($booking['number_of_passengers']) ?><br>
                    <strong>Дата:</strong> <?= htmlspecialchars($booking['created_at']) ?><br>
                    <strong>Статус:</strong> <?= getUserStatusLabel($booking['status']) ?>
                </div>
                <?php if ($booking['status'] == 0): ?>
                    <form action="cancel_booking.php" method="POST" style="display: inline;">
                        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                        <button type="submit" class="cancel-btn">Отменить</button>
                    </form>
                <?php else: ?>
                    <small><?= $booking['status'] == 1 ? 'Подтверждено' : 'Отменено' ?></small>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>У вас пока нет бронирований.</p>
<?php endif; ?>
            </ul>
        <?php else: ?>
            <p>У вас пока нет бронирований.</p>
        <?php endif; ?>
    </div>

    <div class="footer">
        <small>© 2025 Springool. Все права защищены.</small>
    </div>
</body>
</html>