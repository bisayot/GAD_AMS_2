<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\MessageModel;

class MessageController extends ResourceController
{
    protected $messageModel;

    public function __construct()
    {
        $this->messageModel = new MessageModel();
    }

    public function send()
    {
        $rules = [
            'sender_id' => 'required|integer',
            'to'        => 'required',
            'title'     => 'required|string',
            'message'   => 'required|string',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Validation failed', 
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(400);
        }

        $senderId = $this->request->getVar('sender_id');
        $to = $this->request->getVar('to'); // Expected array of user IDs
        $title = $this->request->getVar('title');
        $messageText = $this->request->getVar('message');
        $documentType = $this->request->getVar('document_type');
        $documentId = $this->request->getVar('document_id');

        if (!is_array($to)) {
            $to = [$to];
        }

        if (is_array($documentId)) {
            $documentId = implode(',', $documentId);
        }

        $createdAt = date('Y-m-d H:i:s');
        $insertedCount = 0;

        foreach ($to as $recipientId) {
            $data = [
                'sender_id'     => $senderId,
                'recipient_id'  => $recipientId,
                'title'         => $title,
                'message_text'  => $messageText,
                'document_type' => $documentType ? $documentType : null,
                'document_id'   => $documentId ? $documentId : null,
                'is_read'       => 0,
                'created_at'    => $createdAt
            ];

            if ($this->messageModel->insert($data)) {
                $insertedCount++;
            }
        }

        if ($insertedCount > 0) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "Message sent to $insertedCount recipient(s)."
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to send message.'
            ])->setStatusCode(500);
        }
    }

    public function getInbox($userId)
    {
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'User ID required'])->setStatusCode(400);
        }

        // Fetch messages where recipient_id = $userId
        $db = \Config\Database::connect();
        $builder = $db->table('messages');
        $builder->select('messages.*, users.full_name, users.username, users.email, COALESCE(user_profiles.user_role, users.role) as role, users.office_id');
        $builder->join('users', 'users.id = messages.sender_id');
        $builder->join('user_profiles', 'user_profiles.user_id = users.id', 'left');
        $builder->where('messages.recipient_id', $userId);
        $builder->orderBy('messages.created_at', 'DESC');
        $query = $builder->get();

        $messages = $query->getResultArray();

        // Format for frontend
        $formatted = array_map(function($msg) {
            $senderName = $msg['full_name'] ?? '';
            if (empty($senderName)) {
                $senderName = $msg['username'] ?? 'Unknown Sender';
            }

            $date = new \DateTime($msg['created_at']);
            $date->setTimezone(new \DateTimeZone('Asia/Manila'));

            return [
                'id' => $msg['id'],
                'sender' => $senderName,
                'sender_id' => $msg['sender_id'],
                'email' => $msg['email'],
                'role' => $msg['role'],
                'office_id' => $msg['office_id'],
                'date' => $date->format('M d, Y h:i A'),
                'title' => $msg['title'],
                'preview' => mb_strimwidth(strip_tags($msg['message_text']), 0, 100, "..."),
                'message' => $msg['message_text'],
                'document_type' => $msg['document_type'],
                'document_id' => $msg['document_id']
            ];
        }, $messages);

        return $this->response->setJSON([
            'success' => true,
            'data' => $formatted
        ]);
    }

    public function getSent($userId)
    {
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'User ID required'])->setStatusCode(400);
        }

        // Fetch messages where sender_id = $userId
        $db = \Config\Database::connect();
        $builder = $db->table('messages');
        $builder->select('messages.*, users.full_name, users.username, users.email, COALESCE(user_profiles.user_role, users.role) as role, users.office_id');
        $builder->join('users', 'users.id = messages.recipient_id');
        $builder->join('user_profiles', 'user_profiles.user_id = users.id', 'left');
        $builder->where('messages.sender_id', $userId);
        $builder->orderBy('messages.created_at', 'DESC');
        $query = $builder->get();

        $messages = $query->getResultArray();

        // Format for frontend
        $formatted = array_map(function($msg) {
            $recipientName = $msg['full_name'] ?? '';
            if (empty($recipientName)) {
                $recipientName = $msg['username'] ?? 'Unknown Recipient';
            }

            $date = new \DateTime($msg['created_at']);
            $date->setTimezone(new \DateTimeZone('Asia/Manila'));

            return [
                'id' => $msg['id'],
                'sender' => $recipientName, // we use 'sender' key in frontend but it means 'other party'
                'sender_id' => $msg['recipient_id'],
                'email' => $msg['email'],
                'role' => $msg['role'],
                'office_id' => $msg['office_id'],
                'date' => $date->format('M d, Y h:i A'),
                'title' => $msg['title'],
                'preview' => $msg['message_text'],
                'document_type' => $msg['document_type'],
                'document_id' => $msg['document_id'],
                'is_read' => $msg['is_read']
            ];
        }, $messages);

        return $this->response->setJSON([
            'success' => true,
            'data' => $formatted
        ]);
    }
    
    public function markAsRead($messageId)
    {
        if ($this->messageModel->update($messageId, ['is_read' => 1])) {
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false])->setStatusCode(500);
    }
}
