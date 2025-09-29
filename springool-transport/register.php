<?php
session_start(); // Запускаем сессию
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $fullname = trim($_POST['fullname']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    // Проверяем, что все поля заполнены
    if (empty($fullname) || empty($phone) || empty($email) || empty($login) || empty($password)) {
        header("Location: register_form.php?message=null");
        exit();
    }

    // Проверяем минимальную длину пароля
    if (strlen($password) < 6) {
        header("Location: register_form.php?message=short_password");
        exit();
    }

    // Проверяем уникальность логина и email
    $stmt = $conn->prepare('SELECT COUNT(*) FROM users WHERE login = ? OR email = ?');
    $stmt->execute([$login, $email]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        header("Location: register_form.php?message=exists");
        exit();
    }

    // Хэшируем пароль
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Добавляем пользователя в базу данных
    $stmt = $conn->prepare('INSERT INTO users (fullname, phone, email, login, password, role) VALUES (?, ?, ?, ?, ?, 0)');
    $result = $stmt->execute([$fullname, $phone, $email, $login, $hashedPassword]);

    if ($result) {
        // Создаем сессию для нового пользователя
        $userId = $conn->lastInsertId(); // Получаем ID нового пользователя
        $_SESSION['user'] = [
            'id' => $userId,
            'fullname' => $fullname,
            'login' => $login,
            'role' => 0 // Роль пользователя (например, 0 = обычный пользователь)
        ];

        // Перенаправляем на главную страницу
        header("Location: index.php?message=register_success");
        exit();
    } else {
        header("Location: register_form.php?message=error");
        exit();
    }
} else {
    // Если запрос не POST, перенаправляем на форму регистрации
    header("Location: register_form.php");
    exit();
}
?>