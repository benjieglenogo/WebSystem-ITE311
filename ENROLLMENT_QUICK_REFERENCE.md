═══════════════════════════════════════════════════════════════════════════════════
                    ENROLLMENT APPROVAL WORKFLOW - QUICK REFERENCE
═══════════════════════════════════════════════════════════════════════════════════

🎯 SYSTEM FLOW
───────────────────────────────────────────────────────────────────────────────────

STUDENT PERSPECTIVE:
┌─────────────────────────────────────────────────────────────┐
│ 1. Browse Available Courses                                 │
│    • Go to /courses                                         │
│    • See all available courses                              │
│                                                             │
│ 2. Click Enroll Button                                      │
│    • POST to /course/enroll                                 │
│    • Enrollment created with status = 'pending'             │
│                                                             │
│ 3. See Confirmation Message                                │
│    • "Enrollment request submitted. Approval required."     │
│                                                             │
│ 4. Wait for Approval                                        │
│    • Receives notification about pending request            │
│    • Cannot access course content until approved            │
│    • May receive rejection with reason                      │
│                                                             │
│ 5. Once Approved                                            │
│    • Receives approval notification                         │
│    • Can now access course content                          │
│    • May be unenrolled by teacher/admin anytime             │
└─────────────────────────────────────────────────────────────┘

TEACHER PERSPECTIVE:
┌─────────────────────────────────────────────────────────────┐
│ 1. Navigate to Enrollment Requests                          │
│    • Click "Enrollment Requests" in navigation              │
│    • Goes to /enrollment/pending                            │
│                                                             │
│ 2. View Two Tabs                                            │
│    • Tab 1: "Pending Requests" - New student requests       │
│    • Tab 2: "Enrolled Students" - Already approved          │
│                                                             │
│ 3. Manage Pending Requests                                  │
│    • Click APPROVE → Enrollment instant approved            │
│    • Click REJECT → Modal opens → Enter reason → Submit     │
│                                                             │
│ 4. Manage Enrolled Students                                 │
│    • Click UNENROLL → Confirmation modal → Delete           │
│    • Student immediately loses access                       │
│                                                             │
│ 5. All Actions Send Notifications                           │
│    • Approval: "Your enrollment has been approved!"         │
│    • Rejection: "Your enrollment has been rejected..."      │
│    • Unenroll: "You have been unenrolled from..."           │
└─────────────────────────────────────────────────────────────┘

ADMIN PERSPECTIVE:
┌─────────────────────────────────────────────────────────────┐
│ • Same as teacher BUT can manage ALL courses                │
│ • Can see pending requests from all teachers' courses       │
│ • Can see all enrolled students across entire system        │
└─────────────────────────────────────────────────────────────┘

═══════════════════════════════════════════════════════════════════════════════════

📍 URLS & ENDPOINTS
───────────────────────────────────────────────────────────────────────────────────

ENROLLMENT REQUESTS DASHBOARD:
  URL: http://localhost:8080/enrollment/pending
  Method: GET
  Access: Admin, Teacher (teachers see only their courses)
  Shows: Two tabs - pending requests + enrolled students

APPROVE ENROLLMENT:
  URL: http://localhost:8080/enrollment/{id}/approve
  Method: POST
  Returns: JSON {success: true/false, message: "..."}
  Authorization: Teacher (must own course), Admin (all)

REJECT ENROLLMENT:
  URL: http://localhost:8080/enrollment/{id}/reject
  Method: POST
  Params: rejection_reason (optional)
  Returns: JSON {success: true/false, message: "..."}
  Authorization: Teacher (must own course), Admin (all)

UNENROLL STUDENT:
  URL: http://localhost:8080/enrollment/{id}/unenroll
  Method: POST
  Returns: JSON {success: true/false, message: "..."}
  Authorization: Teacher (must own course), Admin (all)

═══════════════════════════════════════════════════════════════════════════════════

🗄️  DATABASE SCHEMA
───────────────────────────────────────────────────────────────────────────────────

ENROLLMENTS TABLE STRUCTURE:

Column              | Type           | Default      | Notes
────────────────────┼────────────────┼──────────────┼──────────────────────────
id                  | INT UNSIGNED   | AUTO_INC     | Primary Key
user_id             | INT UNSIGNED   | -            | Foreign Key → users.id
course_id           | INT UNSIGNED   | -            | Foreign Key → courses.id
enrollment_date     | DATETIME       | CURRENT_TS   | When student enrolled
status              | VARCHAR(20)    | 'pending'    | pending/approved/rejected
approved_by         | INT UNSIGNED   | NULL         | ID of approver (admin/teacher)
approved_at         | DATETIME       | NULL         | When approved
rejection_reason    | TEXT           | NULL         | Reason if rejected

ENROLLMENT STATUS VALUES:
  • 'pending'   - Waiting for approval, student cannot access course
  • 'approved'  - Student approved, can access course
  • 'rejected'  - Student rejected, cannot enroll again in this request

