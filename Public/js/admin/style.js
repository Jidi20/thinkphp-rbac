/* 
 * 对话框使用方法
 * 
	//单选对话框
	function boxy_ask(){
		Boxy.ask("你感觉怎么样?", ["很好", "还好", "不好"], function(val) {
			alert("你选择的是: " + val);
		});
		return false;
	}
	//普通对话框
	function boxy_alert(){
		Boxy.alert("文件未找到", null);
		return false;
	}
	//确认对话框
	function boxy_confirm(){
		Boxy.confirm("请确认:", function() { alert("已经确认!"); });
		return false;
	}
*/
//计算中间和中间左、右的高度
$(function(){
	//计算中间内容的高
	var bh=document.documentElement.clientHeight-163;
	$("#container_height,.container-left,.container-right").css("height",bh+"px");
	//滑动条
	$(".container-left,.container-right").mCustomScrollbar({
		autoHideScrollbar:true,
		theme:"dark-2"
	});
});

//复选框 
function checked_all(){
	if(document.getElementById('checked_all').checked){
		$(".cboxall").attr("checked",true);
	}else{
		$(".cboxall").attr("checked",false);
	}
}