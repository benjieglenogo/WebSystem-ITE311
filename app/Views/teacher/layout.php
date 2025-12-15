<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="teacher-dashboard-container">
    <div class="container mt-5">
        <!-- Teacher Dashboard Header -->
        <div class="dashboard-welcome">
            <h1>Welcome, <?= esc(session('userName') ?? 'Teacher') ?>!</h1>
            <p>Manage your courses and students from here.</p>
        </div>

        <!-- Navigation Tabs -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <ul class="nav nav-tabs" id="teacherDashboardTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <?= isset($activeTab) && $activeTab === 'dashboard' ? 'active' : '' ?>" href="<?= base_url('teacher/dashboard') ?>">
                            <i class="bi bi-grid"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <?= isset($activeTab) && $activeTab === 'courses' ? 'active' : '' ?>" href="<?= base_url('teacher/course-management') ?>">
                            <i class="bi bi-book"></i> Course Management
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <?= isset($activeTab) && $activeTab === 'students' ? 'active' : '' ?>" href="<?= base_url('teacher/students') ?>">
                            <i class="bi bi-people"></i> Manage Students
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <?= isset($activeTab) && $activeTab === 'gradebook' ? 'active' : '' ?>" href="<?= base_url('gradebook') ?>">
                            <i class="bi bi-percent"></i> Gradebook
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="dashboard-content">
            <?= $this->renderSection('dashboard_content') ?>
        </div>
    </div>
</div>

<style>
    .teacher-dashboard-container {
        background-color: #f8f9fa;
        min-height: 100vh;
        padding: 20px;
    }

    .dashboard-welcome {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin-bottom: 30px;
        text-align: center;
    }

    .dashboard-welcome h1 {
        color: #343a40;
        margin-bottom: 10px;
    }

    .dashboard-welcome p {
        color: #666;
        font-size: 16px;
        margin-bottom: 0;
    }

    .nav-link {
        color: #495057;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
        padding: 10px 15px;
    }

    .nav-link:hover {
        color: #667eea;
    }

    .nav-link.active {
        color: #667eea;
        border-bottom-color: #667eea;
        background-color: transparent;
        font-weight: 500;
    }

    .dashboard-content {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 20px;
        min-height: 500px;
    }

    /* Dashboard Content Styles */
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .summary-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 20px;
        text-align: center;
        border-left: 4px solid #007bff;
    }

    .summary-card h3 {
        color: #495057;
        font-size: 28px;
        margin-bottom: 5px;
    }

    .summary-card p {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 0;
    }

    .courses-section {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    .courses-title {
        font-size: 20px;
        font-weight: 600;
        color: #343a40;
        margin-bottom: 20px;
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 10px;
    }

    .course-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s ease;
    }

    .course-item:hover {
        background-color: #f8f9fa;
    }

    .course-item:last-child {
        border-bottom: none;
    }

    .course-info {
        flex: 1;
    }

    .course-code {
        font-weight: 600;
        color: #007bff;
        margin-bottom: 5px;
    }

    .course-name {
        font-size: 16px;
        font-weight: 600;
        color: #343a40;
        margin-bottom: 5px;
    }

    .course-meta {
        font-size: 12px;
        color: #999;
    }

    .course-actions {
        display: flex;
        gap: 10px;
    }

    .btn-small {
        padding: 8px 15px;
        font-size: 13px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    .btn-info {
        background-color: #17a2b8;
        color: white;
    }

    .btn-info:hover {
        background-color: #138496;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }

    .status-active {
        background-color: #d4edda;
        color: #155724;
    }

    .status-inactive {
        background-color: #f8d7da;
        color: #721c24;
    }

    .no-courses {
        text-align: center;
        padding: 40px;
        color: #999;
    }
</style>
<?= $this->endSection() ?>
<task_progress>
- [x] Analyze current enrollment requests implementation
- [x] Check routes configuration
- [x] Verify controller and model
- [x] Examine view structure
- [ ] Fix navigation bar link
- [ ] Create proper dashboard layout
- [ ] Ensure MVC structure
- [ ] Fix JavaScript/UI behavior
- [ ] Test the implementation
</task_progress>
</write_to_file>
