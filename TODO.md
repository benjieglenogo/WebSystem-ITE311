# TODO List for Online Student Portal Enhancements

## Task 1: Announcements Module
- [x] Create Announcement.php controller with index() method
- [x] Create announcements.php view to display announcements list
- [x] Add route for /announcements

## Task 2: Database Schema and Data Population
- [x] Create migration CreateAnnouncementsTable.php
- [x] Create AnnouncementModel.php
- [x] Update Announcement controller to use model for fetching data
- [x] Create AnnouncementSeeder.php with sample data
- [x] Run migration and seeder

## Task 3: Enhanced Authentication and Role-Based Redirection
- [x] Modify Auth::login() for role-based redirection
- [x] Create Teacher.php controller with dashboard() method
- [x] Create Admin.php controller with dashboard() method
- [x] Create teacher_dashboard.php view
- [x] Create admin_dashboard.php view
- [x] Add routes for /teacher/dashboard and /admin/dashboard

## Task 4: Implementing a Filter for Authorization
- [x] Create RoleAuth.php filter
- [x] Register RoleAuth filter in Filters.php
- [x] Apply filter to /admin/* and /teacher/* route groups in Routes.php