═══════════════════════════════════════════════════════════════════════════════════

🔑 KEY METHODS & USAGE
───────────────────────────────────────────────────────────────────────────────────

ENROLLMENTMODEL METHODS:

1. getPendingRequests($teacher_id = null, $course_id = null)
   Purpose: Get all pending enrollment requests
   Returns: Array of enrollment records with student/course info
   Usage:
   ```php
   $pendingRequests = $enrollmentModel->getPendingRequests(null, 5);
   // Gets pending requests for course ID 5
   ```

2. getApprovedEnrollments($teacher_id = null, $course_id = null)
   Purpose: Get all approved enrolled students
   Returns: Array of approved enrollment records
   Usage:
   ```php
   $students = $enrollmentModel->getApprovedEnrollments($teacherId);
   // Gets all approved students for a teacher's courses
   ```

3. approveEnrollment($enrollment_id, $approved_by)
   Purpose: Approve an enrollment request
   Returns: true/false
   Usage:
   ```php
   $enrollmentModel->approveEnrollment(42, $userId);
   // Approves enrollment 42 by user
   ```

4. rejectEnrollment($enrollment_id, $rejection_reason = null)
   Purpose: Reject an enrollment request
   Returns: true/false
   Usage:
   ```php
   $enrollmentModel->rejectEnrollment(42, "Class is full");
   ```

5. unenrollStudent($enrollment_id)
   Purpose: Remove student from course
   Returns: true/false
   Usage:
   ```php
   $enrollmentModel->unenrollStudent(42);
   // Deletes enrollment record, revokes access
   ```

6. getEnrollmentStatus($user_id, $course_id)
   Purpose: Check enrollment status for specific student/course
   Returns: Enrollment record or null
   Usage:
   ```php
   $status = $enrollmentModel->getEnrollmentStatus(10, 5);
   if ($status['status'] === 'approved') { /* Show course */ }
   ```

═══════════════════════════════════════════════════════════════════════════════════

📋 FEATURE CHECKLIST
───────────────────────────────────────────────────────────────────────────────────

STUDENT ENROLLMENT:
  ✅ When student clicks Enroll, creates enrollment with status = 'pending'
  ✅ Shows message: "Enrollment request submitted. Approval is required."
  ✅ Student receives notification about pending request
  ✅ Student cannot access course until approved

TEACHER/ADMIN APPROVAL WORKFLOW:
  ✅ Navigate to /enrollment/pending to view requests
  ✅ Two tabs: Pending Requests + Enrolled Students
  ✅ Approve button changes status to 'approved' instantly
  ✅ Reject button opens modal with optional reason field
  ✅ Unenroll button removes student and revokes access
  ✅ All actions send notifications to affected students

SECURITY:
  ✅ Login required for all enrollment operations
  ✅ Teachers can only manage their own courses' requests
  ✅ Admins can manage all enrollments
  ✅ CSRF token protection on all POST requests
  ✅ Role-based authorization checks

USER EXPERIENCE:
  ✅ Clear confirmation messages before destructive actions
  ✅ Modals for rejection and unenrollment
  ✅ Badge counters showing pending/enrolled counts
  ✅ Responsive tables for all screen sizes
  ✅ Color-coded status indicators

═══════════════════════════════════════════════════════════════════════════════════

🚀 QUICK START
───────────────────────────────────────────────────────────────────────────────────

AS A STUDENT:
  1. Go to http://localhost:8080/courses
  2. Click Enroll button on any course
  3. See confirmation: "Enrollment request submitted..."
  4. Wait for teacher/admin approval

AS A TEACHER:
  1. Click "Enrollment Requests" in navigation
  2. You'll see two tabs: Pending + Enrolled Students
  3. Pending tab shows students requesting to join your courses
  4. Click APPROVE to instantly approve (sends notification)
  5. Click REJECT to reject (modal opens for optional reason)
  6. Enrolled tab shows all your students currently in courses
  7. Click UNENROLL to remove student (sends notification)

AS AN ADMIN:
  1. Click "Enrollment Requests" in navigation
  2. Manage ALL pending requests across ALL courses
  3. Manage ALL enrolled students across ALL courses

═══════════════════════════════════════════════════════════════════════════════════

⚠️  IMPORTANT NOTES
───────────────────────────────────────────────────────────────────────────────────

• ENROLLMENT REQUESTS ARE REQUIRED: 
  Students cannot immediately enroll. All enrollments start as 'pending'.

• TEACHERS MUST APPROVE:
  Students cannot access course content until teacher/admin approves.

• UNENROLL IS IMMEDIATE:
  When unenrolled, student loses access to course right away.

• NOTIFICATIONS ARE SENT:
  Students receive notifications for:
    - Pending request submission
    - Approval
    - Rejection (with reason if provided)
    - Unenrollment

• ONLY ADMINS/TEACHERS CAN ACCESS:
  The /enrollment/pending dashboard is restricted to Admin/Teacher roles.

═══════════════════════════════════════════════════════════════════════════════════
