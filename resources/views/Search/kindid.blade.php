<div class="form-group">
    <label for="checkbox" >游戏选择: </label>

    <label class="checkbox-inline" id="checkAll" class="form-control">
        <input type="checkbox"  value="1000" name="kindid[]"  checked> 百人牛牛
      </label>
      <label class="checkbox-inline">
        <input type="checkbox" value="1001" name="kindid[]" checked> 奔驰宝马
      </label>
      <label class="checkbox-inline">
        <input type="checkbox"  value="1002" name="kindid[]" @if(!empty(request('kindid'))) 
        @if(request('kindid') == 1002) checked   @endif           
    @endif> 飞禽走兽
      </label>
      <label class="checkbox-inline">
        <input type="checkbox"  value="1004" name="kindid[]" @if(!empty(request('kindid'))) 
        @if(request('kindid') == 1004) checked   @endif           
    @endif> 百家乐
      </label>      <label class="checkbox-inline">
        <input type="checkbox" value="1005" name="kindid[]"  @if(!empty(request('kindid'))) 
        @if(request('kindid') == 1005) checked   @endif           
    @endif> 水浒传
      </label>      <label class="checkbox-inline">
        <input type="checkbox"  value="1015" name="kindid[]"  @if(!empty(request('kindid'))) 
        @if(request('kindid') == 1015) checked   @endif           
    @endif> 红黑大战
      </label>      
      <label class="checkbox-inline">
        <input type="checkbox"  value="1100" name="kindid[]"  @if(!empty(request('kindid'))) 
        @if(request('kindid') == 1100) checked   @endif           
    @endif> 摇钱树
      </label> 
</div>
