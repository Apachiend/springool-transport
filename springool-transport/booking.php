<?php
session_start(); // Запускаем сессию
require_once 'config.php'; // Подключаем конфигурацию базы данных

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    echo "Ошибка: Для бронирования необходимо войти в систему.";
    exit;
}

// Получаем параметры из GET-запроса
$route_id = $_GET['route_id'] ?? null;
$passengers = $_GET['passengers'] ?? null;

// Проверяем, что все необходимые данные переданы
if (!$route_id || !$passengers || !is_numeric($passengers) || $passengers <= 0) {
    echo "Ошибка: Некорректное количество пассажиров или маршрут не указан.";
    exit;
}

// Проверяем маршрут в базе данных
try {
    $stmt = $pdo->prepare("SELECT * FROM routes WHERE id = ? AND available_seats >= ?");
    $stmt->execute([$route_id, $passengers]);
    $route = $stmt->fetch();

    if (!$route) {
        echo "Ошибка: Недостаточно мест для бронирования.";
        exit;
    }

    // Начинаем транзакцию
    $pdo->beginTransaction();

    // Обновляем количество доступных мест
    $new_available_seats = $route['available_seats'] - $passengers;
    $update_stmt = $pdo->prepare("UPDATE routes SET available_seats = ? WHERE id = ?");
    $update_stmt->execute([$new_available_seats, $route_id]);

    // Сохраняем информацию о бронировании в таблицу orders
    $user_id = $_SESSION['user']['id'];

    // Меняем passengers на number_of_passengers (если именно такое имя в БД)
    $stmt = $pdo->prepare("
    INSERT INTO orders 
    (user_id, route_id, number_of_passengers, sender_point, destination, created_at, status) 
    VALUES (?, ?, ?, ?, ?, NOW(), ?)
");

$stmt->execute([
    $user_id,
    $route_id,
    $passengers,
    $route['departure_city'],
    $route['arrival_city'],
    0 // по умолчанию "на ожидании"
]);

    // Фиксируем транзакцию
    $pdo->commit();

    // Перенаправляем пользователя на страницу с расписаниями
    header("Location: routes.php?booking_success=1");
    exit; // Важно завершить выполнение скрипта после редиректа
} catch (PDOException $e) {
    // Откатываем транзакцию в случае ошибки
    $pdo->rollBack();
    echo "Ошибка при бронировании: " . htmlspecialchars($e->getMessage());
}
?>