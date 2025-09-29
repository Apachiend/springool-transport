<?php
session_start();
// Проверка авторизации и роли пользователя
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 1) {
    header("Location: login_form.php");
    exit;
}
include('db.php'); // Подключаем базу данных
$action = $_GET['action'] ?? null;
if (!$action) {
    header("Location: admin_panel.php");
    exit;
}

// === Новые стили ===
echo '
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
        font-family: "Comic Sans MS", cursive;
    }

    body {
        background: linear-gradient(45deg, #333, #111);
        min-height: 100vh;
        color: var(--text-color);
        padding: 2rem;
        position: relative;
    }

    .container {
        max-width: 500px;
        margin: 0 auto;
        background: rgba(255,255,255,0.05);
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.5);
        position: relative;
    }

    .back-link {
        position: fixed;
        right: 2rem;
        bottom: 2rem;
        color: var(--accent);
        text-decoration: none;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        padding: 0.5rem 1rem;
        border-radius: 5px;
    }

    .back-link:hover {
        background: rgba(255,255,255,0.1);
        transform: translateX(-5px);
    }

    h2 {
        color: var(--accent);
        text-align: center;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid var(--accent);
        padding-bottom: 0.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-color);
    }

    input[type="text"],
    input[type="password"],
    input[type="email"],
    input[type="number"],
    select {
        width: 100%;
        padding: 0.8rem;
        background: #333;
        border: 1px solid var(--accent);
        color: white;
        border-radius: 5px;
    }

    .btn {
        background: var(--accent);
        border: none;
        padding: 0.8rem;
        width: 100%;
        color: white;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        font-weight: bold;
    }

    .btn:hover {
        background: black;
    }

    .btn::after {
        content: "→";
        margin-left: 10px;
        transition: transform 0.3s ease;
    }

    .btn:hover::after {
        transform: translateX(5px);
    }

    .message {
        margin-top: 1.5rem;
        padding: 1rem;
        border-radius: 5px;
    }

    .success {
        background: rgba(255, 51, 51, 0.1); /* Легкий красный фон */
        color: var(--accent);
    }

    .error {
        background: rgba(255, 0, 0, 0.1);
        color: red;
    }

    a {
        color: var(--accent);
        text-decoration: underline;
    }
</style>
';

