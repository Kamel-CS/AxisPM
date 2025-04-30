<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php 
  date_default_timezone_set('Africa/Algiers');
  
  ob_start();
  $title = isset($_GET['page']) ? ucwords(str_replace("_", ' ', $_GET['page'])) : "Home";
  ?>
  <title><?php echo $title ?> | </title>
  <?php ob_end_flush() ?>

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- DataTables -->
  <link rel="stylesheet" href="assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
   <!-- Select2 -->
  <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
   <!-- SweetAlert2 -->
  <link rel="stylesheet" href="assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="assets/plugins/toastr/toastr.min.css">
  <!-- dropzonejs -->
  <link rel="stylesheet" href="assets/plugins/dropzone/min/dropzone.min.css">
  <!-- DateTimePicker -->
  <link rel="stylesheet" href="assets/dist/css/jquery.datetimepicker.min.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Switch Toggle -->
  <link rel="stylesheet" href="assets/plugins/bootstrap4-toggle/css/bootstrap4-toggle.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="assets/dist/css/styles.css">
	<script src="assets/plugins/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="assets/plugins/jquery-ui/jquery-ui.min.js"></script>
 <!-- summernote -->
  <link rel="stylesheet" href="assets/plugins/summernote/summernote-bs4.min.css">
  
  <!-- Add this before closing head tag -->
  <style>
    /* Global Font Settings */
    body, html {
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }

    /* Headings */
    h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        letter-spacing: -0.02em;
    }

    /* Navigation and Buttons */
    .navbar, .btn, .nav-link {
        font-family: 'Poppins', sans-serif;
        font-weight: 500;
    }

    /* Table Headers */
    .table thead th {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        letter-spacing: 0.02em;
    }

    /* Card Titles */
    .card-title, .card-header {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
    }

    /* Dropdown Items */
    .dropdown-item {
        font-family: 'Poppins', sans-serif;
        font-weight: 400;
    }

    /* Form Labels and Inputs */
    label, .form-control {
        font-family: 'Poppins', sans-serif;
    }

    /* Sidebar */
    .nav-sidebar .nav-link p {
        font-family: 'Poppins', sans-serif;
        font-weight: 500;
    }

    /* Brand Logo */
    .brand-text {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        letter-spacing: -0.03em;
    }

    /* Font weight utilities */
    .font-light {
        font-weight: 300;
    }
    .font-regular {
        font-weight: 400;
    }
    .font-medium {
        font-weight: 500;
    }
    .font-semibold {
        font-weight: 600;
    }
    .font-bold {
        font-weight: 700;
    }

    /* Adjust line heights for better readability */
    p, .p {
        line-height: 1.6;
    }
    
    /* Small text */
    small, .small {
        font-family: 'Poppins', sans-serif;
        font-weight: 400;
    }

    /* Global animations - optimize performance */
    .card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      will-change: transform, box-shadow;
    }

    .card:hover {
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transform: translateY(-2px);
    }

    /* Smooth scrolling */
    html {
      scroll-behavior: smooth;
    }

    /* Optimize button animations */
    .btn {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      will-change: transform;
    }

    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }

    /* Optimize form control animations */
    .form-control {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      will-change: transform;
    }

    .form-control:focus {
      transform: translateY(-2px);
      box-shadow: 0 3px 8px rgba(0,0,0,0.1) !important;
    }

    /* Optimize modal animations */
    .modal .modal-content {
      animation: slideInUp 0.3s ease-out;
      will-change: transform, opacity;
    }

    /* Toast animations */
    .swal2-toast {
      animation: slideInDown 0.3s ease-out;
    }

    /* Loading animation */
    #preloader2 {
      animation: pulse 1s infinite;
    }

    @keyframes pulse {
      0% { opacity: 0.7; }
      50% { opacity: 1; }
      100% { opacity: 0.7; }
    }
  </style>
</head>