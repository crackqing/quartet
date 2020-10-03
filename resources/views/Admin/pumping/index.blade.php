@extends('Admin/layout/master')

@section('css')
	@include('Front/common/datatables_css')
@endsection

@section('content-header')
      <h1>
        玩家抽水
        <small>pumping</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
        <li class="active">pumping</li>
      </ol>	
@endsection

@section('content')
<div class="box">
	<div class="box-header">
		{{-- <form  class="form-inline pull-left" role="form">
			@include('Search/time')
			<button id="search-form"  class="btn btn-primary btn-md">Search</button>
		</form> --}}
		<h5> 每晚凌晨定时更新00:05 左右可以看到数据. 查看今天大户
				现在的计算为 ===>业绩---yingli (绝对值  intval) 求和 (sum)
         
				游玩记录中针对游戏类别为1100（捕鱼）的盈利值，需要在基础数据除以10000后再除以10，也就是说一共除以100000   其它为10000
		</h5>
		  {{-- @include('flash::message') --}}
	</div>

	<div class="box-body table-responsive" id="form_do">
		<div class="row">
			<div class="col-md-12">
				<table class="table tale-bordered table-bordered table-hover table-condensed mdl-data-table""  id="users-table">
					<thead>
							<tr>
								<th>时间</th>
								<th>抽水(yingli)</th>
								<th>用户ID</th>
								<th>绑定ID</th>
							</tr>
					</thead>
				</table>
			</div>
		</div>		
	</div>

	<div class="box-footer">

	</div>
</div>





@endsection


@section('js')
  
{{-- <script>
    $('div.alert').delay(3000).fadeOut(350);
</script> --}}

	@include('Front/common/datatables_js')
	{{-- @include('Front/common/daterangepicker') --}}

	<script>
		$(function () {
			$('[data-toggle="tooltip"]').tooltip()
		})
			$(function(){
				$('#users-table').DataTable({
					order : [[1,'desc']],
					procession: true,
					serverSide: true,
					pageLength : 100,
					language :{
						url : "{{url('adminTeQ8E5D8/datatabslesZh')}}"
					},
          ajax:{
            url : '{!! route('pumping.data') !!}',
            data:function(d){
				d.time = $("#reservation1").val();
            }
          },
					columns:[
						{data: 'time',name:'time'},
						{data: 'total_choushui',name:'total_choushui'},
						{data: 'uid',name:'uid'},
						{data: 'bind_id',name:'bind_id'},
					]
				});
			});
   $('#search-form').on('submit',function(e){
        oTable.draw();

        //AJAX 显示对应的搜索的格式化信息在右边
        e.preventDefault();
   });

	</script>
@endsection