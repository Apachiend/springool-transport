<?php
session_start(); // Запускаем сессию

include('db.php'); // Подключаем базу данных (PDO-соединение через $conn)

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user'])) {
    header("Location: login_form.php");
    exit;
}

// Получаем ID бронирования из POST-запроса
$booking_id = $_POST['booking_id'] ?? null;

if (!$booking_id || !is_numeric($booking_id)) {
    echo "Ошибка: Некорректный ID бронирования.";
    exit;
}

try {
    // Начинаем транзакцию
    $conn->beginTransaction();

    // Получаем информацию о бронировании
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$booking_id, $_SESSION['user']['id']]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        echo "Ошибка: Бронирование не найдено.";
        exit;
    }

    // Возвращаем места в маршрут
    $update_stmt = $conn->prepare("UPDATE routes SET available_seats = available_seats + ? WHERE id = ?");
    $update_stmt->execute([$booking['number_of_passengers'], $booking['route_id']]);

    // Обновляем статус бронирования на "Отменён"
    $cancel_stmt = $conn->prepare("UPDATE orders SET status = -1 WHERE id = ?");
    $cancel_stmt->execute([$booking_id]);

    // Фиксируем транзакцию
    $conn->commit();

    // Перенаправляем пользователя обратно в личный кабинет
    header("Location: account.php?cancel_success=1");
    exit;
} catch (Exception $e) {
    // Откатываем транзакцию в случае ошибки
    $conn->rollBack();
    echo "Ошибка при отмене бронирования: " . $e->getMessage();
}
?>