<?php /* Smarty version 2.6.19, created on 2015-08-07 09:37:49
         compiled from field_confirm_options.htm */ ?>
<table class="table table-hover table-bordered table-condensed" style="margin-bottom:0px;">
 <tr>
     <td>
        1、默认值
         <input name="field_default" style="margin-bottom:0px;" type="text" placeholder="默认值" value="<?php echo $this->_tpl_vars['field_info']['default']; ?>
">
     </td>
 </tr>
 <tr>
      <td>
        2、<input type="checkbox" name="field_require" value="" <?php if ($this->_tpl_vars['field_info']['if_require'] == 2 && $this->_tpl_vars['field_info']['data_type'] != 7): ?>checked<?php endif; ?> <?php if ($this->_tpl_vars['field_info']['data_type'] == 7): ?>disabled<?php endif; ?> > 是否必填项
      </td>
 </tr>
 <tr>
     <td>
         3、类型设置
         <select id="field_data_type<?php echo $this->_tpl_vars['parent_id']; ?>
" name="field_data_type" style="margin-bottom:0px;" onchange="change_date_type()">
         	<option value="1" <?php if ($this->_tpl_vars['field_info']['data_type'] == 1): ?>selected<?php endif; ?> >输入框</option>
         	<option value="2" <?php if ($this->_tpl_vars['field_info']['data_type'] == 2): ?>selected<?php endif; ?> >下拉框</option>
         	<option value="3" <?php if ($this->_tpl_vars['field_info']['data_type'] == 3): ?>selected<?php endif; ?> >文本框</option>
         	<option value="4" <?php if ($this->_tpl_vars['field_info']['data_type'] == 4): ?>selected<?php endif; ?> >级联框</option>
         	<option value="5" <?php if ($this->_tpl_vars['field_info']['data_type'] == 5): ?>selected<?php endif; ?> >日期框</option>
         	<option value="7" <?php if ($this->_tpl_vars['field_info']['data_type'] == 7): ?>selected<?php endif; ?> >关联多选框</option>
         </select>
     </td>
 </tr>
 
 <tr class="datefmt" style="display:none;">
  <td>
       日期格式
       &nbsp;&nbsp;&nbsp;<input type="radio" name="datefmt" value="yyyy-MM-dd" <?php if ($this->_tpl_vars['field_info']['datefmt'] != 'yyyy-MM-dd HH:mm:ss'): ?>checked<?php endif; ?> > 年-月-日
       &nbsp;&nbsp;&nbsp;<input type="radio" name="datefmt" value="yyyy-MM-dd HH:mm:ss" <?php if ($this->_tpl_vars['field_info']['datefmt'] == 'yyyy-MM-dd HH:mm:ss'): ?>checked<?php endif; ?> > 年-月-日 时：分：秒
  </td>
 </tr>

 
<!--下拉框选项设置 begin-->
<tr id="select_options_tr<?php echo $this->_tpl_vars['parent_id']; ?>
" style="display:none;">
 	<td>
 		<div id="select_option_div<?php echo $this->_tpl_vars['parent_id']; ?>
">
 		 	选项设置 <button id="select-btn-plus<?php echo $this->_tpl_vars['parent_id']; ?>
" type="button" class="btn btn-mini btn-primary"><span class="glyphicon glyphicon-plus"></span></button>
 		 	<?php if ($this->_tpl_vars['dinfo']): ?>
				<?php $_from = $this->_tpl_vars['dinfo']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['dkey'] => $this->_tpl_vars['info']):
?>
				<div class="controls">
             		<label for="option"></label>
             		<input name="select_option[]" style="margin-bottom:2px;" type="text" placeholder="选项" value="<?php echo $this->_tpl_vars['info']['name']; ?>
">
             		<button type="button" name="btn-minus" class="btn btn-mini btn-danger">
                 		<span class="glyphicon glyphicon-minus"></span>
             		</button>
        		</div>
				<?php endforeach; endif; unset($_from); ?>
			<?php else: ?>
 				<div class="controls">
             		<label for="option"></label>
             		<input name="select_option[]" style="margin-bottom:2px;" type="text" placeholder="选项" value="">
             		<button type="button" name="btn-minus" class="btn btn-mini btn-danger">
                 		<span class="glyphicon glyphicon-minus"></span>
             		</button>
        		</div>
        		<div class="controls">
             		<label for="option"></label>
             		<input name="select_option[]" style="margin-bottom:2px;" type="text" placeholder="选项" value="">
             		<button type="button" name="btn-minus" class="btn btn-mini btn-danger">
                 		<span class="glyphicon glyphicon-minus"></span>
             		</button>
        		</div>
        	<?php endif; ?>
        </div>
 	</td>
 </tr>
