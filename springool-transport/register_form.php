<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Springool</title>
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
            background: rgba(255,255,255,0.05);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
            position: relative;
        }

        /* Стили для ссылки "Вернуться на главную" */
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
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 0.8rem;
            background: #333;
            border: 1px solid var(--accent);
            color: white; /* Белый текст в инпутах */
            border-radius: 5px;
        }

        /* Стили для кнопки регистрации */
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
            background: black; /* Черный фон при наведении */
        }

        /* Иконка в кнопке */
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
        <h1>Регистрация</h1>
        
        <form method="post" action="register.php" autocomplete="off">
            <div class="form-group">
                <label for="fullname">Имя:</label>
                <input type="text" id="fullname" name="fullname" required placeholder="Введите ваше имя">
            </div>
            
            <!-- Новое поле Email -->
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required placeholder="example@example.com">
            </div>
            
            <div class="form-group">
                <label for="phone">Телефон:</label>
                <input type="text" id="phone" name="phone" required placeholder="+7 (XXX) XXX-XX-XX">
            </div>
            
            <div class="form-group">
                <label for="login">Логин:</label>
                <input type="text" id="login" name="login" required placeholder="Выберите логин">
            </div>
            
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required minlength="6" placeholder="Не менее 6 символов">
            </div>
            
            <button type="submit" class="btn">Зарегистрироваться</button>
        </form>

        <?php if (isset($_GET['message'])): ?>
            <?php if ($_GET['message'] == 'success'): ?>
                <div class="message success">
                    Регистрация успешна!
                    <?php if (isset($_GET['id'])): ?>
                        <a href="update.php?id=<?= htmlspecialchars($_GET['id']) ?>">
                            Настройте профиль
                        </a>
                    <?php endif; ?>
                </div>
            <?php elseif ($_GET['message'] == 'error'): ?>
                <div class="message error">
                    Ошибка регистрации. Попробуйте еще раз.
                </div>
            <?php elseif ($_GET['message'] == 'null'): ?>
                <div class="message error">
                    Заполните все поля
                </div>
            <?php elseif ($_GET['message'] == 'exists'): ?>
                <div class="message error">
                    Пользователь с таким логином или email уже существует.
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Ссылка "Вернуться на главную" -->
    <a href="index.php" class="back-link">← На главную</a>
</body>
</html>