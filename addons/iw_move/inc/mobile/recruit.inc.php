<?php 

	global $_W,$_GPC;
	$action = 'recruit';
	$url = $this->createMobileUrl($action, array('op' => 'list'));
	
	// $uid = $_W['fans']['uid'];//获取当前用户的uid
	// $openid = $_W['fans']['openid'];//获取当前用户的openid
	// $avatar = $_W['fans']['tag']['avatar'];//获取当前用户的头像信息
	// $nickname = $_W['fans']['tag']['nickname'];//获取当前用户的昵称信息
	// $uniacid = $_W['fans']['uniacid'];//获取当前用户的昵称信息
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';

	if ($operation == "list") {
		
		//查找信息
		$recruit = pdo_fetch("SELECT * FROM ".tablename("move_boss")." WHERE status=0 ORDER BY id DESC ");
		$recruit['content'] = htmlspecialchars_decode($recruit['content']); 
		//加载页面
		include $this->template("recruit");
	}



 ?>