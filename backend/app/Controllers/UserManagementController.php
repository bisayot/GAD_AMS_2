<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class UserManagementController extends ResourceController
{
    protected $format = 'json';

    public function create()
    {
        $data = $this->request->getJSON(true) ?: $this->request->getPost();

        $rules = [
            'full_name' => 'required',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
            'user_role' => 'required',
            'office_id' => 'required|numeric'
        ];

        if (!$this->validateData($data, $rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $email = $data['email'];
        $username = strtolower(str_replace(' ', '_', explode('@', $email)[0]));
        
        $userModel = new \App\Models\UserModel();
        
        if ($userModel->findByIdentity($email)) {
            return $this->failResourceExists('A user with that email already exists');
        }

        $role = 'college'; 
        switch ($data['user_role']) {
            case 'Director': $role = 'admin'; break;
            case 'Staff': $role = 'gad_staff'; break;
            case 'TWG':
            case 'Non-TWG':
            default:
                $role = 'college'; break;
        }

        $userData = [
            'username' => $username,
            'email' => $email,
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $role,
            'full_name' => $data['full_name'],
            'office_id' => $data['office_id']
        ];

        if ($userModel->insert($userData)) {
            $newUserId = $userModel->insertID();

            $db = \Config\Database::connect();
            $db->table('user_profiles')->insert([
                'user_id' => $newUserId,
                'first_name' => '',
                'last_name' => '',
                'user_role' => $data['user_role'],
                'office_unit_id' => $data['office_id']
            ]);

            $actionUserId = $this->request->getHeaderLine('X-User-Id');
            if ($actionUserId) {
                \App\Models\ActivityLogModel::log($actionUserId, 'Register User', 'created a new user: ' . $data['full_name']);
            }

            return $this->respondCreated(['success' => true, 'message' => 'User created successfully.']);
        }

        return $this->fail('Unable to create user.');
    }

    public function update($id = null)
    {
        if (!$id) return $this->fail('User ID required');

        $data = $this->request->getJSON(true) ?: $this->request->getPost();

        $rules = [
            'full_name' => 'required',
            'email' => 'required|valid_email',
            'user_role' => 'required',
            'office_id' => 'required|numeric'
        ];

        if (!$this->validateData($data, $rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($id);
        if (!$user) return $this->failNotFound('User not found');

        if ($data['email'] !== $user['email']) {
            if ($userModel->findByIdentity($data['email'])) {
                return $this->failResourceExists('A user with that email already exists');
            }
        }

        $role = 'college'; 
        switch ($data['user_role']) {
            case 'Director': $role = 'admin'; break;
            case 'Staff': $role = 'gad_staff'; break;
            case 'TWG':
            case 'Non-TWG':
            default:
                $role = 'college'; break;
        }

        $updateData = [
            'email' => $data['email'],
            'full_name' => $data['full_name'],
            'role' => $role,
            'office_id' => $data['office_id']
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $userModel->update($id, $updateData);

        $db = \Config\Database::connect();
        $profileExists = $db->table('user_profiles')->where('user_id', $id)->countAllResults() > 0;
        
        $profileData = [
            'user_role' => $data['user_role'],
            'office_unit_id' => $data['office_id']
        ];

        if ($profileExists) {
            $db->table('user_profiles')->where('user_id', $id)->update($profileData);
        } else {
            $profileData['user_id'] = $id;
            $profileData['first_name'] = '';
            $profileData['last_name'] = '';
            $db->table('user_profiles')->insert($profileData);
        }

        $actionUserId = $this->request->getHeaderLine('X-User-Id');
        if ($actionUserId) {
            \App\Models\ActivityLogModel::log($actionUserId, 'Update User', 'updated user account: ' . $data['full_name']);
        }

        return $this->respond(['success' => true, 'message' => 'User updated successfully.']);
    }

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
