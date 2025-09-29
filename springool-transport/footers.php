<?php
// footers.php

// Подключение общих стилей и шапки (если есть)
include 'header.php';

?>

<div class="content-section">
    <h2 class="section-title">Расписание рейсов</h2>

    <!-- Фильтры -->
    <div class="filter-container">
        <input type="text" id="filterRoute" placeholder="Поиск по направлению">
        <input type="date" id="filterDate">
        <button onclick="filterSchedule()">Применить фильтр</button>
    </div>

    <!-- Таблица расписания -->
    <table class="schedule-table">
        <thead>
            <tr>
                <th>Направление</th>
                <th>Дата и время</th>
                <th>Автобус</th>
                <th>Мест</th>
                <th>Цена</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Пример статических данных расписания
            $schedule = [
                ["Москва → Санкт-Петербург", "2024-03-25 08:00", "Икарус 305", "45/45", "1500 ₽"],
                ["Казань → Екатеринбург", "2024-03-25 12:30", "Mercedes Sprinter", "18/20", "2500 ₽"],
                ["Новосибирск → Красноярск", "2024-03-26 15:45", "Volvo 9900", "35/50", "1800 ₽"],
                ["Сочи → Ростов-на-Дону", "2024-03-27 10:15", "Scania Interlink", "30/40", "2200 ₽"],
                ["Екатеринбург → Челябинск", "2024-03-28 09:00", "Setra S 519", "25/30", "1200 ₽"]
            ];

            // Вывод данных в таблицу
            foreach ($schedule as $route) {
                echo "<tr>
                        <td>{$route[0]}</td>
                        <td>{$route[1]}</td>
                        <td>{$route[2]}</td>
                        <td>{$route[3]}</td>
                        <td>{$route[4]}</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    // Функция для фильтрации расписания
    function filterSchedule() {
        const filterRoute = document.getElementById('filterRoute').value.toLowerCase();
        const filterDate = document.getElementById('filterDate').value;
        const rows = document.querySelectorAll('.schedule-table tbody tr');

        rows.forEach(row => {
            const route = row.cells[0].textContent.toLowerCase();
            const date = row.cells[1].textContent.split(' ')[0];
            const matchesRoute = route.includes(filterRoute);
            const matchesDate = filterDate ? date === filterDate : true;
            row.style.display = matchesRoute && matchesDate ? '' : 'none';
        });
    }
</script>

<?php
// Подключение футера
include 'footer.php';
?>