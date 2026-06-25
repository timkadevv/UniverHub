<?php
require_once "includes/database.php";

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $faculty = trim($_POST["faculty"]);
    $group = trim($_POST["group"]);

    // Проверяем, существует ли email
    $check = $db->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($check->fetch()) {
        $message = "Такой email уже зарегистрирован";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("
            INSERT INTO users (name, email, password, faculty, group_name)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([$name, $email, $hash, $faculty, $group]);

        $message = "Регистрация успешна! Теперь вы можете войти.";
        $success = true;
    }
}

require_once "includes/header.php";
?>

<div class="auth-page">
    <div class="auth-card auth-card-wide">
        <h1>Регистрация</h1>
        <p class="auth-subtitle">Создайте аккаунт и присоединяйтесь к сообществу</p>

        <?php if ($message): ?>
            <div class="auth-error <?= $success ? 'auth-success' : '' ?>">
                <?= htmlspecialchars($message) ?>
                <?php if ($success): ?>
                    <br><a href="/../login.php" style="color: var(--primary); font-weight: 700;">Войти сейчас</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="auth-grid">
                <div class="auth-field">
                    <label for="name">Имя</label>
                    <div class="input-icon">
                        <input type="text" id="name" name="name"  required>
                    </div>
                </div>

                <div class="auth-field">
                    <label for="email">Email</label>
                    <div class="input-icon">
                        <input type="email" id="email" name="email"  required>
                    </div>
                </div>

                <div class="auth-field">
                    <label for="password">Пароль</label>
                    <div class="input-icon">
                        <input type="password" id="password" name="password"  required>
                    </div>
                </div>

                <div class="auth-field">
                    <label for="faculty">Факультет</label>
                    <div class="input-icon">
                        <input type="text" id="faculty" name="faculty" >
                    </div>
                </div>

                <div class="auth-field" style="grid-column: 1 / -1;">
                    <label for="group">Группа</label>
                    <div class="input-icon">
                        <input type="text" id="group" name="group" >
                    </div>
                </div>
            </div>

            <button type="submit" class="auth-btn">Зарегистрироваться</button>
        </form>

        <p class="auth-footer">
            Уже есть аккаунт? <a href="/../login.php">Войти</a>
        </p>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>