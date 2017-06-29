<?php 


	global $_W,$_GPC;
	$action = 'control';
	$uniacid = $_W['uniacid'];
	$uid = intval($_W['uid']);
	$admin = pdo_fetch('SELECT * FROM ' . tablename('users_permission') . ' WHERE uniacid = :aid AND uid = :uid AND type = :type', array(':aid' => $uniacid, ':uid' => $uid, ':type' => "system"));
	$url = $this->createWebUrl($action, array('op' => 'list'));
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';

 if ($operation == "list") {
		
		//查找数据
 		$control = pdo_fetch("SELECT * FROM ".tablename("move_control")." ORDER BY ID DESC");
		
		// echo "nice";die;
		//加载页面
		include $this->template("control",$admin);

	}

 ?>