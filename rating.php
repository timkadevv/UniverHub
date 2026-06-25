<?php

require_once "includes/database.php";
require_once "includes/auth.php";

requireLogin();

$material_id = $_POST["material_id"];
$rating = (int)$_POST["rating"];

if ($rating >= 1 && $rating <= 5) {

    $stmt = $db->prepare("
        SELECT id
        FROM ratings
        WHERE user_id = ?
        AND material_id = ?
    ");

    $stmt->execute([
        $_SESSION["user_id"],
        $material_id
    ]);

    $exists = $stmt->fetch();

    if ($exists) {

        $stmt = $db->prepare("
            UPDATE ratings
            SET rating = ?
            WHERE user_id = ?
            AND material_id = ?
        ");

        $stmt->execute([
            $rating,
            $_SESSION["user_id"],
            $material_id
        ]);

    } else {

        $stmt = $db->prepare("
            INSERT INTO ratings
            (
                user_id,
                material_id,
                rating
            )
            VALUES
            (?, ?, ?)
        ");

        $stmt->execute([
            $_SESSION["user_id"],
            $material_id,
            $rating
        ]);
    }
}

header(
    "Location: material.php?id="
    . $material_id
);

exit;