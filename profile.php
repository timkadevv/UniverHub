<?php

require_once "includes/database.php";
require_once "includes/auth.php";

requireLogin();

$stmt = $db->prepare("
SELECT *
FROM users
WHERE id = ?
");

$stmt->execute([
    $_SESSION["user_id"]
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

require_once "includes/header.php";

?>

<h1>Мой профиль</h1>

<div class="card">

    <h2>
        <?= htmlspecialchars($user["name"]) ?>
    </h2>

    <p>
        Email:
        <?= htmlspecialchars($user["email"]) ?>
    </p>

    <p>
        Факультет:
        <?= htmlspecialchars($user["faculty"]) ?>
    </p>

    <p>
        Группа:
        <?= htmlspecialchars($user["group_name"]) ?>
    </p>

    <p>
        Роль:
        <?= htmlspecialchars($user["role"]) ?>
    </p>

</div>

<div class="card">

    <h3>
        Действия
    </h3>

    <p>
        <a href="upload.php">
            Загрузить новый материал
        </a>
    </p>

</div>

<?php

require_once "includes/footer.php";

?>