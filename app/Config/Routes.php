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

// Materials
$routes->post('/materials/upload', 'Materials::upload');
$routes->get('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('/materials/download/(:num)', 'Materials::download/$1');

// Notifications
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');