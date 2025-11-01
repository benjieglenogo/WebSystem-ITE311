<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $this->renderSection('title') ?> - MyCI</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* Dark background with gradient */
    body {
      background: linear-gradient(135deg, #1e1e2f, #2d2d44);
      min-height: 100vh;
      font-family: 'Segoe UI', sans-serif;
      color: #e4e4e4;
    }

    /* Navbar */
    .navbar {
      background: #232339;
      box-shadow: 0 2px 10px rgba(0,0,0,0.5);
    }

    .navbar-brand {
      font-size: 1.6rem;
      font-weight: bold;
      color: #00d4ff;
    }

    .navbar .nav-link {
      color: #ccc;
      transition: color 0.3s ease;
    }

    .navbar .nav-link:hover {
      color: #00d4ff;
    }

    /* Page container (flat cards) */
    .container {
      margin-top: 70px;
      background: #2b2b40;
      padding: 30px 28px;
      border-radius: 12px;
      box-shadow: 0 6px 14px rgba(0,0,0,0.6);
    }

    /* Buttons */
    .btn-primary {
      background: linear-gradient(135deg, #00d4ff, #007bff);
      border: none;
      font-weight: 500;
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, #007bff, #0056d2);
    }

    /* Footer */
    footer {
      text-align: center;
      padding: 20px 0;
      margin-top: 50px;
      color: #aaa;
      font-size: 0.85rem;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="<?= site_url('/') ?>">MyCI</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <?php if (!session('isLoggedIn')): ?>
            <li class="nav-item"><a class="nav-link" href="<?= site_url('/') ?>">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= site_url('about') ?>">About</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= site_url('contact') ?>">Contact</a></li>
          <?php endif; ?>

          <?php if (session('isLoggedIn')): ?>
            <li class="nav-item"><a class="nav-link" href="<?= site_url('dashboard') ?>">Dashboard</a></li>
            <?php if (session('userRole') === 'admin'): ?>
              <li class="nav-item"><a class="nav-link" href="#">Admin Panel</a></li>
              <li class="nav-item"><a class="nav-link" href="#">User Management</a></li>
              <li class="nav-item"><a class="nav-link" href="#">System Reports</a></li>
            <?php elseif (session('userRole') === 'teacher'): ?>
              <li class="nav-item"><a class="nav-link" href="#">My Classes</a></li>
              <li class="nav-item"><a class="nav-link" href="#">Gradebook</a></li>
              <li class="nav-item"><a class="nav-link" href="#">Assignments</a></li>
            <?php elseif (session('userRole') === 'student'): ?>
              <li class="nav-item"><a class="nav-link" href="#">My Courses</a></li>
              <li class="nav-item"><a class="nav-link" href="#">Assignments</a></li>
              <li class="nav-item"><a class="nav-link" href="#">Grades</a></li>
            <?php endif; ?>
            
            <!-- Notifications Dropdown -->
            <?php if (session('isLoggedIn')): ?>
            <li class="nav-item dropdown">
              <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span style="font-size: 1.2rem;">🔔</span>
                <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" id="notificationBadge" style="display: none;">0</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" id="notificationDropdownMenu" style="min-width: 300px; max-height: 400px; overflow-y: auto;">
                <li><h6 class="dropdown-header">Notifications</h6></li>
                <li><hr class="dropdown-divider"></li>
                <li id="notificationList">
                  <div class="px-3 py-2 text-muted text-center">Loading notifications...</div>
                </li>
                <li id="noNotifications" style="display: none;">
                  <div class="px-3 py-2 text-muted text-center">No notifications</div>
                </li>
              </ul>
            </li>
            <?php endif; ?>
            
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                <?= esc(session('userName') ?? 'User') ?>
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="<?= site_url('dashboard') ?>">Profile</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?= site_url('logout') ?>">Logout</a></li>
              </ul>
            </li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="<?= site_url('login') ?>">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= site_url('register') ?>">Register</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Page Content -->
  <div class="container">
    <?= $this->renderSection('content') ?>
  </div>

  <!-- Footer -->
  <footer>
    &copy; <?= date('Y') ?> ITE-311 • All rights reserved.
  </footer>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Notification and Enrollment AJAX Scripts -->
  <script>
  $(document).ready(function() {
      // ==================== NOTIFICATION FUNCTIONS ====================
      
      // Function to fetch and update notifications
      function fetchNotifications() {
          $.get('<?= base_url('notifications') ?>')
              .done(function(response) {
                  if (response.success) {
                      updateNotificationBadge(response.unread_count);
                      updateNotificationList(response.notifications);
                  }
              })
              .fail(function() {
                  console.error('Failed to fetch notifications');
              });
      }

      // Function to update notification badge
      function updateNotificationBadge(count) {
          var badge = $('#notificationBadge');
          if (count > 0) {
              badge.text(count).show();
          } else {
              badge.hide();
          }
      }

      // Function to update notification list
      function updateNotificationList(notifications) {
          var list = $('#notificationList');
          var noNotifications = $('#noNotifications');
          
          list.empty();
          
          if (notifications && notifications.length > 0) {
              noNotifications.hide();
              
              $.each(notifications, function(index, notification) {
                  var isRead = notification.is_read == 1;
                  var alertClass = isRead ? 'alert-secondary' : 'alert-info';
                  var readButton = isRead ? '' : '<button class="btn btn-sm btn-outline-primary mark-read-btn" data-id="' + notification.id + '">Mark as Read</button>';
                  
                  var notificationHtml = `
                      <li class="px-3 py-2 border-bottom">
                          <div class="alert ${alertClass} mb-0 py-2">
                              <p class="mb-1 small">${escapeHtml(notification.message)}</p>
                              <small class="text-muted d-block mb-1">${formatDate(notification.created_at)}</small>
                              ${readButton ? '<div class="mt-2">' + readButton + '</div>' : ''}
                          </div>
                      </li>
                  `;
                  list.append(notificationHtml);
              });
          } else {
              list.empty();
              noNotifications.show();
          }
      }

      // Function to mark notification as read
      function markNotificationAsRead(notificationId) {
          $.post('<?= base_url('notifications/mark_read/') ?>' + notificationId)
              .done(function(response) {
                  if (response.success) {
                      // Remove the notification from list or update its state
                      $('.mark-read-btn[data-id="' + notificationId + '"]').closest('li').find('.alert')
                          .removeClass('alert-info')
                          .addClass('alert-secondary')
                          .find('.mark-read-btn')
                          .remove();
                      
                      // Refresh notification count
                      fetchNotifications();
                  } else {
                      alert('Failed to mark notification as read: ' + response.message);
                  }
              })
              .fail(function() {
                  alert('An error occurred while marking the notification as read.');
              });
      }

      // Event handler for mark as read button
      $(document).on('click', '.mark-read-btn', function() {
          var notificationId = $(this).data('id');
          markNotificationAsRead(notificationId);
      });

      // Fetch notifications on page load
      <?php if (session('isLoggedIn')): ?>
      fetchNotifications();
      
      // Optional: Fetch notifications every 60 seconds (real-time updates)
      setInterval(function() {
          fetchNotifications();
      }, 60000);
      <?php endif; ?>

      // Helper function to escape HTML
      function escapeHtml(text) {
          var map = {
              '&': '&amp;',
              '<': '&lt;',
              '>': '&gt;',
              '"': '&quot;',
              "'": '&#039;'
          };
          return text.replace(/[&<>"']/g, function(m) { return map[m]; });
      }

      // Helper function to format date
      function formatDate(dateString) {
          var date = new Date(dateString);
          var now = new Date();
          var diff = Math.floor((now - date) / 1000); // difference in seconds
          
          if (diff < 60) return 'Just now';
          if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
          if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
          if (diff < 604800) return Math.floor(diff / 86400) + ' days ago';
          
          return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
      }

      // ==================== ENROLLMENT FUNCTIONS ====================
      
      $('.enroll-btn').on('click', function(e) {
          e.preventDefault();

          var button = $(this);
          var courseId = button.data('course-id');
          var originalText = button.text();

          // Prevent double-clicking
          if (button.prop('disabled')) {
              return;
          }

          // Show loading state
          button.prop('disabled', true).text('Enrolling...');

          // Send AJAX request
          $.post('<?= base_url('course/enroll') ?>', {
              course_id: courseId
          })
          .done(function(response) {
              if (response.success) {
                  // Show success message
                  showAlert(response.message, 'success');

                  // Move course to enrolled section dynamically
                  moveCourseToEnrolled(button, courseId);

                  // Update counters
                  updateEnrollmentCounters();
                  
                  // Refresh notifications after enrollment
                  fetchNotifications();
              } else {
                  // Show error message
                  showAlert(response.message, 'danger');

                  // Reset button
                  button.prop('disabled', false).text(originalText);
              }
          })
          .fail(function() {
              // Show error message
              showAlert('An error occurred. Please try again.', 'danger');

              // Reset button
              button.prop('disabled', false).text(originalText);
          });
      });

      // Function to move course from available to enrolled section
      function moveCourseToEnrolled(button, courseId) {
          // Find the course card
          var courseCard = button.closest('.col-md-6');

          // Get course information
          var courseTitle = courseCard.find('.card-title').text();
          var courseDescription = courseCard.find('.card-text').text();

          // Create enrolled course HTML
          var enrolledCourseHtml = `
              <div class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                      <h6 class="mb-1 fw-semibold">${courseTitle}</h6>
                      <p class="mb-1 text-muted">${courseDescription}</p>
                      <small class="text-muted">Enrolled: ${new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</small>
                  </div>
                  <span class="badge bg-success rounded-pill">Enrolled</span>
              </div>
          `;

          // Add to enrolled courses section (if not empty)
          var enrolledSection = $('.list-group');
          if (enrolledSection.length > 0) {
              enrolledSection.append(enrolledCourseHtml);
          } else {
              // If no enrolled courses section exists, prepend it
              var enrolledContainer = $('h5:contains("My Enrolled Courses")').parent();
              enrolledContainer.append('<div class="list-group mt-3">' + enrolledCourseHtml + '</div>');
          }

          // Remove from available courses
          courseCard.fadeOut(300, function() {
              $(this).remove();

              // Check if no courses left in available section
              var availableSection = $('.row').hasClass('available-courses-container') ? $('.available-courses-container .row') : $('.row').not(':has(.list-group)');
              if (availableSection.children('.col-md-6').length === 0) {
                  availableSection.html('<div class="text-muted">No courses available for enrollment.</div>');
              }
          });

          // Update button state
          button.removeClass('btn-primary').addClass('btn-success')
               .prop('disabled', true).text('Enrolled');
      }

      // Function to update enrollment counters
      function updateEnrollmentCounters() {
          // Count enrolled courses
          var enrolledCount = $('.list-group .list-group-item').length;

          // Update the counter in stats section
          $('.col-md-4 h3').first().text(enrolledCount);

          // Update available courses count if needed
          var availableCount = $('.row .col-md-6').length;
          // You can add logic here to update other counters if needed
      }

      // Function to show Bootstrap alerts
      function showAlert(message, type) {
          var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                          message +
                          '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                          '</div>';

          $('.container').prepend(alertHtml);

          // Auto-dismiss after 5 seconds
          setTimeout(function() {
              $('.alert').fadeOut();
          }, 5000);
      }
  });
  </script>
</body>
</html>
