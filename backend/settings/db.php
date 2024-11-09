<?php
    // Подключаемся к базе данных
    $host = 'localhost';
    $dbname = 'text-tallar_db';
    $user = 'root';
    $password = '';

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
?>