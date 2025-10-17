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

// Role-specific dashboards
$routes->group('admin', ['filter' => 'roleAuth'], function ($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
});

$routes->group('teacher', ['filter' => 'roleAuth'], function ($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
});

// Course enrollment
$routes->post('/course/enroll', 'Course::enroll');
=======
// Course enrollment
$routes->post('/course/enroll', 'Course::enroll');
>>>>>>> 35010129780f3685d0f327c9a20c364cf83dd5e6
=======
// Announcements
$routes->get('/announcements', 'Announcement::index');

// Role-specific dashboards
$routes->group('admin', ['filter' => 'roleAuth'], function ($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
});

$routes->group('teacher', ['filter' => 'roleAuth'], function ($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
});

// Course enrollment
$routes->post('/course/enroll', 'Course::enroll');
=======
// Announcements
$routes->get('/announcements', 'Announcement::index');

// Role-specific dashboards
$routes->group('admin', ['filter' => 'roleAuth'], function ($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
});

$routes->group('teacher', ['filter' => 'roleAuth'], function ($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
});

// Course enrollment
$routes->post('/course/enroll', 'Course::enroll');
=======
// Course enrollment
$routes->post('/course/enroll', 'Course::enroll');
>>>>>>> 35010129780f3685d0f327c9a20c364cf83dd5e6
