<?php

require_once "includes/database.php";

$id = $_GET["id"] ?? 0;

$stmt = $db->prepare("
SELECT *
FROM materials
WHERE id = ?
");

$stmt->execute([$id]);

$file = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$file){

    die("Файл не найден");
}

$db->prepare("
UPDATE materials
SET downloads = downloads + 1
WHERE id = ?
")->execute([$id]);

$path = $file["file_path"];

if(!file_exists($path)){

    die("Файл отсутствует");
}

header(
"Content-Disposition: attachment; filename=\""
. basename($file["file_name"])
. "\""
);

header(
"Content-Type: application/octet-stream"
);

readfile($path);

exit;