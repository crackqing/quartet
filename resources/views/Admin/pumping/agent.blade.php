@extends('Admin/layout/master')

@section('css')
	@include('Front/common/datatables_css')
@endsection

@section('content-header')
      <h1>
        个人业绩
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
		<h5> 每晚凌晨定时更新00:10 左右可以看到数据</h5>
	</div>
	<div class="box-body table-responsive" id="form_do">
		<div class="row">
			<div class="col-md-12">
				<table class="table tale-bordered table-bordered table-hover table-condensed mdl-data-table""  id="users-table">
					<thead>
							<tr>
								<th>时间</th>
								<th>代理ID</th>
								<th>抽水(yingli)</th>
								<th>返利等级</th>
								<th>每日返佣</th>
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
					order : [[2,'desc']],
					procession: true,
					serverSide: true,
					pageLength : 100,
					language :{
						url : "{{url('adminTeQ8E5D8/datatabslesZh')}}"
					},
          ajax:{
            url : '{!! route('pumping.agent.data') !!}',
            data:function(d){
				d.time = $("#reservation1").val();
            }
          },
					columns:[

						{data: 'time',name:'time'},
						{data: 'bind_id',name:'bind_id'},
						{data: 'total_choushui',name:'total_choushui'},
						{data: 'performance_add',name:'performance_add'},
						{data: 'rreturn_gold_add',name:'rreturn_gold_add'},
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