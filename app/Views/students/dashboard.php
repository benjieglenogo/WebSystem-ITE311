<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
    <!-- Student Dashboard - Course Enrollment -->
    <style>
        /* Professional Student Dashboard Styles */
        .student-container {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 20px;
        }

        .student-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .student-title {
            color: #343a40;
            font-weight: 600;
        }

        /* Course Cards */
        .course-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .course-card-body {
            padding: 20px;
            flex: 1;
        }

        .course-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 10px;
        }

        .course-code {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .course-description {
            color: #495057;
            font-size: 0.95rem;
            margin-bottom: 15px;
            flex: 1;
        }

        .course-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 0.85rem;
            color: #6c757d;
        }

        .btn-enroll {
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }

        .btn-enroll:hover {
            background-color: #218838;
        }

        .btn-enrolled {
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 500;
            cursor: not-allowed;
        }

        .btn-view {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 500;
            margin-left: 10px;
        }

        /* Search Bar */
        .search-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .search-input {
            border-radius: 25px;
            border: 1px solid #ced4da;
            padding-left: 15px;
        }

        .search-btn {
            border-radius: 25px;
            background-color: #007bff;
            border: none;
        }

        /* Enrolled Courses Section */
        .enrolled-courses {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .enrolled-course-item {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .enrolled-course-item:last-child {
            border-bottom: none;
        }

        .enrolled-course-name {
            font-weight: 500;
            color: #343a40;
        }

        .enrolled-course-code {
            color: #6c757d;
            font-size: 0.9rem;
        }

        /* Notification Styles */
        .notification-dropdown {
            min-width: 300px;
        }

        .notification-item {
            padding: 10px 15px;
            border-bottom: 1px solid #f1f1f1;
            cursor: pointer;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .notification-item.unread {
            background-color: #e3f2fd;
            border-left: 4px solid #007bff;
        }

        .notification-message {
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .notification-time {
            font-size: 0.8rem;
            color: #6c757d;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .student-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .course-meta {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>

    <div class="student-container">
        <div class="student-header">
            <h1 class="student-title">My Courses Dashboard</h1>
            <div class="d-flex align-items-center gap-2">
                <!-- Notification Bell -->
                <div class="dropdown">
                    <button class="btn btn-outline-primary position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                        <i class="bi bi-bell-fill"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-count" style="display: none;"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><div class="px-3 py-2 text-muted small notification-content">Loading...</div></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">View all notifications</a></li>
                    </ul>
                </div>
                <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">Back to Dashboard</a>
            </div>
        </div>

        <!-- Error Message -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-info">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Success Message -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <!-- Enrolled Courses Section -->
        <div class="enrolled-courses">
            <h3 class="mb-3">My Enrolled Courses</h3>

            <?php if (isset($enrolledCourses) && !empty($enrolledCourses)): ?>
                <div class="row">
                    <?php foreach ($enrolledCourses as $course): ?>
                        <div class="col-md-4 mb-4">
                            <div class="course-card">
                                <div class="course-card-body">
                                    <div class="course-title"><?= esc($course['course_name'] ?? 'N/A') ?></div>
                                    <div class="course-code"><?= esc($course['course_code'] ?? 'N/A') ?></div>
                                    <div class="course-description"><?= esc($course['description'] ?? 'No description available.') ?></div>

                                    <div class="course-meta">
                                        <span><?= esc($course['teacher_name'] ?? 'Unassigned') ?></span>
                                        <span><?= esc($course['schedule'] ?? 'N/A') ?></span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="<?= base_url('materials/course/' . esc($course['course_id'])) ?>" class="btn btn-view">
                                            <i class="bi bi-folder"></i> View Materials
                                        </a>
                                        <span class="badge bg-success">Enrolled</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    You are not currently enrolled in any courses.
                </div>
            <?php endif; ?>
        </div>

        <!-- Available Courses Section -->
        <div class="enrolled-courses">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">Available Courses for Enrollment</h3>
            </div>

            <!-- Search Bar -->
            <div class="search-container mb-3">
                <form id="courseSearchForm" class="d-flex">
                    <input type="text" class="form-control search-input me-2" id="searchInput" placeholder="Search courses...">
                    <button type="submit" class="btn btn-primary search-btn">
                        <i class="bi bi-search"></i> Search
                    </button>
                </form>
            </div>

            <div class="row" id="coursesContainer">
                <?php if (isset($availableCourses) && !empty($availableCourses)): ?>
                    <?php foreach ($availableCourses as $course): ?>
                        <div class="col-md-4 mb-4">
                            <div class="course-card">
                                <div class="course-card-body">
                                    <div class="course-title"><?= esc($course['course_name'] ?? 'N/A') ?></div>
                                    <div class="course-code"><?= esc($course['course_code'] ?? 'N/A') ?></div>
                                    <div class="course-description"><?= esc($course['description'] ?? 'No description available.') ?></div>

                                    <div class="course-meta">
                                        <span><?= esc($course['teacher_name'] ?? 'Unassigned') ?></span>
                                        <span><?= esc($course['schedule'] ?? 'N/A') ?></span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <button class="btn btn-enroll enroll-btn"
                                                data-course-id="<?= esc($course['id']) ?>"
                                                data-course-name="<?= esc($course['course_name']) ?>">
                                            <i class="bi bi-plus"></i> Enroll
                                        </button>
                                        <a href="<?= base_url('materials/course/' . esc($course['id'])) ?>" class="btn btn-view">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            No available courses for enrollment at this time.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Enrollment Confirmation Modal -->
    <div class="modal fade" id="enrollmentModal" tabindex="-1" aria-labelledby="enrollmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="enrollmentModalLabel">Confirm Enrollment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to enroll in <strong id="modalCourseName"></strong>?</p>
                    <p>Once enrolled, you will have access to course materials and resources.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmEnrollBtn">Confirm Enrollment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="successMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="errorMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[title]').tooltip();

    // Enroll button click handler
    $(document).on('click', '.enroll-btn', function() {
        var courseId = $(this).data('course-id');
        var courseName = $(this).data('course-name');

        $('#modalCourseName').text(courseName);
        $('#confirmEnrollBtn').data('course-id', courseId);

        $('#enrollmentModal').modal('show');
    });

    // Confirm enrollment
    $('#confirmEnrollBtn').click(function() {
        var courseId = $(this).data('course-id');
        var $enrollBtn = $('.enroll-btn[data-course-id="' + courseId + '"]');

        $.post('<?= base_url('course/enroll') ?>', {
            course_id: courseId,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        })
        .done(function(response) {
            if (response.success) {
                // Show success modal
                $('#successMessage').text(response.message);
                $('#enrollmentModal').modal('hide');
                $('#successModal').modal('show');

                // Update enrolled courses section without reload
                $('#successModal').on('hidden.bs.modal', function() {
                    refreshEnrolledCourses();
                    loadNotifications(); // Refresh notifications to see enrollment notification
                    $enrollBtn.closest('.col-md-4').remove(); // Remove enrolled course from available list
                });
            } else {
                // Show error modal
                $('#errorMessage').text('Error: ' + response.message);
                $('#enrollmentModal').modal('hide');
                $('#errorModal').modal('show');
            }
        })
        .fail(function() {
            // Show error modal for AJAX failure
            $('#errorMessage').text('An error occurred while processing your enrollment. Please try again.');
            $('#enrollmentModal').modal('hide');
            $('#errorModal').modal('show');
        });
    });

    // Course search functionality
    $('#courseSearchForm').submit(function(e) {
        e.preventDefault();
        var searchTerm = $('#searchInput').val();

        $.ajax({
            url: '<?= base_url('courses/search') ?>',
            type: 'GET',
            data: { search_term: searchTerm },
            success: function(data) {
                var $coursesContainer = $('#coursesContainer');
                $coursesContainer.empty();

                if (data.length > 0) {
                    data.forEach(function(course) {
                        var courseCard = `
                            <div class="col-md-4 mb-4">
                                <div class="course-card">
                                    <div class="course-card-body">
                                        <div class="course-title">${course.course_name || 'N/A'}</div>
                                        <div class="course-code">${course.course_code || 'N/A'}</div>
                                        <div class="course-description">${course.description || 'No description available.'}</div>

                                        <div class="course-meta">
                                            <span>${course.teacher_name || 'Unassigned'}</span>
                                            <span>${course.schedule || 'N/A'}</span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <button class="btn btn-enroll enroll-btn"
                                                    data-course-id="${course.id}"
                                                    data-course-name="${course.course_name}">
                                                <i class="bi bi-plus"></i> Enroll
                                            </button>
                                            <a href="/materials/course/${course.id}" class="btn btn-view">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        $coursesContainer.append(courseCard);
                    });
                } else {
                    $coursesContainer.html('<div class="col-12 text-center"><div class="alert alert-info">No courses found matching your search.</div></div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                $('#coursesContainer').html('<div class="col-12 text-center"><div class="alert alert-danger">An error occurred while searching. Please try again.</div></div>');
            }
        });
    });

    // Clear search when input is cleared
    $('#searchInput').on('input', function() {
        if ($(this).val() === '') {
            // Reload all courses
            location.reload();
        }
    });

    // Refresh enrolled courses section
    function refreshEnrolledCourses() {
        $.get('<?= base_url('course/enrolled') ?>')
        .done(function(enrolledCourses) {
            var $enrolledCoursesSection = $('.enrolled-courses h3:contains("My Enrolled Courses")').closest('.enrolled-courses');
            var $courseRow = $enrolledCoursesSection.find('.row');

            $courseRow.empty(); // Clear existing courses

            if (enrolledCourses.length > 0) {
                enrolledCourses.forEach(function(course) {
                    var courseCard = `
                        <div class="col-md-4 mb-4">
                            <div class="course-card">
                                <div class="course-card-body">
                                    <div class="course-title">${course.course_name || 'N/A'}</div>
                                    <div class="course-code">${course.course_code || 'N/A'}</div>
                                    <div class="course-description">${course.description || 'No description available.'}</div>

                                    <div class="course-meta">
                                        <span>${course.teacher_name || 'Unassigned'}</span>
                                        <span>${course.schedule || 'N/A'}</span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="/materials/course/${course.course_id}" class="btn btn-view">
                                            <i class="bi bi-folder"></i> View Materials
                                        </a>
                                        <span class="badge bg-success">Enrolled</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $courseRow.append(courseCard);
                });
            } else {
                $courseRow.html('<div class="alert alert-info">You are not currently enrolled in any courses.</div>');
            }
        })
        .fail(function() {
            console.log('Failed to refresh enrolled courses');
        });
    }

    // Load notifications
    function loadNotifications() {
        $.get('<?= base_url('notifications/get') ?>')
        .done(function(data) {
            if (data.success) {
                updateNotificationBadge(data.unread_count);
                updateNotificationDropdown(data.notifications);
            }
        })
        .fail(function() {
            console.log('Failed to load notifications');
        });
    }

    function updateNotificationBadge(count) {
        var $badge = $('.notification-count');
        if (count > 0) {
            $badge.text(count > 99 ? '99+' : count).show();
        } else {
            $badge.hide();
        }
    }

    function updateNotificationDropdown(notifications) {
        var $content = $('.notification-content');
        $content.empty();

        if (notifications.length > 0) {
            notifications.forEach(function(notification) {
                var itemClass = notification.is_read == 0 ? 'unread' : '';
                var itemHtml = `
                    <div class="notification-item ${itemClass}" data-id="${notification.id}">
                        <div class="notification-message">${notification.message}</div>
                        <div class="notification-time">${formatDate(notification.created_at)}</div>
                    </div>
                `;
                $content.append(itemHtml);
            });
        } else {
            $content.html('<em>No new notifications</em>');
        }
    }

    function formatDate(dateString) {
        var date = new Date(dateString);
        var now = new Date();
        var diff = now - date;
        var minutes = Math.floor(diff / 60000);

        if (minutes < 1) return 'Just now';
        if (minutes < 60) return minutes + ' minutes ago';
        if (minutes < 1440) return Math.floor(minutes / 60) + ' hours ago';
        return Math.floor(minutes / 1440) + ' days ago';
    }

    // Load notifications on page load
    loadNotifications();

    // Refresh notifications every 30 seconds
    setInterval(loadNotifications, 30000);

    // Mark notification as read on click
    $(document).on('click', '.notification-item', function() {
        var notificationId = $(this).data('id');
        $(this).removeClass('unread');
        $.post('<?= base_url('notifications/mark_as_read/') ?>' + notificationId)
        .done(function(data) {
            if (data.success) {
                loadNotifications(); // Refresh
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
<task_progress>
- [x] Analyze current code structure
- [x] Examine existing course management files
- [x] Identify issues in routes, controllers, models, and views
- [ ] Fix enrollment functionality
- [ ] Test the implementation
</task_progress>
</write_to_file>
