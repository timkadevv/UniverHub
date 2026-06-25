<?php

require_once "includes/database.php";
require_once "includes/auth.php";

requireLogin();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $material_id = $_POST["material_id"];
    $text = trim($_POST["text"]);

    if (!empty($text)) {

        $stmt = $db->prepare("
            INSERT INTO comments
            (
                user_id,
                material_id,
                text
            )
            VALUES
            (?, ?, ?)
        ");

        $stmt->execute([
            $_SESSION["user_id"],
            $material_id,
            $text
        ]);
    }
}

header(
    "Location: material.php?id="
    . $material_id
);

exit;