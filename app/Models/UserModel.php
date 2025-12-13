<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'is_protected',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = false; // timestamps handled by DB defaults in migration

    /**
     * Lock a user account by setting status to 'inactive'
     * @param int $userId
     * @return bool
     */
    public function lockAccount($userId)
    {
        // Check if user is protected (admin accounts)
        $user = $this->find($userId);
        if ($user && isset($user['is_protected']) && $user['is_protected'] == 1) {
            return false; // Cannot lock protected accounts
        }

        return $this->update($userId, ['status' => 'inactive']);
    }

    /**
     * Unlock a user account by setting status to 'active'
     * @param int $userId
     * @return bool
     */
    public function unlockAccount($userId)
    {
        return $this->update($userId, ['status' => 'active']);
    }

    /**
     * Check if a user account is locked
     * @param int $userId
     * @return bool
     */
    public function isAccountLocked($userId)
    {
        $user = $this->find($userId);
        return $user && isset($user['status']) && $user['status'] === 'inactive';
    }

    /**
     * Protect an admin account from being locked
     * @param int $userId
     * @return bool
     */
    public function protectAccount($userId)
    {
        return $this->update($userId, ['is_protected' => 1]);
    }

    /**
     * Unprotect an account
     * @param int $userId
     * @return bool
     */
    public function unprotectAccount($userId)
    {
        return $this->update($userId, ['is_protected' => 0]);
    }

    /**
     * Automatically protect admin users by setting is_protected = 1
     * This ensures admin roles cannot be edited
     * @param int $userId
     * @return bool
     */
    public function protectAdminUsers()
    {
        // Find all admin users
        $adminUsers = $this->where('role', 'admin')->findAll();

        foreach ($adminUsers as $adminUser) {
            if ($adminUser['is_protected'] != 1) {
                $this->update($adminUser['id'], ['is_protected' => 1]);
            }
        }

        return true;
    }

    /**
     * Check if a user is an admin
     * @param int $userId
     * @return bool
     */
    public function isAdmin($userId)
    {
        $user = $this->find($userId);
        return $user && isset($user['role']) && $user['role'] === 'admin';
    }

    /**
     * Check if admin role can be edited (should always return false for admin users)
     * @param int $userId
     * @return bool
     */
    public function canEditRole($userId)
    {
        $user = $this->find($userId);
        if ($user && isset($user['role']) && $user['role'] === 'admin') {
            return false; // Admin roles cannot be edited
        }
        return true;
    }
}
