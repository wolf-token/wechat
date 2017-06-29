<?php 


	global $_W,$_GPC;
	$action = 'estimate';
	$uniacid = $_W['uniacid'];
	$uid = intval($_W['uid']);
	$admin = pdo_fetch('SELECT * FROM ' . tablename('users_permission') . ' WHERE uniacid = :aid AND uid = :uid AND type = :type', array(':aid' => $uniacid, ':uid' => $uid, ':type' => "system"));
	$url = $this->createWebUrl($action, array('op' => 'list'));
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';

 if ($operation == "list") {
		
		//设置条件
		$where = '';
		$params = array();
		//判断是否有搜索
		if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

			$where.=' AND `name` LIKE :keyword OR `phone` LIKE :keywordl OR `plat` LIKE :keywords ';
			
			$params[':keyword'] = "%{$_GPC['keyword']}%";
			$params[':keywordl'] = "%{$_GPC['keyword']}%";
			$params[':keywords'] = "%{$_GPC['keyword']}%";
			
		}
		
		// //查取数据
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_declare')." WHERE status=3 AND appraise=1 ".$where);
		$pager = pagination($total, $pindex, $psize,$params);
		//查取用户信息
		$estimate = pdo_fetchall("SELECT * FROM ".tablename('move_declare')." WHERE status=3 AND appraise=1 ".$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
		
		
		//加载页面
		include $this->template("estimate",$admin);

	}else if ($operation == "dispose") {
		
		//设置条件
		$where1 = '';
		$params1 = array();
		//判断是否有搜索
		if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

			$where1.=' AND `name` LIKE :keyword OR `phone` LIKE :keywordl OR `plat` LIKE :keywords ';
			
			$params1[':keyword'] = "%{$_GPC['keyword']}%";
			$params1[':keywordl'] = "%{$_GPC['keyword']}%";
			$params1[':keywords'] = "%{$_GPC['keyword']}%";
			
		}
		
		// //查取数据
		$pindex1 = max(1, intval($_GPC['page']));
		$psize1 = 20;
		$total1 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_declare')." WHERE status=3 AND appraise=1 ".$where1);
		$pager1 = pagination($total1, $pindex1, $psize1,$params1);
		//查取用户信息
		$nothing = pdo_fetchall("SELECT * FROM ".tablename('move_declare')." WHERE status=3 AND appraise=0 ".$where1." ORDER BY id ASC LIMIT ". ($pindex1 - 1) * $psize1 . ',' . $psize1,$params1);
		
		
		//加载页面
		include $this->template("nothing",$admin);

	}else if ($operation == "edit") {
		
		//查取信息
		$estimate_edit = pdo_fetch("SELECT * FROM ".tablename("move_declare")." WHERE id=:id",array("id"=>$_GPC['id']));
		
				//判断
		if (!empty($estimate_edit['pid'])) {
			
			//查找分配员工信息
			$staff = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$estimate_edit['pid']));

		}
		//查找推荐人信息
		if ($estimate_edit['come'] == 0 ) {
			
			$recommend = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$estimate_edit['uid']));
		}
		if ($estimate_edit['come'] == 1 ) {
			//判断是否有信息
			if ($estimate_edit['uid'] == 0) {
				
				//查找信息
				$recommend =  pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE openid=:openid",array("openid"=>$estimate_edit['openid']));
				if (!empty($recommend)) {
					//修改信息
					$rr1 = pdo_update("move_declare",array("uid"=>$recommend['id']));
				}
				
			}else{

				$recommend = pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE id=:id",array("id"=>$estimate_edit['uid']));
			}
			// $recommend = pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE id=:id",array("id"=>$estimate_edit['uid']));
		}

		//加载页面
		include $this->template("estimate_edit",$recommend,$staff);
		
	}else if ($operation == "update") {
		
		// var_dump($_POST);die();
		//判断是否为post
		if ($_W['ispost']) {
			
			//判断是否评价星级
			if ($_POST['star'] != "0") {
				
				// var_dump($_POST);die();
				//接收数据
				$update['star'] = $_POST['star'];
				$update['appraise'] = 1;

				//修改数据
				$res = pdo_update("move_declare",$update,array("id"=>$_POST['id']));

				//判断是否评价成功
				if (!empty($res)) {
					
					 message("评价成功",$this->createWebUrl("estimate",array("op"=>"list")));
				}else{

					message("对不起您评价失败！");
				}
			}else{

				message("对不起评价星级不能为0！");

			}
		}

	}else if ($operation == "down") {
		
		include $this->template("estimate_down");
		
	}else if ($operation == "estimate_delete") {
		
		//判断是否为post
		if ($_W['ispost']) {
			
			//判断是否为空
			if (!empty($_POST['ipn-id'][0])) {
				
				// $pid = array();
				//接收数据
				$pid = implode(",",$_GPC['ipn-id']);
				// var_dump($pid);die();
				//修改数据
				$sql = "DELETE FROM ims_move_declare WHERE id in (".$pid.")";
				
				$rese = pdo_query($sql);
				//判断是否删除成功
				if (!empty($rese)) {
					
					message("删除成功",$url,"success");
				}else{

					message("删除失败",$url,"error");
				}
			}else{

				message("对不起请选择要删除的数据",$url,"error");
			}

		}
	}else if ($operation == "time") {
		
		include $this->template("estimate_delete");
	}


 ?>