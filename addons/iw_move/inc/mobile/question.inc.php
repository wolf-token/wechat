<?php 

	
	global $_W,$_GPC;
	$action = 'question';
	$url = $this->createMobileUrl($action, array('op' => 'list'));
	
	$uid = $_W['fans']['uid'];//获取当前用户的uid
	$openid = $_W['fans']['openid'];//获取当前用户的openid
	$avatar = $_W['fans']['tag']['avatar'];//获取当前用户的头像信息
	$nickname = $_W['fans']['tag']['nickname'];//获取当前用户的昵称信息
	$uniacid = $_W['fans']['uniacid'];//获取当前用户的昵称信息
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';

	if ($operation == "list") {
		
		//查找分类信息
		$question = pdo_fetchall("SELECT * FROM ".tablename("move_type"));
		// $question['content'] = htmlspecialchars_decode($question['content']);
		//加载页面
		include $this->template("question");

	}else if ($operation == "details") {
		
		//查找所属分类常见问题信息
		$type = pdo_fetchall("SELECT * FROM ".tablename("move_question")." WHERE uid=:uid",array("uid"=>$_GPC['id']));
		// $question['content'] = htmlspecialchars_decode($question['content']);
		//加载页面
		include $this->template("type");

	}else if ($operation == "edit") {
		
		//查找所属分类常见问题信息
		$last = pdo_fetch("SELECT * FROM ".tablename("move_question")." WHERE id=:id",array("id"=>$_GPC['id']));
		$last['content'] = htmlspecialchars_decode($last['content']);
		//加载页面
		include $this->template("last");
	}




 ?>