<?php 
include 'db_connect.php';
include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
    <?php include 'topbar.php'; ?>
    
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Notifications</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <?php if($_SESSION['login_type'] == 1): ?>
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Send New Notification</h3>
            </div>
            <div class="card-body">
                <form id="notification-form">
                    <div class="form-group">
                        <label>Select Recipients</label>
                        <select class="form-control select2" name="recipient_ids[]" multiple="multiple">
                            <?php 
                            $users = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM users WHERE id != '".$_SESSION['login_id']."' ORDER BY firstname");
                            while($row = $users->fetch_assoc()):
                            ?>
                            <option value="<?php echo $row['id'] ?>"><?php echo ucwords($row['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea class="form-control" name="message" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Notification</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">My Notifications</h3>
            </div>
            <div class="card-body">
                <div id="notification-list" class="direct-chat-messages" style="height: auto; max-height: none;">
                    <!-- Notifications will be loaded here -->
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1" role="dialog" aria-labelledby="replyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="replyModalLabel">Reply to Notification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="reply-form">
                    <input type="hidden" name="parent_id" id="parent_id">
                    <input type="hidden" name="recipient_id" id="recipient_id">
                    <div class="form-group">
                        <label>Your Reply</label>
                        <textarea class="form-control" name="reply_message" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="send-reply">Send Reply</button>
            </div>
        </div>
    </div>
</div>

<style>
.direct-chat-messages {
    padding: 10px;
}
.direct-chat-msg {
    margin-bottom: 20px;
}
.direct-chat-msg.right .direct-chat-text {
    margin-right: 0;
    margin-left: 50px;
    background-color: #3498db;
    color: white;
    float: right;
}
.direct-chat-msg.right {
    text-align: right;
}
.direct-chat-text {
    border-radius: 10px;
    position: relative;
    padding: 10px;
    background: #f8f9fa;
    margin: 5px 0;
    display: inline-block;
    max-width: 80%;
}
.direct-chat-name {
    font-weight: bold;
    margin-bottom: 5px;
}
.direct-chat-timestamp {
    color: #6c757d;
    font-size: 0.85rem;
    margin: 0 10px;
}
.reply-btn {
    margin-top: 5px;
    color: #6c757d;
    padding: 2px 5px;
    font-size: 0.85rem;
    display: block;
}
.direct-chat-msg.right .reply-btn {
    margin-left: auto;
}
</style>

<script>
$(document).ready(function(){
    $('.select2').select2({
        placeholder: "Select recipients",
        width: '100%'
    });

    function loadNotifications() {
        $.ajax({
            url: 'ajax.php?action=get_notifications',
            method: 'GET',
            success: function(resp) {
                if(resp){
                    $('#notification-list').html(resp);
                }
            }
        });
    }

    $('#notification-form').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: 'ajax.php?action=send_notification',
            method: 'POST',
            data: $(this).serialize(),
            success: function(resp) {
                if(resp == 1) {
                    alert_toast("Notification sent successfully", "success");
                    $('#notification-form').get(0).reset();
                    loadNotifications();
                }
            }
        });
    });

    // Reply functionality
    $(document).on('click', '.reply-btn', function() {
        var notificationId = $(this).data('id');
        var senderId = $(this).data('sender');
        $('#parent_id').val(notificationId);
        $('#recipient_id').val(senderId);
        $('#replyModal').modal('show');
    });

    $('#send-reply').click(function() {
        console.log('Sending reply...');
        var formData = $('#reply-form').serialize();
        console.log('Form data:', formData);
        $.ajax({
            url: 'ajax.php?action=reply_notification',
            method: 'POST',
            data: formData,
            success: function(resp) {
                console.log('Reply response:', resp);
                if(resp == 1) {
                    alert_toast("Reply sent successfully", "success");
                    $('#replyModal').modal('hide');
                    $('#reply-form').get(0).reset();
                    loadNotifications();
                } else {
                    alert_toast("Error sending reply", "error");
                }
            },
            error: function(xhr, status, error) {
                console.error('Reply error:', error);
                alert_toast("Error sending reply", "error");
            }
        });
    });

    // Load notifications every 30 seconds
    loadNotifications();
    setInterval(loadNotifications, 30000);
});
</script>

<?php include 'footer.php' ?>