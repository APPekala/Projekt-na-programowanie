<?php if ($flash = (new NoteController())->getFlash()): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>