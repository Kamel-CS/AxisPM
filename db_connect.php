<?php 

$conn= new mysqli('localhost','root','','tms_db')or die("Could not connect to mysql".mysqli_error($con));

// Check if notifications table exists and create it if it doesn't
$result = $conn->query("SHOW TABLES LIKE 'notifications'");
if($result->num_rows == 0) {
    $sql = "CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT NOT NULL,
        recipient_id INT NOT NULL,
        message TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        parent_id INT DEFAULT NULL,
        is_read TINYINT(1) DEFAULT 0,
        FOREIGN KEY (sender_id) REFERENCES users(id),
        FOREIGN KEY (recipient_id) REFERENCES users(id),
        FOREIGN KEY (parent_id) REFERENCES notifications(id) ON DELETE CASCADE
    )";
    
    if(!$conn->query($sql)) {
        error_log("Error creating notifications table: " . $conn->error);
    }
}
?>