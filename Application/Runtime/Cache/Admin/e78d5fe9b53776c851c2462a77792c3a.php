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
			当前位置：首页&nbsp;&gt;&nbsp;角色管理
		</div>

		<div class="result-wrap">
			<div class="result-content">
				<form name="form1" id="form1" action="javascript:;" method="post">
					<table class="insert-tab">
						<tr>
							<th>名称：</th>
							<td><input class="inpstyle w300" id="" name="name" value="" type="text">&nbsp;&nbsp;<span style='color:#3ccecb;'>必填</span></td>
						</tr>
						<tr>
							<th>职责：</th>
							<td><input class="inpstyle w300" id="" name="duty" value="" type="text">&nbsp;&nbsp;<span style='color:#3ccecb;'>必填</span></td>
						</tr>
						<tr>
							<th></th>
							<td>
								<input class="butstyle mr10 bg1" value="提交" type="button" onclick="add_data()">
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		add_status=true;
		function add_data(){
		if(add_status){
			add_status=false;
			$.ajax({
				url :"/index.php/Admin/Basic/role_add_ajax",
				type : 'post',
				data :$("#form1").formSerialize(),
				dataType :'json',
				success : function(data) {
					if(data.success==1){
						Boxy.alert(data.message,function(){
							location.href='/index.php/Admin/Basic/rolelist';
						});return false;
					}else{
						add_status=true;
						Boxy.alert(data.message, null);return false;
					}
				}
			});
		}else{
			Boxy.alert("信息正在提交...", null);return false;
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