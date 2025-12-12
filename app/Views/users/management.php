<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
    <!-- User Management Dashboard -->
    <style>
        /* Accessible User Management Styles */
        .user-management-container {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 20px;
        }

        .user-management-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .user-management-title {
            color: #343a40;
            font-weight: 600;
        }

        /* Summary Cards */
        .user-summary-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }

        .user-summary-card h3 {
            color: #495057;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .user-summary-card p {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 0;
        }

        /* Search and Filter Section */
        .user-search-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .user-search-input {
            border-radius: 25px;
            border: 1px solid #ced4da;
            padding-left: 15px;
        }

        .user-search-btn {
            border-radius: 25px;
            background-color: #007bff;
            border: none;
        }

        /* Users Table */
        .users-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .users-table .table {
            margin-bottom: 0;
        }

        .users-table .table th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 500;
            color: #495057;
        }

        .users-table .table td {
            vertical-align: middle;
        }

        /* Status Badges */
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            text-transform: capitalize;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Role Badges */
        .role-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            text-transform: capitalize;
        }

        .role-admin {
            background-color: #cce5ff;
            color: #004085;
        }

        .role-teacher {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .role-student {
            background-color: #fff3cd;
            color: #856404;
        }

        /* Action Buttons */
        .btn-action {
            margin: 0 2px;
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 5px;
        }

        .btn-edit {
            background-color: #28a745;
            color: white;
            border: none;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
        }

        .btn-toggle {
            background-color: #6c757d;
            color: white;
            border: none;
        }

        .btn-password {
            background-color: #17a2b8;
            color: white;
            border: none;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            border-radius: 8px 8px 0 0;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
        }

        .form-control, .form-select {
            border-radius: 5px;
            border: 1px solid #ced4da;
        }

        /* Accessibility Focus Styles */
        .btn:focus, .form-control:focus, .form-select:focus {
            outline: 2px solid #0056b3;
            outline-offset: 2px;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .user-management-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .user-summary-card h3 {
                font-size: 24px;
            }
        }

        /* Screen Reader Only Text */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }
    </style>

    <div class="user-management-container">
    <div class="user-management-header">
        <h1 class="user-management-title">User Management Dashboard</h1>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="fas fa-plus-circle"></i> Create New User
            </button>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

        <!-- Success/Error Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="user-summary-card">
                    <p>Total Users</p>
                    <h3><?= isset($widgets['users']) ? (int)$widgets['users'] : 0 ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="user-summary-card" style="border-left: 4px solid #28a745;">
                    <p>Active Users</p>
                    <h3><?= isset($widgets['active_users']) ? (int)$widgets['active_users'] : 0 ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="user-summary-card" style="border-left: 4px solid #dc3545;">
                    <p>Inactive Users</p>
                    <h3><?= isset($widgets['inactive_users']) ? (int)$widgets['inactive_users'] : 0 ?></h3>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="user-search-container">
            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">
                    <form id="userSearchForm">
                        <div class="input-group">
                            <input type="text" id="userSearchInput" class="form-control user-search-input"
                                   placeholder="Search users by name, email, or role..."
                                   aria-label="Search users">
                            <button class="btn user-search-btn" type="submit" aria-label="Search">
                                <i class="fas fa-search"></i> <span class="sr-only">Search</span>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <select id="roleFilter" class="form-select" aria-label="Filter by role">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="teacher">Teacher</option>
                            <option value="student">Student</option>
                        </select>
                        <select id="statusFilter" class="form-select" aria-label="Filter by status">
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="users-table">
            <h3 class="mb-3">User Management</h3>
            <div class="table-responsive">
                <table class="table table-hover" id="usersTable">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Role</th>
                            <th scope="col">Status</th>
                            <th scope="col">Created</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($allUsers) && !empty($allUsers)): ?>
                            <?php foreach ($allUsers as $user): ?>
                                <tr data-user-id="<?= esc($user['id']) ?>"
                                    data-user-name="<?= esc($user['name']) ?>"
                                    data-user-email="<?= esc($user['email']) ?>"
                                    data-user-role="<?= esc($user['role']) ?>"
                                    data-user-status="<?= esc($user['status']) ?>">
                                    <td><?= esc($user['id']) ?></td>
                                    <td><?= esc($user['name']) ?></td>
                                    <td><?= esc($user['email']) ?></td>
                                    <td>
                                        <span class="role-badge role-<?= esc($user['role']) ?>">
                                            <?= esc($user['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= esc($user['status']) ?>">
                                            <?= esc($user['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= esc(date('M j, Y', strtotime($user['created_at']))) ?></td>
                                    <td>
                                        <?php if ($user['is_protected'] != 1): ?>
                                            <button class="btn btn-action btn-edit edit-user-btn"
                                                    data-user-id="<?= esc($user['id']) ?>"
                                                    data-user-name="<?= esc($user['name']) ?>"
                                                    data-user-email="<?= esc($user['email']) ?>"
                                                    data-user-role="<?= esc($user['role']) ?>"
                                                    data-user-status="<?= esc($user['status']) ?>"
                                                    title="Edit user">
                                                <i class="fas fa-edit"></i> <span class="sr-only">Edit</span>
                                            </button>
                                            <button class="btn btn-action btn-password change-password-btn"
                                                    data-user-id="<?= esc($user['id']) ?>"
                                                    data-user-name="<?= esc($user['name']) ?>"
                                                    title="Change password">
                                                <i class="fas fa-key"></i> <span class="sr-only">Change Password</span>
                                            </button>
                                            <?php if ($user['status'] === 'active'): ?>
                                                <button class="btn btn-action btn-toggle deactivate-user-btn"
                                                        data-user-id="<?= esc($user['id']) ?>"
                                                        data-user-name="<?= esc($user['name']) ?>"
                                                        title="Deactivate user">
                                                    <i class="fas fa-user-minus"></i> <span class="sr-only">Deactivate</span>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-action btn-toggle activate-user-btn"
                                                        data-user-id="<?= esc($user['id']) ?>"
                                                        data-user-name="<?= esc($user['name']) ?>"
                                                        title="Activate user">
                                                    <i class="fas fa-user-plus"></i> <span class="sr-only">Activate</span>
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-secondary" title="Protected admin account">
                                                <i class="fas fa-shield-alt"></i> Protected
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create User Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createUserForm">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="createName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="createName" name="name" required
                                   aria-describedby="nameHelp">
                            <div id="nameHelp" class="form-text">Enter the user's full name</div>
                        </div>

                        <div class="mb-3">
                            <label for="createEmail" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="createEmail" name="email" required
                                   aria-describedby="emailHelp">
                            <div id="emailHelp" class="form-text">Enter a valid email address</div>
                        </div>

                        <div class="mb-3">
                            <label for="createPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="createPassword" name="password" required
                                   aria-describedby="passwordHelp">
                            <div id="passwordHelp" class="form-text">
                                Password must contain at least 8 characters, including uppercase, lowercase, number, and special character
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="createRole" class="form-label">Role</label>
                            <select class="form-select" id="createRole" name="role" required
                                    aria-describedby="roleHelp">
                                <option value="">Select Role</option>
                                <option value="student">Student</option>
                                <option value="teacher">Teacher</option>
                                <option value="admin">Admin</option>
                            </select>
                            <div id="roleHelp" class="form-text">Select the user's role</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editUserForm">
                    <?= csrf_field() ?>
                    <input type="hidden" id="editUserId" name="user_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required readonly>
                        </div>

                        <div class="mb-3">
                            <label for="editRole" class="form-label">Role</label>
                            <select class="form-select" id="editRole" name="role" required>
                                <option value="student">Student</option>
                                <option value="teacher">Teacher</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="changePasswordForm">
                    <?= csrf_field() ?>
                    <input type="hidden" id="changePasswordUserId" name="user_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="password" required
                                   aria-describedby="newPasswordHelp">
                            <div id="newPasswordHelp" class="form-text">
                                Password must contain at least 8 characters, including uppercase, lowercase, number, and special character
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Create user form submission
    $('#createUserForm').submit(function(e) {
        e.preventDefault();

        // Validate password
        const password = $('#createPassword').val();
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

        if (!passwordRegex.test(password)) {
            alert('Password must contain at least 8 characters, including uppercase, lowercase, number, and special character');
            return;
        }

        $.ajax({
            url: '<?= base_url('users/create') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('User created successfully!');
                    $('#createUserModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + (response.message || 'Failed to create user'));
                }
            },
            error: function(xhr, status, error) {
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    let errorMessage = 'Validation errors:\n';
                    for (const field in response.errors) {
                        errorMessage += '- ' + response.errors[field] + '\n';
                    }
                    alert(errorMessage);
                } else {
                    alert('Error: ' + (response ? response.message : 'Failed to create user'));
                }
            }
        });
    });

    // Edit user button click handler
    $(document).on('click', '.edit-user-btn', function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        const userEmail = $(this).data('user-email');
        const userRole = $(this).data('user-role');
        const userStatus = $(this).data('user-status');

        $('#editUserId').val(userId);
        $('#editName').val(userName);
        $('#editEmail').val(userEmail);
        $('#editRole').val(userRole);
        $('#editStatus').val(userStatus);

        $('#editUserModal').modal('show');
    });

    // Edit user form submission
    $('#editUserForm').submit(function(e) {
        e.preventDefault();

        const userId = $('#editUserId').val();
        const newName = $('#editName').val();
        const newRole = $('#editRole').val();
        const newStatus = $('#editStatus').val();

        $.ajax({
            url: '<?= base_url('users/update') ?>',
            type: 'POST',
            data: {
                user_id: userId,
                name: newName,
                role: newRole,
                status: newStatus,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('User updated successfully!');
                    $('#editUserModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + (response.message || 'Failed to update user'));
                }
            },
            error: function(xhr, status, error) {
                const response = xhr.responseJSON;
                alert('Error: ' + (response ? response.message : 'Failed to update user'));
            }
        });
    });

    // Change password button click handler
    $(document).on('click', '.change-password-btn', function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');

        $('#changePasswordUserId').val(userId);
        $('#changePasswordModalLabel').text('Change Password for ' + userName);

        $('#changePasswordModal').modal('show');
    });

    // Change password form submission
    $('#changePasswordForm').submit(function(e) {
        e.preventDefault();

        const password = $('#newPassword').val();
        const confirmPassword = $('#confirmPassword').val();

        if (password !== confirmPassword) {
            alert('Passwords do not match');
            return;
        }

        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

        if (!passwordRegex.test(password)) {
            alert('Password must contain at least 8 characters, including uppercase, lowercase, number, and special character');
            return;
        }

        $.ajax({
            url: '<?= base_url('users/updatePassword') ?>',
            type: 'POST',
            data: {
                user_id: $('#changePasswordUserId').val(),
                password: password,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Password changed successfully!');
                    $('#changePasswordModal').modal('hide');
                } else {
                    alert('Error: ' + (response.message || 'Failed to change password'));
                }
            },
            error: function(xhr, status, error) {
                const response = xhr.responseJSON;
                alert('Error: ' + (response ? response.message : 'Failed to change password'));
            }
        });
    });

    // Deactivate user button click handler
    $(document).on('click', '.deactivate-user-btn', function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');

        if (confirm('Are you sure you want to deactivate user: ' + userName + '?')) {
            $.ajax({
                url: '<?= base_url('users/toggleStatus') ?>',
                type: 'POST',
                data: {
                    user_id: userId,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('User deactivated successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Failed to deactivate user'));
                    }
                },
                error: function(xhr, status, error) {
                    const response = xhr.responseJSON;
                    alert('Error: ' + (response ? response.message : 'Failed to deactivate user'));
                }
            });
        }
    });

    // Activate user button click handler
    $(document).on('click', '.activate-user-btn', function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');

        if (confirm('Are you sure you want to activate user: ' + userName + '?')) {
            $.ajax({
                url: '<?= base_url('users/toggleStatus') ?>',
                type: 'POST',
                data: {
                    user_id: userId,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('User activated successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Failed to activate user'));
                    }
                },
                error: function(xhr, status, error) {
                    const response = xhr.responseJSON;
                    alert('Error: ' + (response ? response.message : 'Failed to activate user'));
                }
            });
        }
    });

    // User search functionality
    $('#userSearchForm').submit(function(e) {
        e.preventDefault();
        filterUsers();
    });

    // Filter change handlers
    $('#roleFilter, #statusFilter').change(function() {
        filterUsers();
    });

    function filterUsers() {
        const searchTerm = $('#userSearchInput').val().toLowerCase();
        const roleFilter = $('#roleFilter').val();
        const statusFilter = $('#statusFilter').val();

        $('#usersTable tbody tr').each(function() {
            const row = $(this);
            const name = row.data('user-name').toLowerCase();
            const email = row.data('user-email').toLowerCase();
            const role = row.data('user-role');
            const status = row.data('user-status');

            const matchesSearch = name.includes(searchTerm) ||
                                email.includes(searchTerm) ||
                                role.includes(searchTerm);

            const matchesRole = !roleFilter || role === roleFilter;
            const matchesStatus = !statusFilter || status === statusFilter;

            if (matchesSearch && matchesRole && matchesStatus) {
                row.show();
            } else {
                row.hide();
            }
        });
    }

    // Keyboard navigation for accessibility
    $(document).on('keydown', function(e) {
        // Add keyboard navigation for modals and buttons
        if (e.key === 'Escape') {
            // Close any open modals
            $('.modal').modal('hide');
        }
    });
});
</script>
<?= $this->endSection() ?>
<task_progress>
- [x] Analyze current user management system
- [x] Identify accessibility issues
- [x] Implement accessibility improvements
- [ ] Test the changes
</task_progress>
</write_to_file>
