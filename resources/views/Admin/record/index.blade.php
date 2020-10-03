@extends('Admin/layout/master')

@section('css')
	@include('Front/common/datatables_css')
@endsection

@section('content-header')
      <h1>
        游戏记录表
        <small>record</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
        <li class="active">record</li>
      </ol>	
@endsection

@section('content')
<div class="box">
	<div class="box-header">
		<form  class="form-inline pull-left" role="form" method="GET">
			@include('Search/time')
			{{-- @include('Search/kindid') --}}
			<button id="search-form"  class="btn btn-primary btn-md">Search</button>
		</form>
	<a class="btn btn-info btn-md pull-left" href="{{url('adminTeQ8E5D8/record/csv')}}">游戏玩家记录表格</a>
	<a class="btn btn-info btn-md pull-left" href="{{url('adminTeQ8E5D8/order/csv')}}">订单带ID表格</a>
		  {{-- @include('flash::message') --}}
	</div>

	<div class="box-body table-responsive" id="form_do">
		<div class="row">
			<div class="col-md-12">
				<table class="table tale-bordered table-bordered table-hover table-condensed mdl-data-table""  id="users-table">
					<thead>
							<tr>
								<th>序列</th>
								<th>游玩时间</th>
								<th>用户ID</th>
								<th>游戏类别</th>
								<th>押注</th>
								<th>得分</th>
								<th>赢利</th>
								<th>抽水</th>
								<th>金币</th>
								<th>银行</th>
								<th>桌子TID</th>
								<th>桌子名称</th>
								<th>是否庄家</th>
								<th>下注类型</th>
								<th>当前期数</th>
								<th>下注牌型</th>
								<th>开奖牌型</th>
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
            url : '{!! route('record.data') !!}',
            data:function(d){
				d.time = $("#reservation1").val();
				d.kindid = 2222;
            }
          },
					columns:[
						{data: 'id',name:'id'},
						{data: 'time',name:'time'},
						{data: 'uid',name:'uid'},
						{data: 'kindid',name:'kindid'},
						{data: 'yazhu',name:'yazhu'},
						{data: 'defen',name:'defen'},
						{data: 'yingli',name:'yingli'},
						{data: 'choushui',name:'choushui'},
						{data: 'coins',name:'coins'},
						{data: 'bank',name:'bank'},
						{data: 'tid',name:'time'},
						{data: 'tname',name:'tname'},
						{data: 'isdealer',name:'isdealer'},
						{data: 'xiazhu',name:'xiazhu'},
						{data: 'qishu',name:'qishu'},
						{data: 'xiazhupx',name:'xiazhupx'},
						{data: 'kaijiangpx',name:'kaijiangpx'},
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