<?php
session_start();
include('./db_connect.php');
ob_start();
// if(!isset($_SESSION['system'])){

$system = $conn->query("SELECT * FROM system_settings")->fetch_array();
foreach ($system as $k => $v) {
  $_SESSION['system'][$k] = $v;
}
// }
ob_end_flush();
?>
<?php
if (isset($_SESSION['login_id']))
  header("location:index.php?page=home");

?>
<?php include 'header.php' ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Design by foolishdeveloper.com -->
  <title>Glassmorphism Login Form</title>

  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
  <!--Stylesheet-->
  <style media="screen">
    *,
    *:before,
    *:after {
      padding: 0;
      margin: 0;
      box-sizing: border-box;
    }

    body {
      background-color: #080710;
    }

    .background {
      width: 430px;
      height: 520px;
      position: absolute;
      transform: translate(-50%, -50%);
      left: 50%;
      top: 50%;
    }

    .background .shape {
      height: 200px;
      width: 200px;
      position: absolute;
      border-radius: 50%;
    }

    .shape:first-child {
      background: linear-gradient(#332048,
          #CE6EEB);
      left: -80px;
      top: -80px;
    }

    .shape:last-child {
      background: linear-gradient(to right,
          #0e5247,
          #16247d);
      right: -30px;
      bottom: -80px;
    }

    form {
      height: 520px;
      width: 400px;
      background-color: rgba(255, 255, 255, 0.13);
      position: absolute;
      transform: translate(-50%, -50%);
      top: 50%;
      left: 50%;
      border-radius: 10px;
      backdrop-filter: blur(10px);
      border: 2px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 0 40px rgba(8, 7, 16, 0.6);
      padding: 50px 35px;
    }

    form * {
      font-family: 'Poppins', sans-serif;
      color: #ffffff;
      letter-spacing: 0.5px;
      outline: none;
      border: none;
    }

    form h3 {
      font-size: 32px;
      font-weight: 500;
      line-height: 42px;
      text-align: center;
    }

    label {
      display: block;
      margin-top: 30px;
      font-size: 16px;
      font-weight: 500;
    }

    input {
      display: block;
      height: 50px;
      width: 100%;
      background-color: rgba(255, 255, 255, 0.07);
      border-radius: 3px;
      padding: 0 10px;
      margin-top: 8px;
      font-size: 14px;
      font-weight: 300;
    }

    ::placeholder {
      color: #e5e5e5;
    }

    button {
      margin-top: 50px;
      width: 100%;
      background-color: #ffffff;
      color: #080710;
      padding: 15px 0;
      font-size: 18px;
      font-weight: 600;
      border-radius: 5px;
      cursor: pointer;
    }

    .social {
      margin-top: 30px;
      display: flex;
    }

    .social div {
      background: red;
      width: 150px;
      border-radius: 3px;
      padding: 5px 10px 10px 5px;
      background-color: rgba(255, 255, 255, 0.27);
      color: #eaf0fb;
      text-align: center;
    }

    .social div:hover {
      background-color: rgba(255, 255, 255, 0.47);
    }

    .social .fb {
      margin-left: 25px;
    }

    .social i {
      margin-right: 4px;
    }

    .alert-danger {
      background: rgba(255, 0, 0, 0.15);
      border: 1px solid rgba(255, 0, 0, 0.3);
      color: #ff5252;
      border-radius: 5px;
      padding: 10px;
      margin-bottom: 15px;
      animation: shake 0.5s ease;
    }

    @keyframes shake {

      0%,
      100% {
        transform: translateX(0);
      }

      25% {
        transform: translateX(-5px);
      }

      50% {
        transform: translateX(5px);
      }

      75% {
        transform: translateX(-5px);
      }
    }
  </style>
</head>

<body>
  <div class="background">
    <div class="shape"></div>
    <div class="shape"></div>
  </div>
  <form id="login-form">
    <h3>AxisPM</h3>

    <label for="username">Email</label>
    <input type="email" placeholder="Email" name="email" required>

    <label for="password">Password</label>
    <input type="password" placeholder="Password" name="password" required>

    <button>Log In</button>
  </form>
  <script>
    $(document).ready(function() {
      $('#login-form').submit(function(e) {
        e.preventDefault();
        start_load();
        if ($(this).find('.alert-danger').length > 0)
          $(this).find('.alert-danger').remove();
        $.ajax({
          url: 'ajax.php?action=login',
          method: 'POST',
          data: $(this).serialize(),
          error: function() {
            // Display a generic error message without exposing sensitive details
            $('#login-form').prepend('<div class="alert alert-danger">An error occurred. Please try again later.</div>');
            end_load();
          },
          success: function(resp) {
            if (resp == 1) {
              location.href = 'index.php?page=home';
            } else {
              $('#login-form').prepend('<div class="alert alert-danger">Username or password is incorrect.</div>');
              end_load();
            }
          }
        });
      });
    });
  </script>
</body>

</html>
<?php include 'footer.php' ?>
