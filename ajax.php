<?php
ob_start();
date_default_timezone_set("Asia/Manila");
include 'db_connect.php';

$action = $_GET['action'];
include 'admin_class.php';
$crud = new Action();
if($action == 'login'){
	$login = $crud->login();
	if($login)
		echo $login;
}
if($action == 'login2'){
	$login = $crud->login2();
	if($login)
		echo $login;
}
if($action == 'logout'){
	$logout = $crud->logout();
	if($logout)
		echo $logout;
}
if($action == 'logout2'){
	$logout = $crud->logout2();
	if($logout)
		echo $logout;
}

if($action == 'signup'){
	$save = $crud->signup();
	if($save)
		echo $save;
}
if($action == 'save_user'){
	$save = $crud->save_user();
	if($save)
		echo $save;
}
if($action == 'update_user'){
	$save = $crud->update_user();
	if($save)
		echo $save;
}
if($action == 'delete_user'){
	$save = $crud->delete_user();
	if($save)
		echo $save;
}
if($action == 'save_project'){
	$save = $crud->save_project();
	if($save)
		echo $save;
}
if($action == 'delete_project'){
	$save = $crud->delete_project();
	if($save)
		echo $save;
}
if($action == 'save_task'){
	$save = $crud->save_task();
	if($save)
		echo $save;
}
if($action == 'delete_task'){
	$save = $crud->delete_task();
	if($save)
		echo $save;
}
if($action == 'save_progress'){
	$save = $crud->save_progress();
	if($save)
		echo $save;
}
if($action == 'delete_progress'){
	$save = $crud->delete_progress();
	if($save)
		echo $save;
}
if($action == 'get_report'){
	$get = $crud->get_report();
	if($get)
		echo $get;
}

if($action == "send_notification"){
    $recipient_ids = $_POST['recipient_ids'];
    $message = $_POST['message'];
    $sender_id = $_SESSION['login_id'];
    $success = true;

    foreach($recipient_ids as $recipient_id){
        $sql = "INSERT INTO notifications (sender_id, recipient_id, message, created_at) VALUES ('$sender_id', '$recipient_id', '$message', NOW())";
        $success = $success && $conn->query($sql);
    }

    if($success)
        echo 1;
    else
        echo 0;
    exit;
}

if($action == "reply_notification"){
    $parent_id = $_POST['parent_id'];
    $recipient_id = $_POST['recipient_id'];
    $message = $_POST['reply_message'];
    $sender_id = $_SESSION['login_id'];
    
    $sql = "INSERT INTO notifications (sender_id, recipient_id, message, created_at, parent_id) 
            VALUES ('$sender_id', '$recipient_id', '$message', NOW(), '$parent_id')";
    
    if($conn->query($sql)) {
        echo 1;
    } else {
        error_log("MySQL Error in reply_notification: " . $conn->error);
        echo 0;
    }
    exit;
}

if($action == "get_notifications"){
    $user_id = $_SESSION['login_id'];
    $notifications = $conn->query("SELECT n.*, CONCAT(u.firstname, ' ', u.lastname) as sender_name 
                                 FROM notifications n 
                                 INNER JOIN users u ON u.id = n.sender_id 
                                 WHERE (n.recipient_id = '$user_id' OR n.sender_id = '$user_id')
                                 AND (n.parent_id IS NULL)
                                 ORDER BY n.created_at DESC");
    
    $output = "";
    while($row = $notifications->fetch_assoc()){
        $timeAgo = date('M d Y h:i A', strtotime($row['created_at']));
        $isReceived = $row['recipient_id'] == $user_id;
        
        $output .= '
        <div class="direct-chat-msg ' . ($isReceived ? '' : 'right') . ' clearfix">
            <div class="direct-chat-infos clearfix">
                <span class="direct-chat-name ' . ($isReceived ? 'float-left' : 'float-right') . '">' . $row['sender_name'] . '</span>
                <span class="direct-chat-timestamp ' . ($isReceived ? 'float-right' : 'float-left') . '">' . $timeAgo . '</span>
            </div>
            <div class="direct-chat-text">
                ' . $row['message'] . '
                <button class="btn btn-sm btn-link reply-btn" data-id="' . $row['id'] . '" data-sender="' . $row['sender_id'] . '">
                    <i class="fas fa-reply"></i> Reply
                </button>
            </div>';
        
        // Get replies for this notification
        $replies = $conn->query("SELECT n.*, CONCAT(u.firstname, ' ', u.lastname) as sender_name 
                               FROM notifications n 
                               INNER JOIN users u ON u.id = n.sender_id 
                               WHERE n.parent_id = {$row['id']}
                               ORDER BY n.created_at ASC");
        
        while($reply = $replies->fetch_assoc()){
            $replyTimeAgo = date('M d Y h:i A', strtotime($reply['created_at']));
            $isReplyReceived = $reply['recipient_id'] == $user_id;
            
            $output .= '
            <div class="direct-chat-msg ' . ($isReplyReceived ? '' : 'right') . ' clearfix" style="margin-left: 30px;">
                <div class="direct-chat-infos clearfix">
                    <span class="direct-chat-name ' . ($isReplyReceived ? 'float-left' : 'float-right') . '">' . $reply['sender_name'] . '</span>
                    <span class="direct-chat-timestamp ' . ($isReplyReceived ? 'float-right' : 'float-left') . '">' . $replyTimeAgo . '</span>
                </div>
                <div class="direct-chat-text">
                    ' . $reply['message'] . '
                </div>
            </div>';
        }
        
        $output .= '</div>';
    }
    
    echo $output;
    exit;
}

if($action == "get_unread_count"){
    $user_id = $_SESSION['login_id'];
    $count = $conn->query("SELECT COUNT(*) as count FROM notifications WHERE recipient_id = '$user_id' AND is_read = 0")->fetch_assoc()['count'];
    echo $count;
    exit;
}

ob_end_flush();
?>
