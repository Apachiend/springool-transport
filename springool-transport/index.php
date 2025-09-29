<?php
session_start(); // Запускаем сессию
require_once 'config.php'; // Подключаем конфигурацию базы данных

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
        .nav-link.admin-link {
        color: var(--accent);
        font-weight: bold;
        }

        .nav-link.admin-link:hover {
        color: #ff6666;
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
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        .section-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--accent);
            padding-bottom: 0.5rem;
        }

        .cars-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .car-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 10px;
            transition: transform 0.3s;
            text-align: center;
        }

        .car-card:hover {
            transform: translateY(-5px);
        }

        .car-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .car-title {
            font-size: 1.2rem;
            margin: 1rem 0;
            color: var(--accent);
        }

        .car-description {
            line-height: 1.6;
        }

        .more-link {
            color: var(--accent);
            text-decoration: underline;
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

        /* Анимации */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .content-section {
            animation: fadeIn 1s ease-in-out;
        }

        /* Новые стили */
        .search-form {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr; /* Два поля ввода и кнопка */
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .search-form input,
        .search-form button {
            padding: 0.8rem;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-color);
        }

        .search-form button {
            background: var(--accent);
            color: white;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .search-form button:hover {
            transform: scale(1.05);
        }

        .routes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .route-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 10px;
            transition: transform 0.3s;
        }

        .route-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <div class="header">
        <ul class="nav">
            <li><a href="#" class="nav-link active">Главная</a></li>
            <li><a href="routes.php" class="nav-link">Расписание</a></li>
            <?php if (isset($_SESSION['user'])): ?>
                <!-- Для авторизованных пользователей -->
                <li><a href="account.php" class="nav-link">Личный кабинет</a></li>
                <?php if ($_SESSION['user']['role'] === 1): ?>
                    <!-- Для администраторов -->
                    <li><a href="admin_panel.php" class="nav-link admin-link">Панель администратора</a></li>
                <?php endif; ?>
                <li><a href="logout.php" class="nav-link">Выйти</a></li>
            <?php else: ?>
                <!-- Для гостей -->
                <li><a href="register_form.php" class="nav-link">Регистрация</a></li>
                <li><a href="login_from.php" class="nav-link">Вход</a></li>
            <?php endif; ?>
        </ul>
    </div>

<!-- Блок поиска -->
<div class="content-section">
    <h2 class="section-title">Поиск рейсов</h2>
    <form class="search-form" action="index.php" method="GET">
        <input type="text" name="from" placeholder="Откуда" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>" required>
        <input type="text" name="to" placeholder="Куда" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>" required>
        <button type="submit" class="btn">Найти билеты</button>
    </form>
</div>

<!-- Результаты поиска -->
<?php if (!empty($_GET)): ?>
    <div class="content-section">
        <h2 class="section-title">Результаты поиска</h2>
        <?php if (empty($routes)): ?>
            <div class="alert error">
                Рейсов по заданным параметрам не найдено
            </div>
        <?php else: ?>
            <div class="routes-grid">
                <?php foreach ($routes as $route): ?>
                    <div class="route-card">
                        <h3><?= htmlspecialchars("{$route['departure_city']} → {$route['arrival_city']}") ?></h3>
                        <p>Время отправления: <?= htmlspecialchars($route['departure_time']) ?></p>
                        <p>Время прибытия: <?= htmlspecialchars($route['arrival_time']) ?></p>
                        <p>Цена: <?= number_format($route['price'], 0, '.', ' ') ?> ₽</p>
                        <p>Доступно мест: <?= htmlspecialchars($route['available_seats']) ?></p>
                        <a href="booking.php?route_id=<?= $route['id'] ?>&passengers=1" class="more-link">Забронировать</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Популярные направления -->
<div class="content-section">
    <h2 class="section-title">Популярные направления</h2>
    <div class="routes-grid">
        <div class="route-card">
            <h3>Москва - Санкт-Петербург</h3>
            <p>от 1500 ₽</p>
            <a href="routes.php" class="more-link">Выбрать</a>
        </div>
        <div class="route-card">
            <h3>Казань - Екатеринбург</h3>
            <p>от 2500 ₽</p>
            <a href="routes.php" class="more-link">Выбрать</a>
        </div>
        <div class="route-card">
            <h3>Новосибирск - Красноярск</h3>
            <p>от 1800 ₽</p>
            <a href="" class="more-link">Выбрать</a>
        </div>
    </div>
</div>

<!-- О нас -->
<div class="content-section">
    <h2 class="section-title">Мы предлагаем</h2>
    <div class="cars-grid">
        <!-- Карточка машины 1 -->
        <div class="car-card">
            <img src="https://cdn.qwenlm.ai/output/e8bdd082-8f7d-441d-8905-2a7d9038c9bb/t2i/e3e3b130-ceda-4b94-8067-9edbe33e07a2/c260583a-2c0c-451c-ba6e-c64744e118f2.png?key=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJyZXNvdXJjZV91c2VyX2lkIjoiZThiZGQwODItOGY3ZC00NDFkLTg5MDUtMmE3ZDkwMzhjOWJiIiwicmVzb3VyY2VfaWQiOiJjMjYwNTgzYS0yYzBjLTQ1MWMtYmE2ZS1jNjQ3NDRlMTE4ZjIiLCJyZXNvdXJjZV9jaGF0X2lkIjpudWxsfQ.LolQQsRZ_Fmk6iizCOVQZMnGBemhm9RTqqw5G_MG-rI"
                 alt="Автобус Neoplan" class="car-image">
            <div class="car-title">Neoplan Skyliner</div>
            <div class="car-description">
                Премиальный двухэтажный автобус с:
                <ul>
                    <li>Кондиционером</li>
                    <li>Wi-Fi</li>
                    <li>Раскладывающимися креслами</li>
                    <li>Биотуалетом</li>
                </ul>
            </div>
        </div>

        <!-- Карточка машины 2 -->
        <div class="car-card">
            <img src="https://50.img.avito.st/image/1/1.CuSjP7a4pg2ViCQAu2sj1dedpAsdniQblZOkDxOWrgcV.izfKHKfQo_4OVSrK4Xnqp_Q56nal5FkGncdpjYg21mA" alt="Микроавтобус Mercedes" class="car-image">
            <div class="car-title">Mercedes Sprinter</div>
            <div class="car-description">
                Компактный микроавтобус для небольших групп:
                <ul>
                    <li>7-местный салон</li>
                    <li>Панорамные окна</li>
                    <li>Мультимедийная система</li>
                    <li>Багажное отделение</li>
                </ul>
            </div>
        </div>

        <!-- Карточка машины 3 -->
        <div class="car-card">
            <img src="https://avatars.mds.yandex.net/i?id=34ac0e3569a0598dfffb99cc7434cc2ffbe5a1e3-5660313-images-thumbs&n=13" alt="Электробус Yutong" class="car-image">
            <div class="car-title">Yutong E12</div>
            <div class="car-description">
                Экологичный электробус с:
                <ul>
                    <li>Автономностью 300 км</li>
                    <li>USB-зарядками</li>
                    <li>Низкопольной платформой</li>
                    <li>Системой климат-контроля</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Футер -->
<div class="footer">
    <small>© 2025 Springool. Все права защищены.</small>
</div>
</body>
</html>