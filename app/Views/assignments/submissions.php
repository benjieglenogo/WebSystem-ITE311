<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
    <!-- Submissions Page -->
    <style>
        .submissions-container {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 20px;
        }

        .submissions-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .submissions-title {
            color: #343a40;
            font-weight: 600;
            font-size: 28px;
        }

        .submission-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #28a745;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .submission-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .student-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .student-name {
            font-weight: 600;
            color: #007bff;
            font-size: 16px;
        }

        .submission-date {
            color: #666;
            font-size: 14px;
        }

        .submission-content {
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .submission-file {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .submission-file a {
            color: #007bff;
            text-decoration: none;
        }

        .submission-file a:hover {
            text-decoration: underline;
        }

        .grade-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .grade-input {
            max-width: 100px;
        }

        .no-submissions {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            color: #999;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-submitted {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-graded {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>

    <div class="submissions-container">
        <div class="container mt-5">
            <!-- Header -->
            <div class="submissions-header">
                <h1 class="submissions-title">📋 Submissions for <?= esc($assignment['title'] ?? 'Assignment') ?></h1>
                <a href="<?= base_url('assignments/' . ($assignment['id'] ?? '#')) ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Assignment
                </a>
            </div>

            <!-- Assignment Info -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><?= esc($assignment['title'] ?? 'Assignment') ?></h5>
                    <p class="card-text"><?= esc($assignment['description'] ?? 'No description') ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Due: <?= date('M d, Y H:i', strtotime($assignment['due_date'] ?? 'now')) ?></span>
                        <span class="status-badge status-<?= isset($assignment['status']) ? strtolower($assignment['status']) : 'pending' ?>">
                            <?= ucfirst($assignment['status'] ?? 'Pending') ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Submissions List -->
            <?php if (!empty($submissions) && is_array($submissions)): ?>
                <div id="submissionsList">
                    <?php foreach ($submissions as $submission): ?>
                        <div class="submission-card">
                            <div class="student-info">
                                <div class="student-name">
                                    <?= esc($submission['student_name'] ?? 'Unknown Student') ?>
                                </div>
                                <div class="submission-date">
                                    Submitted: <?= date('M d, Y H:i', strtotime($submission['submitted_at'] ?? 'now')) ?>
                                </div>
                            </div>

                            <div class="submission-content">
                                <?php if (!empty($submission['submission_text'])): ?>
                                    <div class="mb-3">
                                        <strong>Answer:</strong>
                                        <p><?= nl2br(esc($submission['submission_text'])) ?></p>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($submission['file_path'])): ?>
                                    <div class="submission-file">
                                        <strong>Attachment:</strong>
                                        <a href="<?= base_url('writable/uploads/' . esc($submission['file_path'])) ?>" target="_blank">
                                            <i class="bi bi-file-earmark"></i> <?= esc(basename($submission['file_path'])) ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Grade Section for Teachers -->
                            <?php if (session()->get('role') === 'teacher' || session()->get('role') === 'admin'): ?>
                                <div class="grade-section">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="grade_<?= $submission['id'] ?>" class="form-label">Grade:</label>
                                                <input type="number" class="form-control grade-input" id="grade_<?= $submission['id'] ?>"
                                                       value="<?= esc($submission['grade'] ?? '') ?>" min="0" max="100" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="feedback_<?= $submission['id'] ?>" class="form-label">Feedback:</label>
                                                <textarea class="form-control" id="feedback_<?= $submission['id'] ?>" rows="2"><?= esc($submission['feedback'] ?? '') ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary btn-sm" onclick="saveGrade(<?= $submission['id'] ?>, <?= $assignment['id'] ?>)">
                                            <i class="bi bi-save"></i> Save Grade
                                        </button>
                                        <?php if (!empty($submission['grade'])): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Graded: <?= esc($submission['grade']) ?>/100
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-submissions">
                    <h4>📭 No Submissions Yet</h4>
                    <p>No students have submitted work for this assignment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function saveGrade(submissionId, assignmentId) {
        const grade = $('#grade_' + submissionId).val();
        const feedback = $('#feedback_' + submissionId).val();

        if (!grade) {
            alert('Please enter a grade');
            return;
        }

        $.ajax({
            url: '<?= base_url('assignments/save-grade') ?>',
            type: 'POST',
            data: {
                submission_id: submissionId,
                assignment_id: assignmentId,
                grade: grade,
                feedback: feedback,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Grade saved successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while saving the grade');
            }
        });
    }
</script>
<?= $this->endSection() ?>
