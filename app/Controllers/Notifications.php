<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class Notifications extends BaseController
{
    /**
     * Get notifications for the current user (JSON response)
     * Returns unread count and list of notifications
     */
    public function get()
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please log in to view notifications.'
            ])->setStatusCode(401);
        }

        $userId = $session->get('userId');
        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User ID not found.'
            ])->setStatusCode(401);
        }

        $notificationModel = new NotificationModel();
        
        // Get unread count
        $unreadCount = $notificationModel->getUnreadCount($userId);
        
        // Get notifications list (latest 5)
        $notifications = $notificationModel->getNotificationsForUser($userId, 5);

        return $this->response->setJSON([
            'success' => true,
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark a notification as read
     * 
     * @param int $id Notification ID
     */
    public function mark_as_read($id = null)
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please log in to mark notifications as read.'
            ])->setStatusCode(401);
        }

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notification ID is required.'
            ])->setStatusCode(400);
        }

        $userId = $session->get('userId');
        $notificationModel = new NotificationModel();
        
        // Verify the notification belongs to the current user
        $notification = $notificationModel->find($id);
        
        if (!$notification) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notification not found.'
            ])->setStatusCode(404);
        }

        if ($notification['user_id'] != $userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to mark this notification as read.'
            ])->setStatusCode(403);
        }

        // Mark as read
        if ($notificationModel->markAsRead($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notification marked as read.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark notification as read.'
            ])->setStatusCode(500);
        }
    }
}
