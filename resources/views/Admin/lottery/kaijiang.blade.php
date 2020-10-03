@extends('Admin/layout/master')

@section('css')
	@include('Front/common/datatables_css')
@endsection

@section('content-header')
      <h1>
        采集开关
        <small>kaijiang</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
        <li class="active">kaijiang</li>
      </ol>	
@endsection

@section('content')
<div class="box">
	<div class="box-header">
		<form  class="form-inline pull-left" role="form" method="GET">
			@include('Search/time')
			<button id="search-form"  class="btn btn-primary btn-md">Search</button>
        </form>
        <p class="pull-right">
        </p>
		  {{-- @include('flash::message') --}}
	</div>

	<div class="box-body table-responsive" id="form_do">
		<div class="row">
			<div class="col-md-12">
				<table class="table tale-bordered table-bordered table-hover table-condensed mdl-data-table""  id="users-table">
					<thead>
							<tr>
								<th>id</th>
								<th>addtime</th>
								<th>彩票标识</th>
								<th>彩票名称</th>
								<th>开奖号码</th>
								<th>sourcecode</th>
								<th>第三方快乐彩结果</th>
								<th>期号</th>
								<th>开奖时间</th>
								<th>0未开奖 1开奖</th>
								<th>来源</th>
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
	@include('Front/common/daterangepicker')

	<script>
		$(function () {
			$('[data-toggle="tooltip"]').tooltip()
		})
			$(function(){


				$('#users-table').DataTable({
					order : [[0,'desc']],
					procession: true,
					serverSide: true,
					pageLength : 100,
					language :{
						url : "{{url('adminTeQ8E5D8/datatabslesZh')}}"
					},
          ajax:{
            url : '{!! route('kaijiang.data') !!}',
            data:function(d){
				d.time = $("#reservation1").val();
				d.kindid = 2222;
            }
          },
					columns:[
						{data: 'id',name:'id'},
						{data: 'addtime',name:'addtime'},
						{data: 'name',name:'name'},
						{data: 'title',name:'title'},
						{data: 'opencode',name:'opencode'},
						{data: 'sourcecode',name:'sourcecode'},
						{data: 'remarks',name:'remarks'},
						{data: 'expect',name:'expect'},
						{data: 'opentime',name:'opentime'},
						{data: 'isdraw',name:'isdraw'},
						{data: 'source',name:'source'}
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