<!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-primary navbar-dark ">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <?php if(isset($_SESSION['login_id'])): ?>
      <li class="nav-item">
        <a class="nav-link hover-effect" data-widget="pushmenu" href="" role="button"><i class="fas fa-bars"></i></a>
      </li>
    <?php endif; ?>
      <li>
        <a class="nav-link text-white logo-hover" href="index.php" role="button">
          <img src="landing-page/assets/axisPM-log-nobackground.jpg" alt="AxisPM Logo" style="height: 25px; margin-right: 5px;" class="logo-spin">
          <large><b>AxisPM</b></large>
        </a>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" id="notification-bell">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge notification-count">0</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-header"><span class="notification-count">0</span> Notifications</span>
          <div class="dropdown-divider"></div>
          <div id="notification-dropdown-list">
            <!-- Notifications will be loaded here -->
          </div>
          <div class="dropdown-divider"></div>
          <a href="index.php?page=notifications" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link hover-effect" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
     <li class="nav-item dropdown">
            <a class="nav-link hover-effect"  data-toggle="dropdown" aria-expanded="true" href="javascript:void(0)">
              <span>
                <div class="d-felx badge-pill">
                  <span class="fa fa-user mr-2"></span>
                  <span><b><?php echo ucwords($_SESSION['login_firstname']) ?></b></span>
                  <span class="fa fa-angle-down ml-2"></span>
                </div>
              </span>
            </a>
            <div class="dropdown-menu animate-menu" aria-labelledby="account_settings" style="left: -2.5em;">
              <a class="dropdown-item hover-effect" href="javascript:void(0)" id="manage_account"><i class="fa fa-cog"></i> Manage Account</a>
              <a class="dropdown-item hover-effect" href="ajax.php?action=logout"><i class="fa fa-power-off"></i> Logout</a>
            </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->
  <style>
    /* Hover effect for links */
    .hover-effect {
      transition: all 0.3s ease;
    }
    .hover-effect:hover {
      transform: translateY(-2px);
      color: #fff !important;
      text-shadow: 0 0 10px rgba(255,255,255,0.5);
    }

    /* Logo animation */
    .logo-hover:hover .logo-spin {
      transform: scale(1.1);
    }
    .logo-spin {
      transition: transform 0.3s ease;
    }

    /* Dropdown menu animation */
    .animate-menu {
      animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Pulse animation for notifications */
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }

    /* Smooth page transitions */
    body {
      opacity: 1;
      transition: opacity 0.3s ease;
    }

    body.page-transition {
      opacity: 0;
    }
  </style>
  <script>
     $(document).ready(function(){
        $('#manage_account').on('click', function(e){
            e.preventDefault();
            uni_modal('Manage Account','manage_user.php?id=<?php echo $_SESSION['login_id'] ?>');
        });
     });

     // Add page transition effect
     $(document).on('click', 'a[href]:not([target]):not([href^="#"])', function(e) {
        if (this.href.indexOf(window.location.origin) === 0) {
          e.preventDefault();
          document.body.classList.add('page-transition');
          setTimeout(() => {
            window.location = this.href;
          }, 300);
        }
     });

     // Remove transition class on page load
     window.addEventListener('load', function() {
        document.body.classList.remove('page-transition');
     });
  </script>
