@extends('Admin/layout/master')

@section('css')
	@include('Front/common/datatables_css')
@endsection

@section('content-header')
      <h1>
        彩票投注记录
        <small>touzhu</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
        <li class="active">touzhu</li>
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
           yjf:1元 0.1 角 0.01分 0.001厘
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
								<th>中奖</th>
								<th>单号</th>
								<th>yjf</th>
								<th>种类</th>
								<th>标识</th>
								<th>名称</th>
								<th>标题</th>
								<th>名称</th>
								<th>期号</th>
								<th>游戏ID</th>
								<th>注数</th>
								<th>号码</th>
								<th>奖金</th>
								<th>投注金额</th>
								<th>前金额</th>
								<th>后金额</th>
								<th>投注时间</th>
								<th>开奖号</th>
								<th>发放</th>
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
            url : '{!! route('touzhuData.data') !!}',
            data:function(d){
				d.time = $("#reservation1").val();
				d.kindid = 2222;
            }
          },
					columns:[
						{data: 'id',name:'id'},
						{data: 'isdraw',name:'isdraw'},
						{data: 'trano',name:'trano'},
						{data: 'yjf',name:'yjf'},
						{data: 'typeid',name:'typeid'},
						{data: 'playid',name:'playid'},
						{data: 'playtitle',name:'playtitle'},
						{data: 'cptitle',name:'cptitle'},
						{data: 'cpname',name:'cpname'},
						{data: 'expect',name:'expect'},
						{data: 'uid',name:'uid'},
						{data: 'itemcount',name:'itemcount'},
						{data: 'tzcode',name:'tzcode'},
						{data: 'okamount',name:'okamount'},
						{data: 'amount',name:'amount'},
						{data: 'amountbefor',name:'amountbefor'},
						{data: 'amountafter',name:'amountafter'},
						{data: 'oddtime',name:'oddtime'},
						{data: 'opencode',name:'opencode'},
						{data: 'thrid_status',name:'thrid_status'},
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