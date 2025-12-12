<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default route
$routes->get('/', 'Home::index');

// Custom routes
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

/// Auth & Dashboard
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Auth::dashboard');
// Registration
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');

// Announcements
$routes->get('/announcements', 'Announcement::index');

// Role-specific dashboards - commented out until controllers are created
// $routes->group('admin', ['filter' => 'roleAuth'], function ($routes) {
//     $routes->get('dashboard', 'Admin::dashboard');
// });

// $routes->group('teacher', ['filter' => 'roleAuth'], function ($routes) {
//     $routes->get('dashboard', 'Teacher::dashboard');
// });

// Course enrollment
$routes->post('/course/enroll', 'Course::enroll');
$routes->get('/course/enrolled', 'Course::getEnrolledCourses');
// Course search (GET/POST) - AJAX or regular
$routes->get('/courses/search', 'Course::search');
$routes->post('/courses/search', 'Course::search');

// Courses page should render the index view (not the search JSON)
$routes->get('/courses', 'Course::index');

// Materials
$routes->get('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->get('/teacher/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/teacher/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/materials/ajax-upload', 'Materials::ajaxUpload');
$routes->get('/materials/course/(:num)', 'Materials::display/$1');
$routes->get('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->post('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('/materials/download/(:num)', 'Materials::download/$1');
$routes->post('/materials/forward', 'Materials::forward');

// Notifications
$routes->get('/notifications/get', 'Notifications::get');
$routes->post('/notifications/mark_as_read/(:num)', 'Notifications::mark_as_read/$1');

// Users Management (Admin only)
$routes->get('/users', 'Users::index');
$routes->get('/users/management', 'Auth::userManagement');
$routes->post('/users/create', 'Users::create');
$routes->post('/users/updateRole', 'Users::updateRole');
    $routes->post('/users/updatePassword', 'Users::updatePassword');
    $routes->post('/users/update', 'Users::update');
$routes->post('/users/toggleStatus', 'Users::toggleStatus');
$routes->post('/users/delete', 'Users::delete');

// Course Management (Admin only)
$routes->post('/courses/create', 'Course::create');
$routes->post('/courses/update', 'Course::update');
$routes->post('/courses/update-status', 'Course::updateStatus');
$routes->post('/courses/delete', 'Course::delete');
$routes->get('/courses/get/(:num)', 'Course::get/$1');
$routes->get('/courses/teachers', 'Course::getTeachers');

// Teacher - Manage Students
$routes->get('/teacher/students', 'Auth::manageStudents');
$routes->get('/teacher/students/get', 'Auth::getStudentsForCourse');
$routes->post('/teacher/students/update-status', 'Auth::updateStudentStatus');
$routes->post('/teacher/students/remove', 'Auth::removeStudentFromCourse');

// Teacher - Course Management
$routes->get('/teacher/course-management', 'Auth::courseManagement');
$routes->get('/teacher/course-management/get-students', 'Auth::getStudents');
$routes->post('/teacher/course-management/create-course', 'Auth::teacherCreateCourse');
$routes->post('/teacher/course-management/update-course', 'Auth::teacherUpdateCourse');
$routes->post('/teacher/course-management/delete-course', 'Auth::teacherDeleteCourse');
$routes->post('/teacher/course-management/update-status', 'Auth::updateStatus');
$routes->post('/teacher/course-management/remove', 'Auth::remove');

// Student - Course Enrollment Dashboard
$routes->get('/student/courses', 'Auth::studentCourses');
$routes->post('/student/enroll', 'Course::enroll');

// Test routes - remove in production
$routes->get('/test/enrollment', 'TestEnrollment::test');
$routes->get('/test/reset', 'TestEnrollment::reset');
