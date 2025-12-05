<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0 text-primary fw-bold">Announcements</h1>
</div>

<?php if (isset($announcements) && !empty($announcements)): ?>
    <div class="list-group">
        <?php foreach ($announcements as $a): ?>
            <div class="list-group-item">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="mb-1"><?php echo esc($a['title'] ?? 'Untitled'); ?></h5>
                        <p class="mb-1 text-muted"><?php echo esc($a['message'] ?? ''); ?></p>
                        <small class="text-muted"><?php echo isset($a['created_at']) ? date('M d, Y', strtotime($a['created_at'])) : ''; ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">No announcements yet.</div>
<?php endif; ?>

<?= $this->endSection() ?>
