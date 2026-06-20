<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notatnik osobisty</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
    <div class="app-wrapper">
        <header class="app-header">
            <div class="container">
                <h1>📒 Notatnik osobisty</h1>
                <p class="subtitle">Twórz, organizuj i wyszukuj swoje notatki</p>
            </div>
        </header>

        <main class="app-main container">
            <?php include __DIR__ . '/partials/messages.php'; ?>

            <!-- Panel podsumowujący -->
            <section class="dashboard-panel">
                <h2>📊 Podsumowanie</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <span class="stat-number"><?= $totalNotes ?></span>
                        <span class="stat-label">Wszystkich notatek</span>
                    </div>
                    <?php foreach (['wysoki', 'średni', 'niski'] as $p): ?>
                        <div class="stat-card">
                            <span class="stat-number"><?= $statsByPriority[$p] ?? 0 ?></span>
                            <span class="stat-label"><?= ucfirst($p) ?> priorytet</span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="tags-cloud">
                    <span class="tags-label">🏷️ Tagi:</span>
                    <?php foreach ($statsByTag as $tag => $count): ?>
                        <span class="tag-badge"><?= htmlspecialchars($tag) ?> (<?= $count ?>)</span>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Pasek wyszukiwania i filtrów -->
            <section class="toolbar">
                <form method="get" action="<?= BASE_URL ?>" class="search-form">
                    <input type="hidden" name="action" value="list">
                    <div class="search-row">
                        <div class="search-field">
                            <input type="text" name="search" placeholder="🔍 Szukaj w tytule i treści..." value="<?= htmlspecialchars($search ?? '') ?>">
                        </div>
                        <div class="filter-field">
                            <select name="tag_filter">
                                <option value="">Wszystkie tagi</option>
                                <?php foreach ($allTags as $tag): ?>
                                    <option value="<?= htmlspecialchars($tag) ?>" <?= ($tagFilter ?? '') == $tag ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tag) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-field">
                            <select name="priority_filter">
                                <option value="">Wszystkie priorytety</option>
                                <?php foreach (PRIORITIES as $p): ?>
                                    <option value="<?= $p ?>" <?= ($priorityFilter ?? '') == $p ? 'selected' : '' ?>><?= ucfirst($p) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-field">
                            <select name="sort">
                                <option value="created_at_desc" <?= ($sort ?? '') == 'created_at_desc' ? 'selected' : '' ?>>Najnowsze</option>
                                <option value="created_at_asc" <?= ($sort ?? '') == 'created_at_asc' ? 'selected' : '' ?>>Najstarsze</option>
                                <option value="title_asc" <?= ($sort ?? '') == 'title_asc' ? 'selected' : '' ?>>Tytuł A→Z</option>
                                <option value="title_desc" <?= ($sort ?? '') == 'title_desc' ? 'selected' : '' ?>>Tytuł Z→A</option>
                                <option value="priority_asc" <?= ($sort ?? '') == 'priority_asc' ? 'selected' : '' ?>>Priorytet ↑</option>
                                <option value="priority_desc" <?= ($sort ?? '') == 'priority_desc' ? 'selected' : '' ?>>Priorytet ↓</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Filtruj</button>
                        <a href="<?= BASE_URL ?>?action=list" class="btn btn-secondary">Resetuj</a>
                        <a href="<?= BASE_URL ?>?action=create" class="btn btn-success">+ Nowa notatka</a>
                    </div>
                </form>
            </section>

            <!-- Lista notatek -->
            <section class="notes-list">
                <h2>📋 Lista notatek</h2>
                <?php if (empty($notes)): ?>
                    <div class="empty-state">
                        <p>Brak notatek spełniających kryteria.</p>
                        <a href="<?= BASE_URL ?>?action=create" class="btn btn-success">Utwórz pierwszą notatkę</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="notes-table">
                            <thead>
                                <tr>
                                    <th>Tytuł</th>
                                    <th>Tag</th>
                                    <th>Priorytet</th>
                                    <th>Data utworzenia</th>
                                    <th>Treść (skrót)</th>
                                    <th>Załącznik</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($notes as $note): ?>
                                    <tr>
                                        <td class="note-title"><?= htmlspecialchars($note->getTitle()) ?></td>
                                        <td><span class="tag-badge"><?= htmlspecialchars($note->getTag()) ?></span></td>
                                        <td><span class="priority-badge priority-<?= htmlspecialchars($note->getPriority()) ?>"><?= htmlspecialchars($note->getPriority()) ?></span></td>
                                        <td><?= htmlspecialchars($note->getCreatedAt()) ?></td>
                                        <td class="note-preview"><?= htmlspecialchars($note->getPreview()) ?></td>
                                        <td>
                                            <?php if ($note->getAttachment()): ?>
                                                <a href="<?= BASE_URL ?>uploads/<?= htmlspecialchars($note->getAttachment()) ?>" target="_blank" class="file-link">📎</a>
                                            <?php else: ?>
                                                <span class="no-file">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="actions">
                                            <a href="<?= BASE_URL ?>?action=edit&id=<?= $note->getId() ?>" class="btn btn-sm btn-edit">✏️</a>
                                            <a href="<?= BASE_URL ?>?action=delete&id=<?= $note->getId() ?>" class="btn btn-sm btn-delete" onclick="return confirm('Czy na pewno usunąć notatkę „<?= htmlspecialchars(addslashes($note->getTitle())) ?>”?')">🗑️</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </main>

        <footer class="app-footer">
            <div class="container">
                <p>&copy; <?= date('Y') ?> Notatnik osobisty — Projekt PHP</p>
            </div>
        </footer>
    </div>
    <script>
        // Automatyczne znikanie komunikatów
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.alert').forEach(function(el) {
                setTimeout(function() {
                    el.style.transition = 'opacity 0.5s';
                    el.style.opacity = '0';
                    setTimeout(function() { el.remove(); }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>