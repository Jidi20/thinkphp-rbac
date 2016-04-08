<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo (C("SITE_NAME")); ?></title>
	<link rel="stylesheet" type="text/css" href="/Public/css/admin/common.css"/>
	<link rel="stylesheet" type="text/css" href="/Public/css/admin/main.css"/>
	<link rel="stylesheet" type="text/css" href="/Public/css/admin/page.css"/>
	<link rel="stylesheet" type="text/css" href="/Public/css/admin/jquery.mCustomScrollbar.css"/>
	<script type="text/javascript" language="javascript" src="/Public/js/admin/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="/Public/js/admin/style.js"></script>
	<script type="text/javascript" language="javascript" src="/Public/js/admin/jquery.form.js"></script>
	<script type="text/javascript" language="javascript" src="/Public/js/admin/jquery.mCustomScrollbar.min.js"></script>
	<!-- 对话框的 css 和 js -->
	<link rel="stylesheet" href="/Public/css/admin/boxy.css" type="text/css" />
	<script type="text/javascript" src="/Public/js/admin/jquery.boxy.js"></script>
	<!--图片上传插件-->
	<script type="text/javascript" src="/Public/js/admin/Ajax.js"></script>
	<script type="text/javascript" src="/Public/swfupload/swfupload/swfupload.js"></script><script type="text/javascript" src="/Public/swfupload/js/swfupload.queue.js"></script><script type="text/javascript" src="/Public/swfupload/js/fileprogress.js"></script><script type="text/javascript" src="/Public/swfupload/js/handlers.js"></script><script type="text/javascript" src="/Public/js/admin/ajaxupload.js"></script>
	<script type="text/javascript">
	var session_id = "<?php echo session_id(); ?>";
	var public = "/Public";
	</script>
	<!-- 对话框的 css 和 js -->
	<script type="text/javascript" src="/Public/js/admin/jsAddress.js"></script>
</head>
<body>
<div class="header">
	<div class="header_logo fl"><a href="#"><img src="/Public/images/admin/logo.png"></a></div>
	<div class="header_menu">
		<ul>
			<?php
 $nav_list = session('nav_list'); $nav_list = gettree2($nav_list); ?>
			<?php foreach($nav_list as $ka1=>$va1){?>
				<li>
					<a href="/index.php/Admin/<?php echo $va1['title']?>/index" gm="<?php echo $va1['title']?>" class="menulista"><?php echo $va1['name']?></a>
				</li>
			<?php } ?>
		</ul>
	</div>
	<div class="header_user">
		<ul>
			<li>欢迎您：<?php echo session('name')?></li>
			<li><a href="/index.php/Admin/Index/changepassword">修改密码</a></li>
			<li><a href="###" onclick="login_out();">退出</a></li>
		</ul>
	</div>
