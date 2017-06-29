<?php 


	global $_W,$_GPC;
	$action = 'refuse';
	$uniacid = $_W['uniacid'];
	$uid = intval($_W['uid']);
	$admin = pdo_fetch('SELECT * FROM ' . tablename('users_permission') . ' WHERE uniacid = :aid AND uid = :uid AND type = :type', array(':aid' => $uniacid, ':uid' => $uid, ':type' => "system"));
	$url = $this->createWebUrl($action, array('op' => 'refuse'));
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'refuse';

	if ($operation == "refuse") {
		
		//设置条件
		$where6 = '';
		$params6 = array();
		//判断是否有搜索
		if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

			$where6.=' AND `name` LIKE :keyword OR `phone` LIKE :keywordl OR `plat` LIKE :keywords ';
			
			$params6[':keyword'] = "%{$_GPC['keyword']}%";
			$params6[':keywordl'] = "%{$_GPC['keyword']}%";
			$params6[':keywords'] = "%{$_GPC['keyword']}%";
			
		}
		
		// //查取数据
		$pindex6 = max(1, intval($_GPC['page']));
		$psize6 = 20;
		$tota6 =  pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_declare')." WHERE status=5 ".$where6);
		$pager6 = pagination($tota6, $pindex6, $psize6,$params6);
		//查取用户信息
		$refuse_list = pdo_fetchall("SELECT * FROM ".tablename('move_declare')." WHERE status=5 ".$where6." ORDER BY id ASC LIMIT ". ($pindex6 - 1) * $psize6 . ',' . $psize6,$params6);
		
		
		//加载页面
		include $this->template("refuse_list",$admin);
		
	}