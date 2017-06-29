<?php 


	global $_W,$_GPC;
	$action = 'user';
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

			$where.=' WHERE `name` LIKE :keyword OR `phone` LIKE :keywordl OR `plot` LIKE :keywords OR `account` LIKE :keyworda';
			
			$params[':keyword'] = "%{$_GPC['keyword']}%";
			$params[':keywordl'] = "%{$_GPC['keyword']}%";
			$params[':keywords'] = "%{$_GPC['keyword']}%";
			$params[':keyworda'] = "%{$_GPC['keyword']}%";
			
		}
		
		// //查取数据
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_customer').$where);
		$pager = pagination($total, $pindex, $psize,$params);
		//查取用户信息
		$user_list = pdo_fetchall("SELECT * FROM ".tablename('move_customer').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
		
		//加载页面
		include $this->template("user_list",$admin);

	}else if ($operation == "add") {
		
		//判断是否为添加
		if ($_W['ispost']) {
			
		
			if(empty($_GPC['name'])){

					message('姓名不能为空');
			}
			
			if(empty($_GPC['plot'])){

					message('所属小区不能为空');
			}
			if(empty($_GPC['phone'])){

					message('电话不能为空');
			}
			if(empty($_GPC['account'])){

					message('宽带账号不能为空');
			}
			
			
			//接收数据
			$add['name'] = $_GPC['name']; 
			$add['address'] = $_GPC['address']; 
			$add['plot'] = $_GPC['plot']; 
			
			$add['phone'] = $_GPC['phone']; 
			$add['account'] = $_GPC['account']; 
			$add['time'] = time(); 

			//添加数据
			$result = pdo_insert("move_customer",$add);
			//判断
			if (!empty($result)) {
				
				message("添加客户信息成功",$url);
			}else{

				message("添加失败");

			}
			
		}else{
			
			//加载添加页面
			include $this->template("user_add");
		}
		

	}else if ($operation == "details") {
		
		//查取信息
		$user_details = pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE id=:id",array("id"=>$_GPC['id']));

		$pindex2 = max(1, intval($_GPC['page']));
		$psize2 = 10;
		$total2 = pdo_fetchcolumn('SELECT COUNT(*) FROM '. tablename('move_customer')." WHERE type=1 AND pid=:pid",array("pid"=>$_GPC['id']));
		$pager2 = pagination($total2, $pindex2, $psize2);
		//查取所属于其站点的员工信息
		$station_staff = pdo_fetchall("SELECT * FROM ".tablename("move_customer")." WHERE type=1 AND pid=:pid ORDER BY id ASC LIMIT ". ($pindex2 - 1) * $psize2 . ',' . $psize2,array("pid"=>$_GPC['id']));
		// var_dump($station_staff);
		//加载页面
		include $this->template("user_details",$station_staff,$pager2,$admin);

	}else if ($operation == "edit") {
		
		//查取信息
		$user_edit = pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE id=:id",array("id"=>$_GPC['id']));
		
		
		//加载页面
		include $this->template("user_edit");

	}else if ($operation == "delete") {
		
		//查取信息
		$result1 = pdo_delete("move_customer",array("id"=>$_GPC['id']));
		// var_dump($user_edit);
		//加载页面
		if (!empty($result1)) {
			
			message("删除成功",$url);
		}else{

			message("删除失败");
		}
	}else if ($operation == "update") {
		
		if ($_W['ispost']) {
			
			$update['name'] = $_GPC['name'];
			$update['plot'] = $_GPC['plot'];
			$update['address'] = $_GPC['address'];
			$update['phone'] = $_GPC['phone'];
			$update['account'] = $_GPC['account'];
			$update['time'] = time();
				//执行修改
				$res = pdo_update("move_customer",$update,array("id"=>$_GPC['id']));
				if (!empty($res)) {
					
					message("修改成功",$url);
				}else{
					message("修改失败");
				}
		}
	}else if ($operation == "file") {
		
		include $this->template("user_file");
		
	}else if ($operation == "new") {
		
		include $this->template("user_new");
		
	}else if ($operation == "down") {
		
		include $this->template("user_down");
		
	}else if ($operation == "user_delete") {
		
		//判断是否为post
		if ($_W['ispost']) {
			
			//判断是否为空
			if (!empty($_POST['ipn-id'][0])) {
				
				// $pid = array();
				//接收数据
				$pid = implode(",",$_GPC['ipn-id']);
				// var_dump($pid);die();
				//修改数据
				$sql = "DELETE FROM ims_move_customer WHERE id in (".$pid.")";
				
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
	}elseif ($operation == "time") {
		
		include $this->template("user_delete");
	}

 ?>