<!--下拉框选项设置 end-->

<!--关联多选设置 -->
<tr class="more_select_jl_tr" style="display:none;">
 	<td>
 	<form class="form-inline">
      <select name="select_jl_1" style="width:90px;">
      	<option value="">--第1级--</option>
      </select>
      <button type="button" name="select_jl-1-btn" class="btn btn-small btn-primary" onclick="set_select_jl_option(1)">设置</button>
      <select name="select_jl_2" style="width:90px;">
      	<option value="">--第2级--</option>
      </select>
      <button type="button" name="select_jl-2-btn" class="btn btn-small btn-primary" onclick="set_select_jl_option(2)">设置</button>
    </form>
    </td>
 </tr>
<!--关联多选设置 end -->
 
<!--级联设置  -->
 <tr class="jl_tr" style="display:none;">
      <td>
          级数
            <label class="checkbox inline">
                <input type="radio" class="radio" name="series" value="2" onclick="change_series()" <?php if ($this->_tpl_vars['field_info']['jl_series'] != 3): ?>checked<?php endif; ?> > 两级
            </label>
            <label class="checkbox inline">
                <input type="radio" class="radio" name="series" value="3"onclick="change_series()" <?php if ($this->_tpl_vars['field_info']['jl_series'] == 3): ?>checked<?php endif; ?> > 三级
            </label>
       </td>
 </tr>
 
 <tr class="jl_tr" style="display:none;">
 	<td>
 	<form class="form-inline" id='jl_series<?php echo $this->_tpl_vars['parent_id']; ?>
'>
      <select name="jl_1" style="width:90px;">
      	<option value="">--第1级--</option>
      </select>
      <button type="button" name="jl-1-btn" class="btn btn-small btn-primary" onclick="set_jl_1()">设置</button>
      <select name="jl_2" style="width:90px;">
      	<option value="">--第2级--</option>
      </select>
      <button type="button" name="jl-2-btn" class="btn btn-small btn-primary" onclick="set_jl_2()">设置</button>
    </form>
    </td>
 </tr>
 
 <tr class="jl_set_tr" style="display:none;">
 	<td>
 		<div id="jl_field_div<?php echo $this->_tpl_vars['parent_id']; ?>
"></div>
 	</td>
 </tr>
 <!--级联设置 end  -->
 
 <!--级联选项、关联多选选项设置-->
 <tr class="jl_option_tr" style="display:none;">
 	<td>
 		<div id="jl_option_div<?php echo $this->_tpl_vars['parent_id']; ?>
"></div>
 	</td>
 </tr>
 <!--级联选项、关联多选选项设置 end-->

 <tr>
 	<td>
 		<div style="text-align:right;">
 			<span class='_fop_msg' style='color:red;'></span>
 			<input type="hidden" name="parent_id<?php echo $this->_tpl_vars['parent_id']; ?>
" id="parent_id<?php echo $this->_tpl_vars['parent_id']; ?>
" value="<?php echo $this->_tpl_vars['parent_id']; ?>
" />
	 		<button type="button" class="btn btn-primary" id="_option_save<?php echo $this->_tpl_vars['parent_id']; ?>
" onclick="_save_submit('<?php echo $this->_tpl_vars['field_info']['field_type']; ?>
',<?php echo $this->_tpl_vars['parent_id']; ?>
)" title="保存设置">
     			<span class="glyphicon glyphicon-saved"></span> 保存设置
			</button>
		</div>
 	</td>
 </tr>
</table>

