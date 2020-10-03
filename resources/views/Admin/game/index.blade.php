@extends('Admin/layout/master')

@section('css')
	@include('Front/common/datatables_css')
@endsection

@section('content-header')
      <h1>
        游戏调控
        <small>control</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
        <li class="active">control</li>
      </ol>	
@endsection

@section('content')
<div class="box">
	<div class="box-header">
			@if (session('status'))
				<div class="alert alert-success">
						{{ session('status') }}
				</div>
		   @endif
	</div>
	<div class="box-body " id="form_do">
		<div class="row">
			<div class="col-md-12">
			<form class="form-inline" id="POST" method="POST" action="{{url("adminTeQ8E5D8/gameControl")}}">

					<div class="form-group">
					  <label for="exampleInputName2">游戏类别</label>

					  <select class="form-control" name="kindid" id="exampleInputName2">
							@foreach ($data as $k => $v)
					  			<option value="{{$k}}">{{$v}}</option>
							@endforeach
						</select>
					</div>


					<div class="form-group">
					  <label for="exampleInputEmail2">比率</label>
					  <input type="input" name="rate" class="form-control" id="exampleInputEmail2" placeholder="0-100调控比率" value="">
					</div>

					<button type="submit" class="btn btn-default">调控</button>


				  </form>
			</div>
		</div>		
	</div>
	<div class="box-footer">

	</div>
</div>



@endsection


@section('js')
<script>
    $('div.alert').delay(3000).fadeOut(350);
</script>

@endsection