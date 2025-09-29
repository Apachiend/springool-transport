<?php
session_start(); // Запускаем сессию
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    // Проверяем, что все поля заполнены
    if (empty($login) || empty($password)) {
        header("Location: login_form.php?message=error");
        exit();
    }

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
        header("Location: login_form.php?message=error");
        exit();
    }
} else {
    // Если запрос не POST, перенаправляем на форму входа
    header("Location: login_form.php");
    exit();
}
?>