<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title><?php echo _project_complete_name_ ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&display=swap" rel="stylesheet">
  <?
  $csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
  );
  ?>
  <meta name="<?= $csrf['name']; ?>" content="<?= $csrf['hash']; ?>">
  <?
  if (!empty($page_type)) {
    if ($page_type == "list") {
      $this->load->view('admin/inc/files/header-list', $this->data);
    }
  } else {
    $this->load->view('admin/inc/files/header', $this->data);
  }
  ?>
  <style type="text/css">
    .width100,
    .select2-container {
      width: 100% !important;
    }
  </style>
  <script>

    $.ajaxSetup({
      headers: {
        '<?= $csrf['name'] ?>': '<?= $csrf['hash'] ?>'
      }
    });
  </script>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed pace-primary"
  data-scrollbar-auto-hide="n">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="<?= MAINSITE_Admin . "wam" ?>" class="nav-link">Dashboard</a>
        </li>
        <!-- Lock screen -->
        <!-- <li class="nav-item d-none d-sm-inline-block">
          <a href="<?= MAINSITE_Admin . 'wam/lock-screen' ?>" class="btn btn-dark "><b>Screen Lock</b></a>
        </li> -->
      </ul>

      <ul class="navbar-nav ml-auto">
        <li class="nav-item d-none d-sm-inline-block mr-3">
          <p>
            <?= $user_data->name ?>
          </p>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="<?= MAINSITE_Admin . 'wam/logout' ?>" class="btn btn-default float-right"><b>Logout</b></a>
        </li>


      </ul>
    </nav>