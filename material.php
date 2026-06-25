<?php
require_once "includes/database.php";
require_once "includes/auth.php";
require_once "includes/header.php";

$id = $_GET["id"] ?? 0;

$stmt = $db->prepare("
    SELECT
        materials.*,
        users.name AS author,
        subjects.name AS subject_name
    FROM materials
    LEFT JOIN users ON materials.user_id = users.id
    LEFT JOIN subjects ON materials.subject_id = subjects.id
    WHERE materials.id = ?
");
$stmt->execute([$id]);
$material = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$material) {
    die("Материал не найден");
}

// Рейтинг
$stmt = $db->prepare("SELECT AVG(rating) AS avg_rating, COUNT(*) AS votes FROM ratings WHERE material_id = ?");
$stmt->execute([$id]);
$ratingInfo = $stmt->fetch(PDO::FETCH_ASSOC);

// Комментарии
$stmt = $db->prepare("
    SELECT comments.*, users.name
    FROM comments
    LEFT JOIN users ON comments.user_id = users.id
    WHERE material_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Функция для отображения звёзд
function renderStars($avgRating, $votes = 0) {
    $full = floor($avgRating);
    $half = ($avgRating - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;
    $stars = '';
    for ($i = 0; $i < $full; $i++) $stars .= '<i class="fas fa-star"></i>';
    if ($half) $stars .= '<i class="fas fa-star-half-alt"></i>';
    for ($i = 0; $i < $empty; $i++) $stars .= '<i class="far fa-star"></i>';
    return $stars;
}
?>

<div class="material-page">
    <div class="auth-card material-detail-card">
        <!-- Заголовок -->
        <h1><?= htmlspecialchars($material["title"]) ?></h1>

        <!-- Мета-информация -->
        <div class="meta-grid">
            <div class="meta-item">
                <i class="fas fa-book"></i>
                <span><strong>Предмет:</strong> <?= htmlspecialchars($material["subject_name"]) ?></span>
            </div>
            <div class="meta-item">
                <i class="fas fa-user"></i>
                <span><strong>Автор:</strong> <?= htmlspecialchars($material["author"]) ?></span>
            </div>
            <div class="meta-item">
                <i class="fas fa-calendar-alt"></i>
                <span><strong>Дата загрузки:</strong> <?= $material["created_at"] ?></span>
            </div>
            <div class="meta-item">
                <i class="fas fa-download"></i>
                <span><strong>Скачиваний:</strong> <?= (int)$material["downloads"] ?></span>
            </div>
        </div>

        <!-- Описание -->
        <?php if (!empty($material["description"])): ?>
            <div class="description">
                <i class="fas fa-align-left"></i>
                <p><?= nl2br(htmlspecialchars($material["description"])) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Рейтинг -->
    <div class="auth-card material-detail-card rating-card">
        <h2><i class="fas fa-star"></i> Рейтинг материала</h2>
        <div class="rating-display">
            <div class="stars">
                <?= renderStars($ratingInfo["avg_rating"] ?? 0, $ratingInfo["votes"] ?? 0) ?>
            </div>
            <div class="rating-numbers">
                <span class="average"><?= number_format($ratingInfo["avg_rating"] ?? 0, 1) ?></span>
                <span class="votes">(<?= (int)($ratingInfo["votes"] ?? 0) ?> оценок)</span>
            </div>
        </div>
    </div>

    <!-- Форма оценки (только для авторизованных) -->
    <?php if (isLoggedIn()): ?>
        <div class="auth-card material-detail-card">
            <h2><i class="fas fa-pen"></i> Поставить оценку</h2>
            <form method="POST" action="rating.php" class="rating-form">
                <input type="hidden" name="material_id" value="<?= $material["id"] ?>">
                <div class="rating-select-wrapper">
                    <select name="rating" required>
                        <option value="">Оцените</option>
                        <option value="1">1 ⭐</option>
                        <option value="2">2 ⭐⭐</option>
                        <option value="3">3 ⭐⭐⭐</option>
                        <option value="4">4 ⭐⭐⭐⭐</option>
                        <option value="5">5 ⭐⭐⭐⭐⭐</option>
                    </select>
                    <button type="submit" class="auth-btn small-btn">Оценить</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Кнопка скачивания -->
    <div class="auth-card material-detail-card download-card">
        <a href="download.php?id=<?= $material['id'] ?>" class="download-btn">
            <i class="fas fa-file-download"></i> Скачать файл
        </a>
    </div>

    <!-- Список комментариев -->
    <div class="auth-card material-detail-card comments-card">
        <h2><i class="fas fa-comments"></i> Комментарии (<?= count($comments) ?>)</h2>

        <?php if (empty($comments)): ?>
            <p class="no-comments">Комментариев пока нет. Будьте первым!</p>
        <?php else: ?>
            <div class="comments-list">
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-item">
                        <div class="comment-header">
                            <strong><?= htmlspecialchars($comment["name"]) ?></strong>
                            <small><?= $comment["created_at"] ?></small>
                        </div>
                        <p><?= nl2br(htmlspecialchars($comment["text"])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Форма добавления комментария (только авторизованные) -->
    <?php if (isLoggedIn()): ?>
        <div class="auth-card material-detail-card comment-form-card">
            <h2><i class="fas fa-edit"></i> Добавить комментарий</h2>
            <form method="POST" action="comment.php">
                <input type="hidden" name="material_id" value="<?= $material['id'] ?>">
                <div class="input-icon textarea-icon">
                    <span class="icon"><i class="fas fa-pencil-alt"></i></span>
                    <textarea name="text" rows="4" placeholder="Введите комментарий..." required></textarea>
                </div>
                <button type="submit" class="auth-btn">Отправить</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Удаление материала (только автор) -->
    <?php if (isLoggedIn() && $_SESSION["user_id"] == $material["user_id"]): ?>
        <div class="auth-card material-detail-card delete-card">
            <form method="POST" action="delete_material.php" onsubmit="return confirm('Вы уверены, что хотите удалить этот материал?');">
                <input type="hidden" name="id" value="<?= $material["id"] ?>">
                <button type="submit" class="delete-btn">
                    <i class="fas fa-trash-alt"></i> Удалить материал
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require_once "includes/footer.php"; ?>