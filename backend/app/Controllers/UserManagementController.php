<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class UserManagementController extends ResourceController
{
    protected $format = 'json';

    public function suspend($id = null)
    {
        if (!$id) return $this->fail('User ID required');
        
        $db = \Config\Database::connect();
        $db->table('users')->where('id', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        
        $user = $db->table('users')->where('id', $id)->get()->getRowArray();
        $actionUserId = $this->request->getHeaderLine('X-User-Id');
        if ($actionUserId && $user) {
            \App\Models\ActivityLogModel::log($actionUserId, 'Suspend User', 'suspended user account: ' . $user['full_name']);
        }
        
        return $this->respond(['success' => true, 'message' => 'User suspended']);
    }

    public function restore($id = null)
    {
        if (!$id) return $this->fail('User ID required');
        
        $db = \Config\Database::connect();
        $db->table('users')->where('id', $id)->update(['deleted_at' => null]);
        
        $user = $db->table('users')->where('id', $id)->get()->getRowArray();
        $actionUserId = $this->request->getHeaderLine('X-User-Id');
        if ($actionUserId && $user) {
            \App\Models\ActivityLogModel::log($actionUserId, 'Restore User', 'restored user account: ' . $user['full_name']);
        }
        
        return $this->respond(['success' => true, 'message' => 'User restored']);
    }

    public function permanentlyDelete($id = null)
    {
        if (!$id) return $this->fail('User ID required');
        
        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $id)->get()->getRowArray();
        
        $db->table('users')->where('id', $id)->delete();
        $db->table('user_profiles')->where('user_id', $id)->delete();
        
        $actionUserId = $this->request->getHeaderLine('X-User-Id');
        if ($actionUserId && $user) {
            \App\Models\ActivityLogModel::log($actionUserId, 'Delete User', 'permanently deleted user account: ' . $user['full_name']);
        }
        
        return $this->respond(['success' => true, 'message' => 'User permanently deleted']);
    }
}
