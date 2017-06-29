<?php 


	global $_W,$_GPC;
	$action = 'staff';
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

			$where.=' WHERE `name` LIKE :keyword OR `phone` LIKE :keywordl OR `tid` LIKE :keywords OR `team` LIKE :keyworda';
			
			$params[':keyword'] = "%{$_GPC['keyword']}%";
			$params[':keywordl'] = "%{$_GPC['keyword']}%";
			$params[':keywords'] = "%{$_GPC['keyword']}%";
			$params[':keyworda'] = "%{$_GPC['keyword']}%";
			
		}
		
		// //查取数据
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_staff').$where);
		$pager = pagination($total, $pindex, $psize,$params);
		//查取用户信息
		$staff_list = pdo_fetchall("SELECT * FROM ".tablename('move_staff').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
		
		
		//加载页面
		include $this->template("staff_list",$admin);

	}else if ($operation == "add") {
		
		//判断是否为添加
		if ($_W['ispost']) {
			
			if(empty($_GPC['name'])){

					message('姓名不能为空');
			}
			
			if(empty($_GPC['tid'])){

					message('班组ID不能为空');
			}
			if(empty($_GPC['phone'])){

					message('电话不能为空');
			}
			if(empty($_GPC['team'])){

					message('所属班组名称不能为空');
			}
			
			
			//接收数据
			$add['name'] = $_GPC['name']; 
			$add['tid'] = $_GPC['tid']; 
			$add['pass'] = md5("root"); 
			
			$add['phone'] = $_GPC['phone']; 
			$add['team'] = $_GPC['team']; 
			$add['time'] = time(); 
			
			//添加数据
			$result = pdo_insert("move_staff",$add);
			//判断
			if (!empty($result)) {
				
				message("添加员工信息成功",$url);
			}else{

				message("添加失败");

			}
			
		}else{
			
			//加载添加页面
			include $this->template("staff_add");
		}
		

	}else if ($operation == "details") {
		
		//查取信息
		$staff_details = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$_GPC['id']));

		$pindex2 = max(1, intval($_GPC['page']));
		$psize2 = 10;
		$total2 = pdo_fetchcolumn('SELECT COUNT(*) FROM '. tablename('move_customer')." WHERE type=2 AND pid=:pid",array("pid"=>$_GPC['id']));
		$pager2 = pagination($total2, $pindex2, $psize2);
		//查取所属于其站点的员工信息
		$station_staff = pdo_fetchall("SELECT * FROM ".tablename("move_customer")." WHERE type=2 AND pid=:pid ORDER BY id ASC LIMIT ". ($pindex2 - 1) * $psize2 . ',' . $psize2,array("pid"=>$_GPC['id']));
		//查找所负责小区信息
		$pindex3 = max(1, intval($_GPC['page']));
		$psize3 = 10;
		$total3 = pdo_fetchcolumn('SELECT COUNT(*) FROM '. tablename('move_team')." WHERE pid=:pid",array("pid"=>$staff_details['tid']));
		$pager3 = pagination($total3, $pindex3, $psize3);
		//查取所属于其站点的员工信息
		$plot = pdo_fetchall("SELECT * FROM ".tablename("move_team")." WHERE pid=:pid ORDER BY id ASC LIMIT ". ($pindex3 - 1) * $psize3 . ',' . $psize3,array("pid"=>$staff_details['tid']));
		// var_dump($station_staff);
		//加载页面
		include $this->template("staff_details",$station_staff,$pager2,$admin,$plot,$pager3);

	}else if ($operation == "edit") {
		
		//查取信息
		$staff_edit = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$_GPC['id']));
		
		
		//加载页面
		include $this->template("staff_edit");

	}else if ($operation == "delete") {
		
		//查取信息
		$result1 = pdo_delete("move_staff",array("id"=>$_GPC['id']));
		// var_dump($staff_edit);
		//加载页面
		if (!empty($result1)) {
			
			//修改此员工负责的订单信息
			$rer = pdo_update("move_declare",array("appraise"=>3),array("pid"=>$_GPC['id']));
			message("删除成功",$url);

		}else{

			message("删除失败");
		}
	}else if ($operation == "update") {
		
		if ($_W['ispost']) {
			
			$update['name'] = $_GPC['name'];
			$update['tid'] = $_GPC['tid'];
			$update['phone'] = $_GPC['phone'];
			$update['team'] = $_GPC['team'];
			$update['time'] = time();
				//执行修改
				$res = pdo_update("move_staff",$update,array("id"=>$_GPC['id']));
				if (!empty($res)) {
					
					message("修改成功",$url);
				}else{
					message("修改失败");
				}
		}
	}else if ($operation == "control") {

		
		//查找数据
 		$control = pdo_fetch("SELECT * FROM ".tablename("move_control")." ORDER BY ID DESC");
		
		// var_dump($control);die();
		//加载页面
		include $this->template("control",$admin);


	}else if ($operation == "file") {
		
		//加载页面
		include $this->template("staff_file");

	}else if ($operation == "down") {
		
		//加载页面
		include $this->template("staff_down");

	}else if ($operation == "staff_delete") {
		
		//判断是否为post
		if ($_W['ispost']) {
			
			//判断是否为空
			if (!empty($_POST['ipn-id'][0])) {
				
				// $pid = array();
				//接收数据
				$pid = implode(",",$_GPC['ipn-id']);
				// var_dump($pid);die();
				//修改数据
				$sql = "DELETE FROM ims_move_staff WHERE id in (".$pid.")";
				
				$rese = pdo_query($sql);
				//判断是否删除成功
				if (!empty($rese)) {
					
					//循环修改订单信息
					foreach ($_GPC['ipn-id'] as $key => $value) {
						
						$ret = pdo_update("move_declare",array("appraise"=>3),array("pid"=>$value));
					}
					
					message("删除成功",$url,"success");
				}else{

					message("删除失败",$url,"error");
				}
			}else{

				message("对不起请选择要删除的数据",$url,"error");
			}

		}
	}else if ($operation == "time") {
		
		include $this->template("staff_delete");
	}

 ?>