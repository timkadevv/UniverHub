<?php
require_once "includes/database.php";
require_once "includes/auth.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["role"];

        if ($user["role"] === "admin") {
            header("Location: admin/admin.php");
        } else {
            header("Location: profile.php");
        }
        exit;
    } else {
        $message = "Неверный логин или пароль.";
    }
}

require_once "includes/header.php";
?>

<div class="auth-page">
    <div class="auth-card">
        <h1>Вход</h1>
        <p class="auth-subtitle">Войдите в свой аккаунт, чтобы продолжить</p>

        <?php if ($message): ?>
            <div class="auth-error"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
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

            <button type="submit" class="auth-btn">Войти</button>
        </form>

        <p class="auth-footer">
            Нет аккаунта? <a href="/../register.php">Зарегистрироваться</a>
        </p>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>