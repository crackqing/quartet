@extends('Admin/layout/master')

@section('css')

@endsection

@section('content-header')
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
        <li class="active">tree</li>
      </ol>	
@endsection

@section('content')
<div>
    <ul id="treeDemo" class="ztree">

    </ul>
 </div>
@endsection


@section('js')
    @include('Front/common/ztree')

    <SCRIPT LANGUAGE="JavaScript">
   var zTreeObj;
   // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
   var setting = {};
   // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
   var zNodes = {!! $relationUser !!};
   $(document).ready(function(){
      zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
   });
  </SCRIPT>
@endsection