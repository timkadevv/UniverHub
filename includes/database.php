<?php

try {

    $db = new PDO(
        "sqlite:" . __DIR__ . "/../database/university.db"
    );

    $db->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch(PDOException $e) {

    die(
        "Ошибка подключения: "
        . $e->getMessage()
    );
}