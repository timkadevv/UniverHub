<?php

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>University Knowledge Hub</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,600;14..32,700;14..32,800;14..32,900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="/../assets/css/style.css">
</head>
<body>

<nav class="navbar">
  <div class="logo">UniverHub</div>

  <ul class="menu">
    <li><a href="/../index.php">Главная</a></li>
    <li><a href="/../materials.php">Материалы</a></li>

    <?php if(isset($_SESSION["user_id"])): ?>
      <li><a href="/../profile.php">Профиль</a></li>
      <li><a href="/../upload.php">Загрузить</a></li>
      <li><a href="/../logout.php">Выход</a></li>
    <?php else: ?>
      <li><a href="/../login.php">Вход</a></li>
      <li><a href="/../register.php">Регистрация</a></li>
    <?php endif; ?>
  </ul>
</nav>

<div class="container">