<script language="javascirpt" type="text/javascript">
var jl_options = {}; //原级联选项
var jl_del_ids = {};//级联要删除的ids
var jl_field_type = {}; //最后一级级联类型记录
$(document).ready(function(){
	change_date_type();
	get_jl_option(1,0,'<?php echo $this->_tpl_vars['parent_id']; ?>
','jl_1');
	<?php if ($this->_tpl_vars['field_info']['jl_field_type']): ?>
	jl_field_type = eval("(" + '<?php echo $this->_tpl_vars['field_info']['jl_field_type']; ?>
' + ")");
	<?php endif; ?>
});

/*字段类型改变*/
function change_date_type()
{
	var select = $('#field_data_type<?php echo $this->_tpl_vars['parent_id']; ?>
').val();
	$('.datefmt').css('display','none');
	$('.more_select_jl_tr').css('display','none');
	$('.jl_tr').css('display','none');
	$('.jl_set_tr').css('display','none');
	$('.jl_option_tr').css('display','none');
	$('#select_options_tr<?php echo $this->_tpl_vars['parent_id']; ?>
').css('display','none');
	$('input[name="field_default"]').attr('disabled',false);
	$('input[name="field_default"]').val('');
    $('input[name="field_require"]').attr('disabled',false);
	if(select==2)
	{
		$('input[name="field_default"]').attr('disabled',true);
		$('#select_options_tr<?php echo $this->_tpl_vars['parent_id']; ?>
').css('display','');
	}
	else if(select==4)
	{
		$('input[name="field_default"]').attr('disabled',true);
		$('.jl_tr').css('display','');
		change_series();
	}
	else if(select==7)
	{
		$('input[name="field_default"]').attr('disabled',true);
		$('input[name="field_require"]').attr('disabled',true);
        $('input[name="field_require"]').attr('checked',false);
		$('.more_select_jl_tr').css('display','');
	}
	else if(select==5)
	{
		$('.datefmt').css('display','');
		$('input[name="field_default"]').val('系统时间');
	}
}

/*下拉框添加选项*/
$('button[name="btn-minus"]').on('click', function() {
	$(this).parent().remove();
});
$('#select-btn-plus<?php echo $this->_tpl_vars['parent_id']; ?>
').click(function(){
	$('#select_option_div<?php echo $this->_tpl_vars['parent_id']; ?>
').append('<div class="controls">\
             	<label for="option"></label>\
             	<input name="select_option[]" style="margin-bottom:2px;" type="text" placeholder="选项" value="">\
             	<button type="button" name="btn-minus" class="btn btn-mini btn-danger">\
                 	<span class="glyphicon glyphicon-minus"></span>\
             	</button>\
        </div>');
	$('button[name="btn-minus"]').on('click', function() {
		$(this).parent().remove();
	});
});

/*级联（关联多选） - 选项添加*/
function add_jl_option()
{
	$('#jl_option_div<?php echo $this->_tpl_vars['parent_id']; ?>
').append('<div class="controls">\
             	<label for="option"></label>\
             	<input name="jl_option[]" style="margin-bottom:2px;" type="text" placeholder="选项" value="">\
             	<input type="hidden" name="jl_id[]" value="0">\
             	<button type="button" name="jl-btn-minus" class="btn btn-mini btn-danger">\
                 	<span class="glyphicon glyphicon-minus"></span>\
             	</button>\
        </div>');
	$('button[name="jl-btn-minus"]').on('click', function() {
		var id = $(this).parent().find('input[name="jl_id[]"]').val();
		if(id!=0 && jl_del_ids[id]==undefined)
		{
			jl_del_ids[id] = id;
		}
		$(this).parent().remove();
	});
}

/*关联多选框，第1级改变，获取第2级选项*/
$('select[name="select_jl_1"]').change(function(){
	$('.jl_set_tr').css('display','none');
	$('.jl_option_tr').css('display','none');
	var p_id = $('select[name="select_jl_1"]').val();
	if(p_id.length==0)
	{
		$('select[name="select_jl_2"]').empty();
	}
	else
	{
		get_jl_option(2,p_id,'<?php echo $this->_tpl_vars['parent_id']; ?>
','select_jl_2');
	}
});

/*第一级改变，获取相应第二级选项*/
$('select[name="jl_1"]').change(function(){
	$('.jl_set_tr').css('display','none');
	$('.jl_option_tr').css('display','none');
	var series = $("input:radio[name='series']:checked").val();
	var p_id = $('select[name="jl_1"]').val();
	$('select[name="jl_2"]').empty();
	$('select[name="jl_2"]').append('<option value="">--第2级--</option>');
	$('select[name="jl_3"]').empty();
	$('select[name="jl_3"]').append('<option value="">--第3级--</option>');
	if(p_id.length!=0)
	{
		if(series==2)
		{
			if(jl_field_type[p_id]==undefined || jl_field_type[p_id]!=1)
			{
				get_jl_option(2,p_id,'<?php echo $this->_tpl_vars['parent_id']; ?>
','jl_2');
			}
		}
		else
		{
			get_jl_option(2,p_id,'<?php echo $this->_tpl_vars['parent_id']; ?>
','jl_2');
		}
	}
});

/*第2级改变，获取相应第3级选项*/
$('select[name="jl_2"]').change(function(){
	var series = $("input:radio[name='series']:checked").val();
	if(series==3)
	{
		$('select[name="jl_3"]').empty();
		$('select[name="jl_3"]').append('<option value="">--第3级--</option>');
		$('.jl_set_tr').css('display','none');
		$('.jl_option_tr').css('display','none');
		var p_id = $('select[name="jl_2"]').val();
		if(p_id.length!=0&&(jl_field_type[p_id]==undefined || jl_field_type[p_id]!=1))
		{
			get_jl_option(3,p_id,'<?php echo $this->_tpl_vars['parent_id']; ?>
','jl_3');
		}
	}
});

/*级联 - 级数改变*/
function change_series()
{
	$('.jl_set_tr').css('display','none');
	$('.jl_option_tr').css('display','none');
	var series = $("input:radio[name='series']:checked").val();
	$('#jl_series<?php echo $this->_tpl_vars['parent_id']; ?>
').find('select[name="jl_3"]').remove();
	$('#jl_series<?php echo $this->_tpl_vars['parent_id']; ?>
').find('button[name="jl-3-btn"]').remove();
	if(series==3)
	{
		$('#jl_series<?php echo $this->_tpl_vars['parent_id']; ?>
').append('\
				<select name="jl_3" style="width:90px;">\
      				<option value="">--第3级--</option>\
      			</select>\
      			<button type="button" name="jl-3-btn" class="btn btn-small btn-primary" onclick="set_jl_3()">设置</button>');
	}
}

/*级联 - 第一级选项设置*/
function set_jl_1()
{
	$('.jl_set_tr').css('display','none');
	$('.jl_option_tr').css('display','');
	load_jl_option(0,1,'jl_1');//获取选项信息
}

/*级联 - 第二级选项设置*/
function set_jl_2()
{
	var p_id = $('select[name="jl_1"]').val();
	if(p_id.length==0)
	{
		$.messager.alert("错误","请先选择第一级选项",'error');
		return;
	}

	var series = $("input:radio[name='series']:checked").val();
	if(series==2)
	{
		$('.jl_set_tr').css('display','');
		$('#jl_field_div<?php echo $this->_tpl_vars['parent_id']; ?>
').empty();
		if(jl_field_type[p_id]!=undefined && jl_field_type[p_id]==1)
		{
			$('#jl_field_div<?php echo $this->_tpl_vars['parent_id']; ?>
').append('本级类型设置\
         			<select name="jl_field_type" style="width:90px;margin-bottom:0px;" onchange="change_jl_field_type('+p_id+',2)">\
         				<option value="0">下拉框</option>\
         				<option value="1" selected>输入框</option>\
         	</select>');
		}
		else
		{
			$('#jl_field_div<?php echo $this->_tpl_vars['parent_id']; ?>
').append('本级类型设置\
         			<select name="jl_field_type" style="width:90px;margin-bottom:0px;" onchange="change_jl_field_type('+p_id+',2)">\
         				<option value="0">下拉框</option>\
         				<option value="1">输入框</option>\
         	</select>');
			load_jl_option(p_id,2,'jl_2');
			$('.jl_option_tr').css('display','');
		}
	}
	else
	{
		load_jl_option(p_id,2,'jl_2');
		$('.jl_set_tr').css('display','none');
		$('.jl_option_tr').css('display','');
	}
}

/*级联 - 第三级选项设置*/
function set_jl_3()
{
	var p_id = $('select[name="jl_2"]').val();
	if(p_id.length==0)
	{
		$.messager.alert("错误","请先选择第二级选项",'error');
		return;
	}
	$('.jl_set_tr').css('display','');
	$('#jl_field_div<?php echo $this->_tpl_vars['parent_id']; ?>
').empty();
	if(jl_field_type[p_id]!=undefined && jl_field_type[p_id]==1)
	{
		$('#jl_field_div<?php echo $this->_tpl_vars['parent_id']; ?>
').append('本级类型设置\
         			<select name="jl_field_type" style="width:90px;margin-bottom:0px;" onchange="change_jl_field_type('+p_id+',3)">\
         				<option value="0">下拉框</option>\
         				<option value="1" selected>输入框</option>\
         	</select>');
	}
	else
	{
		$('#jl_field_div<?php echo $this->_tpl_vars['parent_id']; ?>
').append('本级类型设置\
         			<select name="jl_field_type" style="width:90px;margin-bottom:0px;" onchange="change_jl_field_type('+p_id+',3)">\
         				<option value="0">下拉框</option>\
         				<option value="1">输入框</option>\
         	</select>');
		load_jl_option(p_id,3,'jl_3');
		$('.jl_option_tr').css('display','');
	}
}

/*级联最后一级类型改变*/
function change_jl_field_type(p_id,deep)
{
	var type = $('select[name="jl_field_type"]').val();
	jl_field_type[p_id] = type;
	if(type==1)
	{
		$('.jl_option_tr').css('display','none');
	}
	else
	{
		load_jl_option(p_id,deep,'jl_'+deep);
		$('.jl_option_tr').css('display','');
	}
}

/*级联 - 加载级联选项*/
function load_jl_option(p_id,deep,field_name)
{
	$('#jl_option_div<?php echo $this->_tpl_vars['parent_id']; ?>
').empty();
	$('#jl_option_div<?php echo $this->_tpl_vars['parent_id']; ?>
').parent().find('button').remove();
	$('#jl_option_div<?php echo $this->_tpl_vars['parent_id']; ?>
').parent().find('span').remove();
	$('#jl_option_div<?php echo $this->_tpl_vars['parent_id']; ?>
').append('第'+deep+'级选项设置\
		<button type="button" class="btn btn-mini btn-primary" onclick="add_jl_option()"><span class="glyphicon glyphicon-plus"></span></button>\
		<button type="button" class="btn btn-small btn-primary set_jl_option_btn" onclick="save_jl_option(\'<?php echo $this->_tpl_vars['parent_id']; ?>
\','+deep+','+p_id+',\''+field_name+'\',<?php echo $this->_tpl_vars['field_info']['field_type']; ?>
)">保存选项</button>\
		<span class="set_jl_msg" style="color:red"></span>');
	if(jl_options[p_id]!=undefined && jl_options[p_id].length!=0)
	{
		$.each(jl_options[p_id],function(id,option){
			$('#jl_option_div<?php echo $this->_tpl_vars['parent_id']; ?>
').append('<div class="controls">\
             	<label for="option"></label>\
             	<input name="jl_option[]" style="margin-bottom:2px;" type="text" placeholder="选项" value="'+option+'">\
             	<input type="hidden" name="jl_id[]" value="'+id+'">\
             	<button type="button" name="jl-btn-minus" class="btn btn-mini btn-danger">\
                 	<span class="glyphicon glyphicon-minus"></span>\
             	</button>\
        	</div>');
		});
	}
	else
	{
		$('#jl_option_div<?php echo $this->_tpl_vars['parent_id']; ?>
').append('<div class="controls">\
             	<label for="option"></label>\
             	<input name="jl_option[]" style="margin-bottom:2px;" type="text" placeholder="选项" value="">\
             	<input type="hidden" name="jl_id[]" value="0">\
             	<button type="button" name="jl-btn-minus" class="btn btn-mini btn-danger">\
                 	<span class="glyphicon glyphicon-minus"></span>\
             	</button>\
        	</div>');
	}
	$('button[name="jl-btn-minus"]').on('click', function() {
		var id = $(this).parent().find('input[name="jl_id[]"]').val();
		if(id!=0 && jl_del_ids[id]==undefined)
		{
			jl_del_ids[id] = id;
		}
		$(this).parent().remove();
	});
}

/*级联 - 保存选项*/
function save_jl_option(field_id,type,p_id,field_name,field_type)
{
	var _data = {};
	var ids = document.getElementsByName('jl_id[]');
	$('[name="jl_option[]"]:input').each(function(i){
		if(ids[i].value!=0)
		{
			_data[i] = {'id':ids[i].value,'name':this.value};
		}
		else if(this.value!='')
		{
			_data[i] = {'id':ids[i].value,'name':this.value};
		}
	});
	var x = ids.length;
	$.each(jl_del_ids,function(index,del_id){
		_data[x] = {'id':del_id,'name':''};
		x++;
	});
	$('.set_jl_option_btn').attr('disabled',true);
	$.ajax({
		type:'post',
		url:'index.php?c=field_confirm&m=save_jl',
		data:{"p_id":p_id,"options":_data,'type':type,'field_id':field_id,'field_type':field_type},
		dataType:"json",
		success: function(responce){
			if(responce['error']==0)
			{
				get_jl_option(type,p_id,field_id,field_name);
				jl_del_ids = {};
				$('.set_jl_msg').html('&nbsp;&nbsp;<img src="./themes/default/icons/ok.png" />&nbsp;操作成功');
				setTimeout(function(){
					$('.set_jl_msg').html('');
					$('.set_jl_option_btn').attr('disabled',false);
					$('.jl_option_tr').css('display','none');
					$('.jl_set_tr').css('display','none');
				},1000);
			}
			else
			{
				$.messager.alert("错误","保存失败",'error');
			}
		}
	});
}

/*获取级联选项信息*/
function get_jl_option(deep,p_id,field_id,field_name)
{
	$.ajax({
		type:'POST',
		url: "index.php?c=field_confirm&m=get_jl_options",
		data:{"parent_id":p_id,"type":deep,"field_id":field_id},
		dataType:"json",
		success: function(responce){
			if(responce['error']=='0')
			{
				jl_options[p_id] = {};
				jl_options[p_id] = responce['content'];
				if(p_id==0)
				{
					$('select[name="select_'+field_name+'"]').empty();
					$('select[name="select_'+field_name+'"]').append('<option value="">--第'+deep+'级--</option>');
					$.each(responce['content'],function(id,value){
						$('select[name="select_'+field_name+'"]').append('<option value="'+id+'">'+value+'</option>');  //添加一项option
					});
				}
				$('select[name="'+field_name+'"]').empty();
				$('select[name="'+field_name+'"]').append('<option value="">--第'+deep+'级--</option>');
				$.each(responce['content'],function(id,value){
					$('select[name="'+field_name+'"]').append('<option value="'+id+'">'+value+'</option>');  //添加一项option
				});
			}
			else
			{
				$.messager.alert('错误',"<br>".responce['message'],'error');
			}
		}
	});
}

/*保存设置*/
function _save_submit(field_type,parent_id)
{
	var data = {};
	$('#_option_save'+parent_id).attr('disabled',true);
	data.parent_id = $('#parent_id'+parent_id).val();//字段id
	data.default = $("input[name='field_default']").val();//默认值
	data.datefmt = '';
	data.if_require = 1;
	if($('input:checkbox[name="field_require"]').attr('checked') == 'checked')
	{
		data.if_require = 2;//字段必选
	}
	var data_type = $('#field_data_type'+parent_id).val();//字段类型
	data.data_type = data_type;
	if(data_type==2)
	{
		//得到value值
		var v_data={};
		$('input[name="select_option[]"]') .each(function(i){
			v_data[i]=this.value;
		});
		data.options = v_data;
	}
	else if(data_type==4)
	{
		data.jl_series = $("input:radio[name='series']:checked").val();//级数
		data.jl_field_type = json2str(jl_field_type);
	}
	else if(data_type==5)
	{
		data.datefmt = $("input:radio[name='datefmt']:checked").val();
	}
	$.ajax({
		type:'post',
		url:'index.php?c=field_confirm&m=save_fields',
		data:data,
		dataType:"json",
		success: function(responce){
			if(responce['error']==0)
			{
				$('._fop_msg').html('<img src="./themes/default/icons/ok.png" />&nbsp;操作成功');
				setTimeout(function(){
					$('#_field_confirm_options_'+field_type).window('close');
				},300);
			}
			else
			{
				$('._fop_msg').html('操作失败');
			}
		}
	});
}

/*关联级联*/
function set_select_jl_option(deep)
{
	$('.jl_set_tr').css('display','none');
	$('.jl_option_tr').css('display','');

	var p_id = 0;
	if(deep==2)
	{
		p_id = $('select[name="select_jl_1"]').val();
		if(p_id.length==0)
		{
			$.messager.alert("错误","请先选择第1级选项",'error');
			return;
		}
	}
	load_jl_option(p_id,deep,'select_jl_'+deep);
}

function delete_old_options()
{
	
}
</script>