<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      {{-- <div class="user-panel">
        <div class="pull-left image">
          <img src="{{asset('bower_components/admin-lte/dist/img/user2-160x160.jpg')}}" class="img-circle" alt="User Image">
        </div>

        <div class="pull-left info">
          <p>Alexander Pierce</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div> --}}
      <!-- search form -->
      {{-- <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form> --}}
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-dashboard"></i> <span>原始数据</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="active"><a href="{{url('adminTeQ8E5D8/record')}}"><i class="fa fa-circle-o"></i> 游玩记录表</a></li>
            <li class="active"><a href="{{url('adminTeQ8E5D8/pumping')}}"><i class="fa fa-circle-o"></i> 玩家抽水</a></li>
            <li class="active"><a href="{{url('adminTeQ8E5D8/pumpingAgent')}}"><i class="fa fa-circle-o"></i> 个人业绩(每天)</a></li>
            <li class="active"><a href="{{url('adminTeQ8E5D8/dailyWeek')}}"><i class="fa fa-circle-o"></i> 团队业绩(每天)</a></li>
            
            <li class="active"><a href="{{url('adminTeQ8E5D8/control')}}"><i class="fa fa-circle-o"></i> 游戏调控</a></li>

          </ul>
        </li>
        <li class="header">时时彩(本地测试)</li>

        <li><a href="{{url('adminTeQ8E5D8/lottery')}}"><i class="fa fa-circle-o text-red"></i> <span>投注情况</span></a></li>

        <li><a href="{{url('adminTeQ8E5D8/kaijiang')}}"><i class="fa fa-circle-o text-yellow"></i> <span>采集开奖</span></a></li>
        {{-- <li><a href="#"><i class="fa fa-circle-o text-aqua"></i> <span>Information</span></a></li> --}}
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>