try {
    switch ($action):
        // === Обновление статуса брони ===
        case 'update_order_status':
            $id = $_GET['id'] ?? null;
            $status = $_GET['status'] ?? null;
            if (!$id || !is_numeric($status)) {
                echo "<div class='error'>Ошибка: Недостаточно данных.</div>";
                echo '<a href="admin_panel.php" class="back-link">← На главную</a>';
                exit;
            }
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            echo "<div class='success'>Статус успешно обновлён!</div>";
            echo '<meta http-equiv="refresh" content="2;URL=admin_panel.php">';
            exit;
            break;

        // === Добавление маршрута ===
        case 'add_route':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $departure_city = trim($_POST['departure_city'] ?? '');
                $arrival_city = trim($_POST['arrival_city'] ?? '');
                $available_seats = $_POST['available_seats'] ?? '';
                $price = $_POST['price'] ?? '';
                if (empty($departure_city) || empty($arrival_city) || !is_numeric($available_seats) || !is_numeric($price)) {
                    echo "<div class='error'>Ошибка: Все поля обязательны и должны быть корректными.</div>";
                    echo '<a href="admin_actions.php?action=add_route" class="back-link">← Вернуться</a>';
                    exit;
                }
                $stmt = $conn->prepare("INSERT INTO routes (departure_city, arrival_city, available_seats, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$departure_city, $arrival_city, $available_seats, $price]);
                echo "<div class='success'>Маршрут успешно добавлен!</div>";
                echo '<meta http-equiv="refresh" content="2;URL=admin_panel.php">';
                exit;
            } else {
                echo '
                <div class="container">
                    <h2>Добавить маршрут</h2>
                    <form method="POST" action="admin_actions.php?action=add_route">
                        <div class="form-group">
                            <label>Откуда:
                                <input type="text" name="departure_city" required>
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Куда:
                                <input type="text" name="arrival_city" required>
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Доступные места:
                                <input type="number" name="available_seats" required min="1">
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Цена:
                                <input type="number" name="price" step="0.01" required min="0">
                            </label>
                        </div>
                        <button class="btn" type="submit">Добавить маршрут</button>
                    </form>
                    <a href="admin_panel.php" class="back-link">← На главную</a>
                </div>';
            }
            break;

        // === Редактировать маршрут ===
        case 'edit_route':
            $id = $_GET['id'] ?? null;
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $departure_city = trim($_POST['departure_city'] ?? '');
                $arrival_city = trim($_POST['arrival_city'] ?? '');
                $available_seats = $_POST['available_seats'] ?? '';
                $price = $_POST['price'] ?? '';
                if (!$id || empty($departure_city) || empty($arrival_city) || !is_numeric($available_seats) || !is_numeric($price)) {
                    echo "<div class='error'>Ошибка: Все поля обязательны и должны быть корректными.</div>";
                    echo '<a href="admin_actions.php?action=edit_route&id=' . $id . '" class="back-link">← Вернуться</a>';
                    exit;
                }
                $stmt = $conn->prepare("UPDATE routes SET departure_city = ?, arrival_city = ?, available_seats = ?, price = ? WHERE id = ?");
                $stmt->execute([$departure_city, $arrival_city, $available_seats, $price, $id]);
                echo "<div class='success'>Маршрут успешно отредактирован!</div>";
                echo '<meta http-equiv="refresh" content="2;URL=admin_panel.php">';
                exit;
            } else {
                $stmt = $conn->prepare("SELECT * FROM routes WHERE id = ?");
                $stmt->execute([$id]);
                $route = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$route) {
                    echo "<div class='error'>Ошибка: Маршрут не найден.</div>";
                    exit;
                }
                echo '
                <div class="container">
                    <h2>Редактировать маршрут</h2>
                    <form method="POST" action="admin_actions.php?action=edit_route&id=' . $id . '">
                        <div class="form-group">
                            <label>Откуда:
                                <input type="text" name="departure_city" value="' . htmlspecialchars($route['departure_city']) . '" required>
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Куда:
                                <input type="text" name="arrival_city" value="' . htmlspecialchars($route['arrival_city']) . '" required>
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Доступные места:
                                <input type="number" name="available_seats" value="' . htmlspecialchars($route['available_seats']) . '" required min="0">
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Цена:
                                <input type="number" name="price" value="' . htmlspecialchars($route['price']) . '" step="0.01" required min="0">
                            </label>
                        </div>
                        <button class="btn" type="submit">Сохранить изменения</button>
                    </form>
                    <a href="admin_panel.php" class="back-link">← На главную</a>
                </div>';
            }
            break;

        // === Удалить маршрут ===
        case 'delete_route':
            $id = $_GET['id'] ?? null;
            if (!$id) {
                echo "<div class='error'>Ошибка: ID маршрута не указан.</div>";
                echo '<a href="admin_panel.php" class="back-link">← На главную</a>';
                exit;
            }
            $stmt = $conn->prepare("DELETE FROM routes WHERE id = ?");
            $stmt->execute([$id]);
            echo "<div class='success'>Маршрут удалён!</div>";
            echo '<meta http-equiv="refresh" content="2;URL=admin_panel.php">';
            exit;
            break;

        // === Добавить пользователя ===
        case 'add_user':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $fullname = trim($_POST['fullname'] ?? '');
                $login = trim($_POST['login'] ?? '');
                $password = $_POST['password'] ?? '';
                $role = $_POST['role'] ?? '';
                if (empty($fullname) || empty($login) || empty($password) || !is_numeric($role)) {
                    echo "<div class='error'>Ошибка: Все поля обязательны.</div>";
                    echo '<a href="admin_actions.php?action=add_user" class="back-link">← Вернуться</a>';
                    exit;
                }
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (fullname, login, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$fullname, $login, $hashed_password, $role]);
                echo "<div class='success'>Пользователь успешно добавлен!</div>";
                echo '<meta http-equiv="refresh" content="2;URL=admin_panel.php">';
                exit;
            } else {
                echo '
                <div class="container">
                    <h2>Добавить пользователя</h2>
                    <form method="POST" action="admin_actions.php?action=add_user">
                        <div class="form-group">
                            <label>Имя:
                                <input type="text" name="fullname" required>
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Логин:
                                <input type="text" name="login" required>
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Пароль:
                                <input type="password" name="password" required>
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Роль:
                                <select name="role" required>
                                    <option value="0">Пользователь</option>
                                    <option value="1">Администратор</option>
                                </select>
                            </label>
                        </div>
                        <button class="btn" type="submit">Добавить пользователя</button>
                    </form>
                    <a href="admin_panel.php" class="back-link">← На главную</a>
                </div>';
            }
            break;

        // === Редактировать пользователя ===
        case 'edit_user':
            $id = $_GET['id'] ?? null;
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $fullname = trim($_POST['fullname'] ?? '');
                $login = trim($_POST['login'] ?? '');
                $password = $_POST['password'] ?? '';
                $role = $_POST['role'] ?? '';
                if (!$id || empty($fullname) || empty($login) || !is_numeric($role)) {
                    echo "<div class='error'>Ошибка: Все обязательные поля должны быть заполнены.</div>";
                    echo '<a href="admin_actions.php?action=edit_user&id=' . $id . '" class="back-link">← Вернуться</a>';
                    exit;
                }
                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET fullname = ?, login = ?, password = ?, role = ? WHERE id = ?");
                    $stmt->execute([$fullname, $login, $hashed_password, $role, $id]);
                } else {
                    $stmt = $conn->prepare("UPDATE users SET fullname = ?, login = ?, role = ? WHERE id = ?");
                    $stmt->execute([$fullname, $login, $role, $id]);
                }
                echo "<div class='success'>Пользователь успешно изменён!</div>";
                echo '<meta http-equiv="refresh" content="2;URL=admin_panel.php">';
                exit;
            } else {
                $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$user) {
                    echo "<div class='error'>Ошибка: Пользователь не найден.</div>";
                    exit;
                }
                echo '
                <div class="container">
                    <h2>Редактировать пользователя</h2>
                    <form method="POST" action="admin_actions.php?action=edit_user&id=' . $id . '">
                        <div class="form-group">
                            <label>Имя:
                                <input type="text" name="fullname" value="' . htmlspecialchars($user['fullname']) . '" required>
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Логин:
                                <input type="text" name="login" value="' . htmlspecialchars($user['login']) . '" required>
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Новый пароль (если хотите изменить):
                                <input type="password" name="password">
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Роль:
                                <select name="role" required>
                                    <option value="0"' . ($user['role'] == 0 ? ' selected' : '') . '>Пользователь</option>
                                    <option value="1"' . ($user['role'] == 1 ? ' selected' : '') . '>Администратор</option>
                                </select>
                            </label>
                        </div>
                        <button class="btn" type="submit">Сохранить изменения</button>
                    </form>
                    <a href="admin_panel.php" class="back-link">← На главную</a>
                </div>';
            }
            break;

        // === Удалить пользователя ===
        case 'delete_user':
            $id = $_GET['id'] ?? null;
            if (!$id) {
                echo "<div class='error'>Ошибка: ID пользователя не указан.</div>";
                echo '<a href="admin_panel.php" class="back-link">← На главную</a>';
                exit;
            }
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            echo "<div class='success'>Пользователь удалён!</div>";
            echo '<meta http-equiv="refresh" content="2;URL=admin_panel.php">';
            exit;
            break;

        // === Удалить заказ ===
        case 'delete_order':
            $id = $_GET['id'] ?? null;
            if (!$id || !is_numeric($id)) {
                echo "<div class='error'>Ошибка: Некорректный ID бронирования.</div>";
                echo '<a href="admin_panel.php" class="back-link">← На главную</a>';
                exit;
            }
            try {
                $conn->beginTransaction();
                $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
                $stmt->execute([$id]);
                $order = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$order) {
                    echo "<div class='error'>Ошибка: Бронирование не найдено.</div>";
                    exit;
                }
                $update_seats_stmt = $conn->prepare("UPDATE routes SET available_seats = available_seats + ? WHERE id = ?");
                $update_seats_stmt->execute([$order['number_of_passengers'], $order['route_id']]);
                $delete_stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
                $delete_stmt->execute([$id]);
                $conn->commit();
                echo "<div class='success'>Заказ удалён успешно!</div>";
                echo '<meta http-equiv="refresh" content="2;URL=admin_panel.php">';
                exit;
            } catch (PDOException $e) {
                $conn->rollBack();
                echo "<div class='error'>Ошибка при удалении бронирования: " . htmlspecialchars($e->getMessage()) . "</div>";
                exit;
            }
            break;

        default:
            echo "<div class='error'>Ошибка: Неизвестное действие.</div>";
            echo '<a href="admin_panel.php" class="back-link">← На главную</a>';
            exit;
    endswitch;
} catch (PDOException $e) {
    error_log("Ошибка базы данных: " . $e->getMessage());
    echo "<div class='error'>Произошла ошибка на сервере.</div>";
    echo '<a href="admin_panel.php" class="back-link">← На главную</a>';
}
?>