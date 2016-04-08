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
			<div class="result-title">
				<div class="result-list">
					<div class="result-list-left">
						<a href="/index.php/Admin/Basic/add"><input type="button" class="butstyle mr10 bg1" value="新增角色"></a>					</div>
					<div class="result-list-right">
						<ul>
							<li>
								<input type="text" class="inpstyle" value="<?php echo ($_GET['keyword']); ?>" name="keyword" id="keyword">
							</li>
							<li>
								<input type="button" class="butstyle bg1" value="搜索" onclick="search();">
							</li>
						</ul>
					</div>
				</div>
			</div>

			<div class="result-content">
				<table class="result-tab" width="100%">
					<tr>
						<th class="tc" width="5%"><input id="checked_all" name="" type="checkbox" onclick="checked_all();"></th>
						<th>编号</th>
						<th>名称</th>
						<th>职责</th>
						<!-- <th>状态</th> -->
						<th>操作</th>
					</tr>
					<?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
							<td class="tc"><input name="" class="cboxall" value="" type="checkbox" checkedId='<?php echo ($vo["id"]); ?>'></td>
							<td><?php echo ($vo["id"]); ?></td>
							<td><?php echo ($vo["name"]); ?></td>
							<td><?php echo ($vo["duty"]); ?></td>
							<!-- <td><?php if($vo["status"] == 0): ?><span class="c1">启用</span><?php else: ?><span class="c2">停用</span><?php endif; ?></td> -->
							<td>
								<a href="/index.php/Admin/Basic/edit/id/<?php echo ($vo["id"]); ?>" class="c1">修改</a> |
								<a href="###" class="c1" onclick="delete_one('<?php echo ($vo["id"]); ?>');">删除</a>
							</td>
						</tr><?php endforeach; endif; ?>
				</table>
				<div class="result-content-page">
					<div class="result-list">
						<div class="result-list-left">
						</div>
						<div class="result-list-right">
							<div class="green-black"><?php echo ($page); ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="checkedAllIds" value="" />
	<script type="text/javascript">
		//删除单个
		function delete_one(id){
			Boxy.confirm("确定删除？", function() {
				$.post('/index.php/Admin/Basic/Roledelete',{id:id},function(data){
					if(data.success == 1){
	 					window.location.href="/index.php/Admin/Basic/index";
		 			}else{
		 				Boxy.alert(data.message, null);
						return false;
		 			}
				},'json');
			});
			return false;
		}

		//搜索
		function search(){
			var keyword = $("#keyword").val();
			var url = '/index.php/Admin/Basic/index';
			if(keyword != ''){
				url += '/keyword/'+keyword;
			}
			location.href = url;
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