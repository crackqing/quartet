<link rel="stylesheet" href="{{asset('bower_components/bootstrap-daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css') }}">


<div class="form-group">
    <label for="time">时间区间: </label>
    <input type="text" id="reservation1" name="time" class="form-control"  @if(!empty(request('time'))) value="{{ request('time')}}"  @endif >
</div>


