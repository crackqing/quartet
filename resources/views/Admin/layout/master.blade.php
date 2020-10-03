<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>data-platform</title>

  @include('Admin/layout/css')
  @yield('css')

  <meta name="csrf-token" content="{{ csrf_token() }}">


</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  @include('Admin/layout/header')
  <!-- Left side column. contains the logo and sidebar -->
  @include('Admin/layout/sidebar')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      {{-- <h1>
        Dashboard
        <small>Control panel</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol> --}}
      @yield('content-header')
    </section>

    <!-- Main content -->
    <section class="content">

      @yield('content')

    </section>
    <!-- /.content -->
  </div>

  <!-- /.content-wrapper -->
  @include('Admin/layout/footer')


</div>
<!-- ./wrapper -->

  @include('Admin/layout/js')
  @yield('js')

</body>
</html>
