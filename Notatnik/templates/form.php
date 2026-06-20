<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $note ? 'Edycja notatki' : 'Nowa notatka' ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
    <div class="app-wrapper">
        <header class="app-header">
            <div class="container">
                <h1><?= $note ? '✏️ Edytuj notatkę' : '➕ Nowa notatka' ?></h1>
            </div>
        </header>

        <main class="app-main container">
            <?php include __DIR__ . '/partials/messages.php'; ?>

            <form method="post" action="<?= BASE_URL ?>?action=save" enctype="multipart/form-data" class="note-form">
                <?php if ($note): ?>
                    <input type="hidden" name="id" value="<?= $note->getId() ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="title">Tytuł <span class="required">*</span></label>
                    <input type="text" name="title" id="title" value="<?= htmlspecialchars($oldData['title'] ?? '') ?>" required>
                    <?php if (isset($errors['title'])): ?>
                        <span class="error-text"><?= $errors['title'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="content">Treść <span class="required">*</span></label>
                    <textarea name="content" id="content" rows="6" required><?= htmlspecialchars($oldData['content'] ?? '') ?></textarea>
                    <?php if (isset($errors['content'])): ?>
                        <span class="error-text"><?= $errors['content'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="tag">Tag <span class="required">*</span></label>
                    <input type="text" name="tag" id="tag" list="tag-list" value="<?= htmlspecialchars($oldData['tag'] ?? '') ?>" placeholder="np. praca, pomysł, zakupy">
                    <datalist id="tag-list">
                        <?php foreach ((new NoteRepository())->getAllTags() as $t): ?>
                            <option value="<?= htmlspecialchars($t) ?>">
                        <?php endforeach; ?>
                    </datalist>
                    <?php if (isset($errors['tag'])): ?>
                        <span class="error-text"><?= $errors['tag'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="priority">Priorytet <span class="required">*</span></label>
                    <select name="priority" id="priority" required>
                        <option value="">Wybierz</option>
                        <?php foreach (PRIORITIES as $p): ?>
                            <option value="<?= $p ?>" <?= (($oldData['priority'] ?? '') == $p) ? 'selected' : '' ?>><?= ucfirst($p) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['priority'])): ?>
                        <span class="error-text"><?= $errors['priority'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="attachment">Załącznik (plik)</label>
                    <input type="file" name="attachment" id="attachment">
                    <?php if ($note && $note->getAttachment()): ?>
                        <p class="current-file">📎 Obecny: <a href="<?= BASE_URL ?>uploads/<?= htmlspecialchars($note->getAttachment()) ?>" target="_blank"><?= htmlspecialchars($note->getAttachment()) ?></a></p>
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Zapisz</button>
                    <a href="<?= BASE_URL ?>?action=list" class="btn btn-secondary">Anuluj</a>
                </div>
            </form>
        </main>

        <footer class="app-footer">
            <div class="container">
                <p>&copy; <?= date('Y') ?> Notatnik osobisty</p>
            </div>
        </footer>
    </div>
</body>
</html>