</div>
<div class="container clearfix global" id="container_height">
	<div class="container-left">
		<?php foreach($nav_list as $k1=>$v1){?>
			<?php foreach($v1['son'] as $k2=>$v2){?>
				<?php if(CONTROLLER_NAME==$v1['title']){ ?>
					<div class="container-left-title"><?php echo $v2['name']?></div>
					<ul class="sidebar-list">
						<?php foreach($v2['son'] as $k3=>$v3){?>
							<?php if($v3['is_view']!="0"){?>
								<li><a href="/index.php/Admin/<?php echo $v1['title']?>/<?php echo $v3['title']?>" class="menulistb" gmb="<?php echo $v3['title']?>"><?php echo $v3['name']?></a></li>
							<?php }?>
						<?php }?>
					</ul>
				<?php }?>
			<?php }?>
		<?php }?>
	</div>

    
	<div class="container-right">
		<div class="container-right-title">
			当前位置：首页&nbsp;&gt;&nbsp;节点分配
		</div>

		<div class="result-wrap">
			<div class="result-content">
				<form action="/index.php/Admin/Basic/node_add_ajax" method="post">
					<table class="insert-tab">
						<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i;?><tr>
								<td style="width:100px;"></td>
									<td>
										<div style="height:30px;line-height:30px;width:100%;">
											<input type="checkbox" style="margin-right:5px;" name="module_id[]" value="<?php echo ($vo1["id"]); ?>" class="lista<?php echo ($vo1["id"]); ?>" onclick="all_checked(<?php echo ($vo1["id"]); ?>)" id="checkbox<?php echo ($vo1["id"]); ?>" <?php if(in_array(($vo1["id"]), is_array($module_ids)?$module_ids:explode(',',$module_ids))): ?>checked<?php endif; ?> ><label for="checkbox<?php echo ($vo1["id"]); ?>"><?php echo ($vo1["name"]); ?></label>
										</div>
										<?php if(is_array($vo1["son"])): $i = 0; $__LIST__ = $vo1["son"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($i % 2 );++$i;?><div style="height:30px;line-height:30px;width:100%;padding-left:30px;">
												<input type="checkbox" style="margin-right:5px;" class="showa<?php echo ($vo1["id"]); ?> listb<?php echo ($vo2["id"]); ?>" id-date="<?php echo ($vo1["id"]); ?>" name="module_id[]" value="<?php echo ($vo2["id"]); ?>" id="checkbox<?php echo ($vo2["id"]); ?>" onclick="all_checked2(<?php echo ($vo2["id"]); ?>)" <?php if(in_array(($vo2["id"]), is_array($module_ids)?$module_ids:explode(',',$module_ids))): ?>checked<?php endif; ?>><label for="checkbox<?php echo ($vo2["id"]); ?>"><?php echo ($vo2["name"]); ?></label>
											</div>
												<?php if(is_array($vo2["son"])): $i = 0; $__LIST__ = $vo2["son"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo3): $mod = ($i % 2 );++$i;?><div style="line-height:30px;width:100%;padding-left:60px;">
													<div style="float:left;width:200px;">
														<input type="checkbox" style="margin-right:5px;" class="showa<?php echo ($vo1["id"]); ?> showb<?php echo ($vo2["id"]); ?> listc<?php echo ($vo3["id"]); ?>" id-date1="<?php echo ($vo1["id"]); ?>" id-date2="<?php echo ($vo2["id"]); ?>" name="module_id[]" value="<?php echo ($vo3["id"]); ?>" onclick="all_checked3(<?php echo ($vo3["id"]); ?>)" id="checkbox<?php echo ($vo3["id"]); ?>" <?php if(in_array(($vo3["id"]), is_array($module_ids)?$module_ids:explode(',',$module_ids))): ?>checked<?php endif; ?>><label for="checkbox<?php echo ($vo3["id"]); ?>"><?php echo ($vo3["name"]); ?></label>
													</div>
													<div style="clear:both;"></div>
												</div><?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
									</td>
							</tr><?php endforeach; endif; else: echo "" ;endif; ?>
						<tr>
							<th></th>
							<td>
								<input class="butstyle mr10 bg1" value="提交" type="submit">
							</td>
						</tr>
					</table>
					<input type="hidden" value="<?php echo ($id); ?>" name="id">
				</form>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		//第一级选中
    	function all_checked(id) {
    		if($('.lista'+id).attr('checked')){
				$(".showa" + id).attr("checked", true);
    		}else{
    			$(".showa" + id).attr("checked", false);
    		}
    	}
		//第二级选中
    	function all_checked2(id) {
	        if($('.listb'+id).attr('checked')){
	        	$(".showb" + id).attr("checked", true);
	        	$(".lista" + $(".listb" + id).attr("id-date")).attr("checked", true);
	        }else{
	        	$(".showb" + id).attr("checked", false);
	        	var st=1;
	        	$(".showa"+$('.listb'+id).attr("id-date")).each(function(){
	        		if($(this).attr('checked')){
	        			st=2;
	        		}
	        	});
	        	if(st==1){
	        		$(".lista" + $(".listb" + id).attr("id-date")).attr("checked", false);
	        	}
	        }
    	}
    	//第三级选中
		function all_checked3(id){
			if($('.listc'+id).attr('checked')){
				$(".lista" + $(".listc" + id).attr("id-date1")).attr("checked", true);
				$(".listb" + $(".listc" + id).attr("id-date2")).attr("checked", true);
			}else{
				var st=1;
	        	$(".showb"+$('.listc'+id).attr("id-date2")).each(function(){
	        		if($(this).attr('checked')){
	        			st=2;
	        		}
	        	});
	        	if(st==1){
					$(".listb" + $(".listc" + id).attr("id-date2")).attr("checked", false);
					var st1=1;
		        	$(".showa"+$(".listc" + id).attr("id-date1")).each(function(){
		        		if($(this).attr('checked')){
		        			st1=2;
		        		}
		        	});
		        	if(st1==1){
						$(".lista" + $(".listc" + id).attr("id-date1")).attr("checked", false);
		        	}
	        	}
			}
    	}
    </script>
</div>
<div class="footer">
	<div>版权所有&nbsp;&copy;&nbsp;2014-2015&nbsp;南昌微聚科技有限公司&nbsp;并保留所有权利</div>
</div>
<script type="text/javascript">
	$(".menulista").each(function(){
		if($(this).attr("gm")=="<?php echo (CONTROLLER_NAME); ?>"){
			$(this).addClass("astyle");
		}
	});
	$(".menulistb").each(function(){
		if($(this).attr("gmb")=="<?php echo (ACTION_NAME); ?>"){
			$(this).addClass("aselected");
		}
	});
	function login_out(){
		Boxy.confirm("退出系统？", function() {
			$.post('/index.php/Admin/Public/logout',{},function(){
				window.location.href='/index.php/Admin/Public/login';
			});
		});
		return false;
	}
</script>
</body>
</html>