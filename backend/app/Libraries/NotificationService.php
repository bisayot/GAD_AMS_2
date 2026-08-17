<?php

namespace App\Libraries;

use App\Models\NotificationModel;
use App\Models\UserModel;

class NotificationService
{
    /**
     * Send a notification to a user, and also dispatch an email.
     * 
     * @param int $userId Target user ID
     * @param string $title Notification title
     * @param string $message Notification detailed message
     * @param string|null $link Optional link when clicking notification
     * @param string|null $type Optional type (e.g. 'success', 'warning', 'info')
     * @return bool
     */
    public static function send($userId, $title, $message, $link = null, $type = 'info')
    {
        // 1. Save to database
        $notificationModel = new NotificationModel();
        $notificationModel->insert([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'link'    => $link,
            'type'    => $type,
            'is_read' => 0
        ]);

        // 2. Fetch User to get email
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if ($user && !empty($user['email'])) {
            self::sendEmail($user['email'], $user['first_name'], $title, $message, $link);
        }

        return true;
    }

    /**
     * Internal method to send email via CI4 Email service
     */
    private static function sendEmail($to, $name, $title, $message, $link)
    {
        $email = \Config\Services::email();

        // Build simple HTML layout
        $htmlMessage = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee;'>
            <h2 style='color: #990dd1;'>GAD AMS Notification</h2>
            <p>Hi " . htmlspecialchars($name ?? 'User') . ",</p>
            <p><strong>" . htmlspecialchars($title) . "</strong></p>
            <p>" . nl2br(htmlspecialchars($message)) . "</p>";
        
        // No link routing in email as per user request
        $htmlMessage .= "
            <br><br>
            <hr style='border: 0; border-top: 1px solid #eee;'>
            <p style='font-size: 12px; color: #888;'>This is an automated message from the GAD AMS System. Please do not reply.</p>
        </div>";

        $email->setTo($to);
        $email->setSubject($title);
        $email->setMessage($htmlMessage);
        
        // We catch errors silently to not break the API if email fails
        try {
            $email->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send notification email: ' . $e->getMessage());
        }
    }

    /**
     * Send a notification to all admin users.
     * 
     * @param string $title Notification title
     * @param string $message Notification detailed message
     * @param string|null $link Optional link when clicking notification
     * @param string|null $type Optional type (e.g. 'success', 'warning', 'info')
     * @return void
     */
    public static function sendToAdmins($title, $message, $link = null, $type = 'info')
    {
        $db = \Config\Database::connect();
        $admins = $db->table('users')->where('role', 'admin')->orWhere('role', 'gad_staff')->get()->getResultArray();
        
        foreach ($admins as $admin) {
            self::send($admin['id'], $title, $message, $link, $type);
        }
    }
}
