<?php 

	global $_W,$_GPC;
	$action = 'interact';
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
		$interact_list = pdo_fetchall("SELECT * FROM ".tablename("move_interact")." WHERE status=0 ORDER BY id ASC ");
		
		
		//加载页面
		include $this->template("interact_list");

	}else if ($operation == "details") {
		
		 $interact = pdo_fetch("SELECT * FROM ".tablename("move_interact")." WHERE status=0 AND id=:id ",array("id"=>$_GPC['id']));
		 $interact['content'] = htmlspecialchars_decode($interact['content']);

		 include $this->template("interact");

	}



 ?>