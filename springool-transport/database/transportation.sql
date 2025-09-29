-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.2
-- Время создания: Апр 30 2025 г., 15:49
-- Версия сервера: 8.2.0
-- Версия PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `transportation`
--

-- --------------------------------------------------------

--
-- Структура таблицы `bookings`
--

CREATE TABLE `bookings` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `route_id` int NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `passengers` int NOT NULL,
  `booking_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `route_id`, `status`, `created_at`, `passengers`, `booking_date`) VALUES
(1, 22, 4, 'pending', '2025-04-29 13:47:52', 1, '2025-04-29 16:47:52'),
(2, 22, 5, 'pending', '2025-04-29 13:49:26', 1, '2025-04-29 16:49:26'),
(3, 22, 4, 'pending', '2025-04-29 13:49:28', 1, '2025-04-29 16:49:28'),
(4, 22, 4, 'pending', '2025-04-29 13:53:00', 1, '2025-04-29 16:53:00'),
(5, 22, 4, 'pending', '2025-04-30 10:04:01', 1, '2025-04-30 13:04:01'),
(6, 22, 5, 'pending', '2025-04-30 10:07:10', 1, '2025-04-30 13:07:10'),
(7, 22, 4, 'pending', '2025-04-30 10:42:23', 1, '2025-04-30 13:42:23'),
(8, 22, 5, 'pending', '2025-04-30 10:42:25', 1, '2025-04-30 13:42:25'),
(9, 22, 4, 'pending', '2025-04-30 10:42:59', 1, '2025-04-30 13:42:59'),
(10, 22, 4, 'pending', '2025-04-30 10:44:15', 1, '2025-04-30 13:44:15'),
(11, 22, 4, 'pending', '2025-04-30 10:45:01', 1, '2025-04-30 13:45:01'),
(12, 22, 5, 'pending', '2025-04-30 10:46:05', 1, '2025-04-30 13:46:05'),
(13, 22, 7, 'pending', '2025-04-30 10:48:55', 1, '2025-04-30 13:48:55'),
(14, 22, 9, 'pending', '2025-04-30 10:50:33', 1, '2025-04-30 13:50:33'),
(15, 22, 5, 'pending', '2025-04-30 10:56:03', 1, '2025-04-30 13:56:03'),
(16, 22, 4, 'pending', '2025-04-30 11:05:26', 1, '2025-04-30 14:05:26'),
(17, 22, 8, 'pending', '2025-04-30 11:06:23', 1, '2025-04-30 14:06:23');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `sender_point` varchar(70) NOT NULL,
  `destination` varchar(255) DEFAULT 'Не указано',
  `number_of_passengers` varchar(35) NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int NOT NULL DEFAULT '0',
  `route_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `date`, `sender_point`, `destination`, `number_of_passengers`, `user_id`, `created_at`, `status`, `route_id`) VALUES
(23, '2025-04-29', 'Казань', 'Екатеринбург', '1', 23, '2025-04-29 12:29:00', 1, 0),
(24, '2025-04-29', 'Екатеринбург', 'Москва', '1', 23, '2025-04-29 12:29:05', 1, 0),
(35, NULL, 'Краснодар', 'Москва', '1', 22, '2025-04-30 11:15:09', 1, 6),
(36, NULL, 'Сочи', 'Казань', '1', 22, '2025-04-30 11:15:12', 1, 7),
(39, NULL, 'Краснодар', 'Москва', '1', 22, '2025-04-30 11:32:50', 0, 6),
(40, NULL, 'Екатеринбург', 'Москва', '1', 22, '2025-04-30 11:33:40', 1, 5),
(41, NULL, 'Краснодар', 'Москва', '1', 22, '2025-04-30 11:35:13', 1, 6),
(42, NULL, 'Екатеринбург', 'Москва', '1', 23, '2025-04-30 12:06:13', 0, 5),
(43, NULL, 'Краснодар', 'Москва', '2', 22, '2025-04-30 12:41:39', 0, 6);

-- --------------------------------------------------------

--
-- Структура таблицы `routes`
--

CREATE TABLE `routes` (
  `id` int NOT NULL,
  `departure_city` varchar(100) NOT NULL,
  `arrival_city` varchar(100) NOT NULL,
  `departure_time` time NOT NULL,
  `arrival_time` time NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `available_seats` int NOT NULL,
  `travel_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `routes`
--

INSERT INTO `routes` (`id`, `departure_city`, `arrival_city`, `departure_time`, `arrival_time`, `price`, `available_seats`, `travel_date`) VALUES
(4, 'Санкт-Петербург', 'Москва', '20:00:00', '04:30:00', 1600.00, 0, '2024-06-21'),
(5, 'Екатеринбург', 'Москва', '14:20:00', '20:40:00', 2100.00, 3, '2024-06-22'),
(6, 'Краснодар', 'Москва', '09:00:00', '14:15:00', 1950.00, 0, '2024-06-23'),
(7, 'Сочи', 'Казань', '11:30:00', '18:50:00', 2300.00, 9, '2024-06-24'),
(8, 'Новосибирск', 'Владивосток', '07:00:00', '18:30:00', 3500.00, 18, '2024-06-25'),
(9, 'Калининград', 'Санкт-Петербург', '13:15:00', '16:45:00', 1400.00, 4, '2024-06-26'),
(10, 'Самара', 'Ростов-на-Дону', '15:45:00', '21:20:00', 1700.00, 7, '2024-06-27'),
(11, 'Москва', 'Санкт-Петербург', '10:00:00', '14:00:00', 5000.00, 46, '2023-12-01'),
(12, 'Санкт-Петербург', 'Москва', '15:00:00', '19:00:00', 5000.00, 49, '2023-12-02'),
(13, 'Казань', 'Сочи', '08:00:00', '12:00:00', 7000.00, 30, '2023-12-03'),
(14, 'Сочи', 'Казань', '13:00:00', '17:00:00', 7000.00, 29, '2023-12-04');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `fullname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `login` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `fullname`, `phone`, `email`, `login`, `password`, `role`) VALUES
(22, 'Rasulov', '+7992927744', 'ganeapachix@gmail.com', 'admin', '$2y$10$Tcp.lUhVi2uBKKGAa6XQ4.4hfP8VmqUe0Z17OlraqlnjRSgDb6ZTK', 1),
(23, 'Lolo', '+7992927744', 'reket@gmail.com', 'lolo', '$2y$10$qBGFyi31P8zzITn.bqk.DuocbZOkc95k1n/fj/5sQvmx/7JAd55ka', 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `route_id` (`route_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT для таблицы `routes`
--
ALTER TABLE `routes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`);

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
