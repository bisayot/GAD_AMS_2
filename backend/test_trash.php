<?php
$db = new mysqli('localhost', 'root', '', 'gad_submission_system');
$userId = 1;
$sql = "
    SELECT messages.id, users.username
    FROM messages
    INNER JOIN (
        SELECT MAX(id) as max_id
        FROM messages
        WHERE (recipient_id = $userId AND deleted_by_recipient_at IS NOT NULL)
           OR (sender_id = $userId AND deleted_by_sender_at IS NOT NULL)
        GROUP BY IFNULL(parent_id, id)
    ) latest ON messages.id = latest.max_id
    JOIN users ON users.id = IF(messages.sender_id = $userId, messages.recipient_id, messages.sender_id)
    LEFT JOIN user_profiles ON user_profiles.user_id = users.id
    ORDER BY messages.created_at DESC
";
$res = $db->query($sql);
if (!$res) {
    echo "Error: " . $db->error;
} else {
    while($row = $res->fetch_assoc()) {
        print_r($row);
    }
}
