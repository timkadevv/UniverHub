<?php
require_once "includes/database.php";
require_once "includes/header.php";

// Получаем параметры поиска и фильтра
$search = $_GET["search"] ?? "";
$subject = $_GET["subject"] ?? "";

// Строим запрос с учётом фильтров
$sql = "
    SELECT
        m.*,
        u.name AS author,
        s.name AS subject_name
    FROM materials m
    LEFT JOIN users u ON m.user_id = u.id
    LEFT JOIN subjects s ON m.subject_id = s.id
    WHERE 1=1
";

$params = [];

if (!empty($search)) {
    $sql .= " AND m.title LIKE ?";
    $params[] = "%$search%";
}

if (!empty($subject)) {
    $sql .= " AND s.id = ?";
    $params[] = $subject;
}

$sql .= " ORDER BY m.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем список предметов для выпадающего списка
$subjects = $db->query("SELECT * FROM subjects ORDER BY name");
?>

<!-- ЗАГОЛОВОК СТРАНИЦЫ -->
<div class="materials-header">
    <h1>Учебные материалы</h1>
    <p>Находите нужные конспекты, лабораторные и файлы по предметам</p>
</div>

<!-- ФОРМА ПОИСКА (ТОЛЬКО ОДИН РАЗ) -->
<form class="search-filter" method="GET" action="">
    <div class="search-row">
        <div class="search-input-wrapper">
            <span class="icon">🔍</span>
            <input
                type="text"
                name="search"
                placeholder="Поиск материала..."
                value="<?= htmlspecialchars($search) ?>"
            >
        </div>

        <select name="subject">
            <option value="">Все предметы</option>
            <?php while ($sub = $subjects->fetch(PDO::FETCH_ASSOC)): ?>
                <option
                    value="<?= $sub['id'] ?>"
                    <?= ($subject == $sub['id']) ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($sub['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Найти</button>
    </div>
</form>

<!-- СЕТКА МАТЕРИАЛОВ -->
<div class="material-grid">
    <?php if (!empty($materials)): ?>
        <?php foreach ($materials as $material): ?>
            <div class="card material-card">
                <h2>
                    <a href="material.php?id=<?= $material['id'] ?>">
                        <?= htmlspecialchars($material['title']) ?>
                    </a>
                </h2>
                <div class="meta">
                    <span><strong>Предмет:</strong> <?= htmlspecialchars($material['subject_name']) ?></span>
                    <span><strong>Автор:</strong> <?= htmlspecialchars($material['author']) ?></span>
                    <span><strong>Скачиваний:</strong> <?= (int)$material['downloads'] ?></span>
                    <span><strong>Дата:</strong> <?= htmlspecialchars($material['created_at']) ?></span>
                </div>
                <div class="card-actions">
                    <a href="#" class="btn-download">Скачать</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-results">Материалов не найдено. Попробуйте изменить запрос.</p>
    <?php endif; ?>
</div>

<?php require_once "includes/footer.php"; ?>