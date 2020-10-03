@extends('Admin/layout/master')

@section('css')
	@include('Front/common/datatables_css')
@endsection

@section('content-header')
      <h1>
        团队业绩
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
		<h5>每天凌晨定时计算 00:10 开始统计每天的团队业绩</h5>
	</div>

	<div class="box-body table-responsive" id="form_do">
		<div class="row">
			<div class="col-md-12">
				<table class="table tale-bordered table-bordered table-hover table-condensed mdl-data-table"  id="users-table">
					<thead>
							<tr>
									<th>时间区间</th>
									<th>代理ID</th>
									<th>个人业绩</th>
									<th>每周抽水</th>
									<th>返利等级</th>
									<th>返利钱数</th>
									<th>个人业绩已领钱数</th>
									<th>团队业绩已领钱数</th>
									<th>实发钱数</th>
									<th>level</th>
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
					order : [[3,'desc']],
					procession: true,
					serverSide: true,
					pageLength : 100,
					language :{
						url : "{{url('adminTeQ8E5D8/datatabslesZh')}}"
					},
          ajax:{
            url : '{!! route('daily.week.data') !!}',
            data:function(d){
				d.time = $("#reservation1").val();
            }
          },
					columns:[
						{data: 'time_rand',name:'time_rand'},
						{data: 'uid',name:'uid'},
						{data: 'self',name:'self'},
						{data: 'total_choushui',name:'total_choushui'},
						{data: 'performance',name:'performance'},
						{data: 'return_gold',name:'return_gold'},
						{data: 'receive',name:'receive'},
						{data: 'team_receive',name:'team_receive'},
						{data: 'payable',name:'payable'},
						{data: 'level',name:'level'},
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