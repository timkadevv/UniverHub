<?php
require_once "includes/database.php";
require_once "includes/auth.php";

requireLogin();

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"]);
    $subject_id = $_POST["subject_id"];
    $description = trim($_POST["description"]);

    if (empty($title) || empty($subject_id)) {
        $message = "Заполните обязательные поля.";
    } else {
        if (isset($_FILES["file"])) {
            $allowedExtensions = ["pdf", "docx", "pptx"];
            $fileName = $_FILES["file"]["name"];
            $fileTmp = $_FILES["file"]["tmp_name"];
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($extension, $allowedExtensions)) {
                $message = "Разрешены только PDF, DOCX и PPTX.";
            } else {
                $newFileName = time() . "_" . basename($fileName);
                $destination = "uploads/" . $newFileName;

                if (move_uploaded_file($fileTmp, $destination)) {
                    $stmt = $db->prepare("
                        INSERT INTO materials (user_id, subject_id, title, description, file_name, file_path)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $_SESSION["user_id"],
                        $subject_id,
                        $title,
                        $description,
                        $fileName,
                        $destination
                    ]);

                    $message = "Материал успешно загружен.";
                    $success = true;
                } else {
                    $message = "Ошибка загрузки файла.";
                }
            }
        }
    }
}

$subjects = $db->query("SELECT * FROM subjects ORDER BY name");

require_once "includes/header.php";
?>

<div class="auth-page">
    <div class="auth-card auth-card-wide upload-card">
        <h1>Загрузка материала</h1>
        <p class="auth-subtitle">Поделитесь своими учебными материалами с сообществом</p>

        <?php if ($message): ?>
            <div class="auth-error <?= $success ? 'auth-success' : '' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="upload-grid">
                <div class="auth-field" style="grid-column: 1 / -1;">
                    <label for="title">Название <span class="required">*</span></label>
                    <div class="input-icon">
                        <span class="icon"><i class="fas fa-file-alt"></i></span>
                        <input type="text" id="title" name="title" placeholder="Введите название материала" required>
                    </div>
                </div>

                <div class="auth-field" style="grid-column: 1 / -1;">
                    <label for="subject_id">Предмет <span class="required">*</span></label>
                    <div class="input-icon">
                        <span class="icon"><i class="fas fa-book"></i></span>
                        <select id="subject_id" name="subject_id" required>
                            <option value="">Выберите предмет</option>
                            <?php while ($subject = $subjects->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?= $subject['id'] ?>">
                                    <?= htmlspecialchars($subject['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="auth-field" style="grid-column: 1 / -1;">
                    <label for="description">Описание</label>
                    <div class="input-icon textarea-icon">
                        <span class="icon"><i class="fas fa-pencil-alt"></i></span>
                        <textarea id="description" name="description" rows="4" placeholder="Краткое описание материала (необязательно)"></textarea>
                    </div>
                </div>

                <div class="auth-field" style="grid-column: 1 / -1;">
                    <label for="file">Файл <span class="required">*</span></label>
                    <div class="file-upload-wrapper">
                        <div class="file-upload-area">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Перетащите файл сюда или <span class="file-browse">выберите файл</span></p>
                            <input type="file" id="file" name="file" required>
                            <span class="file-name">Файл не выбран</span>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="auth-btn">
                <i class="fas fa-upload"></i> Загрузить
            </button>
        </form>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>