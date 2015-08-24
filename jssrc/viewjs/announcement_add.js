$(document).ready(function(){
	var editor = $('#ancontent').xheditor({skin:'o2007silver',width:'99%',height:'350',layerShadow:3,forcePtag:false,editorRoot:'./jssrc/xheditor/',emots:{msn:{name:'MSN',count:40,width:22,height:22,line:8}},
	upImgUrl:"./jssrc/xheditor/upload.php",upImgExt:"jpg,jpeg,gif,png",upFlashUrl:"./jssrc/xheditor/upload.php",upFlashExt:"swf",upMediaUrl:"./jssrc/xheditor/upload.php",upMediaExt:"avi"});

	$('#department').combotree({
		url:'index.php?c=department&m=get_department_tree',
		onClick:function(node){
				$(this).tree('expand', node.target);
		},
		onBeforeLoad : function(node, param){
			if (node){
				return false;
			} else {
				$('#department').combotree('options').url = "index.php?c=department&m=get_department_tree";
			}
		}
	});
});
function  checkvalue()
{
	var acontent = $("#ancontent").val();
	var title=$("#title_add_ans").val();
	if(title!="" && acontent!=""){
		$('#add_anns').attr('disabled',true);
		$('#theForm').submit();
	}
	else{
		$.messager.alert('保存失败','缺少标题或者内容！');
	}
}