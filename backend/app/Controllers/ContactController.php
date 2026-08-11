<?php

namespace App\Controllers;

use App\Models\ContactInquiryModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class ContactController extends BaseController
{
    use ResponseTrait;

    public function submit()
    {
        $rules = [
            'name'    => 'required|max_length[150]',
            'email'   => 'required|valid_email|max_length[150]',
            'subject' => 'required|max_length[150]',
            'message' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'name'    => $this->request->getVar('name'),
            'email'   => $this->request->getVar('email'),
            'subject' => $this->request->getVar('subject'),
            'message' => $this->request->getVar('message'),
            'status'  => 'new'
        ];

        $model = new ContactInquiryModel();
        
        if ($model->insert($data)) {
            // Send email
            $email = \Config\Services::email();
            
            // Using SMTP user as sender to avoid DMARC/SPF issues with Brevo
            $smtpUser = getenv('SMTP_USER') ?: 'gad.office@bsu.edu.ph';
            $email->setFrom($smtpUser, 'GAD AMS Contact Form');
            $email->setReplyTo($data['email'], $data['name']);
            
            // Send to admin email (fallback to smtp user if none specific)
            $adminEmail = 'gad.office@bsu.edu.ph'; // Replace with actual recipient if needed
            $email->setTo($adminEmail);
            
            $email->setSubject('New Contact Inquiry: ' . $data['subject']);
            
            $htmlMessage = "
                <h2>New Contact Inquiry</h2>
                <p><strong>Name:</strong> {$data['name']}</p>
                <p><strong>Email:</strong> {$data['email']}</p>
                <p><strong>Subject:</strong> {$data['subject']}</p>
                <br/>
                <p><strong>Message:</strong></p>
                <p>" . nl2br(esc($data['message'])) . "</p>
            ";
            
            $email->setMessage($htmlMessage);
            
            $emailSent = $email->send();
            
            return $this->respondCreated([
                'status'  => 201,
                'message' => 'Inquiry submitted successfully.',
                'email_sent' => $emailSent
            ]);
        }

        return $this->failServerError('Failed to save inquiry.');
    }

    public function index()
    {
        // For admin/staff to view inquiries
        $model = new ContactInquiryModel();
        
        $status = $this->request->getVar('status');
        if ($status) {
            $model->where('status', $status);
        }
        
        $inquiries = $model->orderBy('created_at', 'DESC')->findAll();
        
        return $this->respond(['inquiries' => $inquiries]);
    }

    public function markAsRead($id = null)
    {
        $model = new ContactInquiryModel();
        $inquiry = $model->find($id);
        
        if (!$inquiry) {
            return $this->failNotFound('Inquiry not found.');
        }
        
        $model->update($id, ['status' => 'read']);
        return $this->respond(['message' => 'Inquiry marked as read.']);
    }

    public function reply($id = null)
    {
        $model = new ContactInquiryModel();
        $inquiry = $model->find($id);
        
        if (!$inquiry) {
            return $this->failNotFound('Inquiry not found.');
        }

        $replyMessage = $this->request->getVar('reply_message');
        if (empty($replyMessage)) {
            return $this->failValidationErrors(['reply_message' => 'Reply message is required.']);
        }

        // Identify who is replying
        $actionUserId = $this->request->getHeaderLine('X-User-Id');
        $repliedByStatus = 'replied_staff';
        $replierName = 'GAD Staff';
        
        if ($actionUserId) {
            $userModel = new UserModel();
            $user = $userModel->find($actionUserId);
            if ($user) {
                if ($user['role'] === 'admin') {
                    $repliedByStatus = 'replied_director';
                    $replierName = 'GAD Director';
                }
            }
        }

        // Send Email
        $email = \Config\Services::email();
        $smtpUser = getenv('SMTP_USER') ?: 'gad.office@bsu.edu.ph';
        $email->setFrom($smtpUser, $replierName . ' - BSU GAD');
        $email->setTo($inquiry['email']);
        $email->setSubject('Re: ' . $inquiry['subject']);
        
        $htmlMessage = "
            <h2>Reply to Your Inquiry</h2>
            <p>Hello {$inquiry['name']},</p>
            <p>" . nl2br(esc($replyMessage)) . "</p>
            <br>
            <hr>
            <p><strong>Your original message:</strong></p>
            <p><em>" . nl2br(esc($inquiry['message'])) . "</em></p>
        ";
        
        $email->setMessage($htmlMessage);
        
        if ($email->send()) {
            $model->update($id, ['status' => $repliedByStatus]);
            return $this->respond([
                'message' => 'Reply sent successfully.',
                'status' => $repliedByStatus
            ]);
        } else {
            // Usually fails when Brevo limit is hit or network issue
            return $this->failServerError('Failed to send email. You might have reached your daily limit.');
        }
    }
}
