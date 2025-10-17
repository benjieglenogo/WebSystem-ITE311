<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h1 class="mb-4">Announcements</h1>
    <?php if (!empty($announcements)): ?>
        <div class="row">
            <?php foreach ($announcements as $announcement): ?>
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?= esc($announcement['title']) ?></h5>
                            <p class="card-text"><?= esc($announcement['content']) ?></p>
                            <p class="card-text"><small class="text-muted">Posted on: <?= esc($announcement['created_at']) ?></small></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No announcements available.</p>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
