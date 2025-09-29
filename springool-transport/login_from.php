<?php
session_start(); // Запускаем сессию
include('db.php'); // Подключаем базу данных

// Обработка POST-запроса (если форма отправлена)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    // Проверяем, что все поля заполнены
    if (empty($login) || empty($password)) {
        $message = "error"; // Ошибка: пустые поля
    } else {
        // Ищем пользователя в базе данных
        $stmt = $conn->prepare('SELECT * FROM users WHERE login = ?');
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Если пароль верный, создаем сессию
            $_SESSION['user'] = [
                'id' => $user['id'],
                'fullname' => $user['fullname'],
                'login' => $user['login'],
                'role' => $user['role']
            ];

            // Перенаправляем на главную страницу
            header("Location: index.php?message=login_success");
            exit();
        } else {
            // Если логин или пароль неверные
            $message = "error"; // Ошибка: неверный логин или пароль
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Springool</title>
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
            padding: 2rem;
            position: relative;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.05);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        h1 {
            color: var(--accent);
            text-align: center;
            margin-bottom: 2rem;
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
        input[type="password"] {
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
            content: '→';
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
            background: rgba(var(--accent), 0.1);
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
</head>
<body>
    <div class="container">
        <h1>Вход</h1>

        <?php if (isset($message) && $message == "error"): ?>
            <div class="message error">
                Неверный логин или пароль.
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="login">Логин:</label>
                <input type="text" id="login" name="login" required placeholder="Введите ваш логин">
            </div>

            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required placeholder="Введите ваш пароль">
            </div>

            <button type="submit" class="btn">Войти</button>
        </form>

        <p style="text-align: center; margin-top: 1rem;">
            Нет аккаунта? <a href="register_form.php">Зарегистрируйтесь</a>.
        </p>
    </div>
</body>
</html>