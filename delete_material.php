<?php

require_once "includes/database.php";
require_once "includes/auth.php";

requireLogin();

$id = $_POST["id"] ?? 0;

$stmt = $db->prepare("
SELECT *
FROM materials
WHERE id = ?
");

$stmt->execute([$id]);

$material =
$stmt->fetch(PDO::FETCH_ASSOC);

if(
$material &&
$material["user_id"]
==
$_SESSION["user_id"]
){

    if(
    file_exists(
        $material["file_path"]
    )
    ){
        unlink(
            $material["file_path"]
        );
    }

    $db->prepare("
    DELETE FROM materials
    WHERE id = ?
    ")
    ->execute([$id]);
}

header(
"Location: materials.php"
);

exit;