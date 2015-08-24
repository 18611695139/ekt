/**
* 必填项样式设置
*/
$(document).ready(function(){
	var second = 0;
	//表单提交事件
	$("form").submit(function() {
        var status = [];
		//循环遍历表单中是否有required属性
        //console.log($("input,select,textarea,label").not("[type=hidden]"));
		$("input,select,textarea,label").not("[type=hidden]").each(function(index,item) {
			//假如存在该属性，择报错，提示该选项必填
			if(typeof($(item).attr("require")) != "undefined")
			{
				var value = "";
				if($(item).parents(".control-group").attr('type') == 'select')
				{
					
					var selected = "#" + $(item).attr('id') + " option:selected";
					value = $(selected).val();
				}
				else if($(item).parents(".control-group").attr('type') == 'checkbox' || $(item).parents(".control-group").attr('type') == 'radio' || $(item).parents(".control-group").attr('type') == 'gender')
				{
					value = $(item).parent().find('.controls').children().has(":checked").length;
				}
				else
				{
					value = $(item).val();
				}

				if(value == "" || value == 0)
				{

					$(item).parents(".control-group").addClass('error');

					if(second == 0)
					{
						var reg = /^.*(input-append|input-prepend).*(input-append)*.*$/gi;
						if(reg.test($(item).parent().attr('class')))
						{
							$("<div class='help-inline' style='color: red;'>此选项必填.</div>").appendTo($(item).parent());
						}
						else if($(item).parents(".control-group").attr('type') == 'checkbox' || $(item).parents(".control-group").attr('type') == 'radio' || $(item).parents(".control-group").attr('type') == 'gender')
						{
							$("<span class='help-inline' style='color: red;'>此选项必填.</span>").appendTo($(item).parent().find('.controls'));
						}
						else if($(item).parents(".control-group").attr('type') == 'cascade')
						{
							$(item).parent().find(".help-inline").remove();
							var require = $(item).parents(".control-group").attr('require');
							$("<div class='help-inline' style='color: red;'>此选项前"+require+"必填.</div>").appendTo($(item).parent());
						}
						else
						{
							$("<div class='help-inline' style='color: red;'>此选项必填.</div>").insertAfter($(item));
						}
					}
					status.push(0);
				}
                else
                {
                    status.push(1);
                }
			}
		});
		second ++
        for (var i in status)
        {
            if( status[i] == 0)
            {
                return false;
            }
        }
		return true;
	});
});