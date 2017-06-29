<?php 

	
	global $_W,$_GPC;
	$action = 'interact';
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

			$where.=' WHERE `name` LIKE :keyword OR `content` LIKE :keywords OR `time` LIKE :keywordl ';
			$params[':keywords'] = "%{$_GPC['keyword']}%";
			$params[':keyword'] = "%{$_GPC['keyword']}%";
			$params[':keywordl'] = "%{$_GPC['keyword']}%";
			
			
		}
		
		// //查取数据
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_interact').$where);
		$pager = pagination($total, $pindex, $psize,$params);
		//查取用户信息
		$work_list = pdo_fetchall("SELECT * FROM ".tablename('move_interact').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
		
		//加载页面
		include $this->template("work_list",$admin);

	}else if ($operation == "add") {
		
		//判断是否为添加
		if ($_W['ispost']) {
			// var_dump($_GPC);
			// die;
			if(empty($_GPC['name'])){

					message('活动主题不能为空');
			}
			if(empty($_GPC['content'])){

					message('活动内容不能为空');
			}
			if(empty($_GPC['content'])){

					message('活动封面图不能为空');
			}
			//接收数据
			$add['name'] = $_GPC['name']; 
			$add['photo'] = $_GPC['photo']; 
			$add['content'] = $_GPC['content']; 
			$add['status'] = $_GPC['status']; 

			$add['time'] = date("Y-m-d H:i:s",time()); 
			
			//添加数据
			$result = pdo_insert("move_interact",$add);
			//判断
			if (!empty($result)) {
				
				message("添加优惠活动成功",$url);
			}else{

				message("添加失败");

			}
			
		}else{
			
			//加载添加页面
			include $this->template("work_add");
		}
		

	}else if ($operation == "details") {
		
		//查取信息
		$work_details = pdo_fetch("SELECT * FROM ".tablename("move_interact")." WHERE id=:id",array("id"=>$_GPC['id']));
		$work_details['content'] = htmlspecialchars_decode($work_details['content']);
		
		
		//加载页面
		include $this->template("work_details",$admin);

	}else if ($operation == "edit") {
		
		//查取信息
		$work_edit = pdo_fetch("SELECT * FROM ".tablename("move_interact")." WHERE id=:id",array("id"=>$_GPC['id']));
		// $activity = pdo_fetchall("SELECT * FROM ".tablename("telecom_business"));
		//加载页面
		include $this->template("work_edit");

	}else if ($operation == "delete") {
		
		// echo "nice";
		// die;
		//查取信息
		$result1 = pdo_delete("move_interact",array("id"=>$_GPC['id']));
		// var_dump($office_edit);
		//加载页面
		if (!empty($result1)) {
			
			message("删除成功",$url);
		}else{

			message("删除失败");
		}
	}else if ($operation == "update") {
		
		if ($_W['ispost']) {
			// var_dump($_POST);
			// die;
			$update['photo'] = $_GPC['photo']; 
			$update['name'] = $_GPC['name']; 
			$update['content'] = $_GPC['content']; 
			$update['status'] = $_GPC['status']; 
			
				$update['time'] = date("Y-m-d H:i:s",time());
				//执行修改
				// var_dump($update);
				$res = pdo_update("move_interact",$update,array("id"=>$_GPC['id']));
				// var_dump($res);
				// die;
				if (!empty($res)) {
					
					message("修改成功",$url);
				}else{
					message("修改失败");
				}
		}

	}else if ($operation == "boss_list") {
	
		//设置条件
		$where = '';
		$params = array();
		//判断是否有搜索
		if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

			$where.=' WHERE `time` LIKE :keyword ';
			$params[':keyword'] = "%{$_GPC['keyword']}%";
			
			
		}
		
		// //查取数据
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_boss').$where);
		$pager = pagination($total, $pindex, $psize,$params);
		//查取用户信息
		$boss_list = pdo_fetchall("SELECT * FROM ".tablename('move_boss').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
		foreach ($boss_list as $key => $value) {
			
			$boss_list[$key]['content'] = htmlspecialchars_decode($value['content']);
			$boss_list[$key]['content'] = substr($boss_list[$key]['content'], 300,200);

		}
		
		//加载页面
		include $this->template("boss_list",$admin);

	}else if ($operation == "boss_add") {
		
		//判断是否为添加
		if ($_W['ispost']) {
			// var_dump($_GPC);
			// die;
			
			if(empty($_GPC['content'])){

					message('招聘内容不能为空');
			}
			
			//接收数据
			$add['name'] = $_GPC['name']; 
			$add['content'] = $_GPC['content']; 
			$add['status'] = $_GPC['status']; 
			$add['time'] = date("Y-m-d H:i:s",time()); 
			
			//添加数据
			$result = pdo_insert("move_boss",$add);
			//判断
			if (!empty($result)) {
				
				message("添加公司招聘信息成功",$url);
			}else{

				message("添加失败");

			}
			
		}else{
			
			//加载添加页面
			include $this->template("boss_add");
		}
		

	}else if ($operation == "boss_details") {
		
		//查取信息
		$boss_details = pdo_fetch("SELECT * FROM ".tablename("move_boss")." WHERE id=:id",array("id"=>$_GPC['id']));
		$boss_details['content'] = htmlspecialchars_decode($boss_details['content']);
		
		
		//加载页面
		include $this->template("boss_details",$admin);

	}else if ($operation == "boss_edit") {
		
		//查取信息
		$boss_edit = pdo_fetch("SELECT * FROM ".tablename("move_boss")." WHERE id=:id",array("id"=>$_GPC['id']));
		// $activity = pdo_fetchall("SELECT * FROM ".tablename("telecom_business"));
		//加载页面
		include $this->template("boss_edit");

	}else if ($operation == "boss_delete") {
		
		// echo "nice";
		// die;
		//查取信息
		$result1 = pdo_delete("move_boss",array("id"=>$_GPC['id']));
		// var_dump($office_edit);
		//加载页面
		if (!empty($result1)) {
			
			message("删除成功",$url);
		}else{

			message("删除失败");
		}
	}else if ($operation == "boss_update") {
		
		if ($_W['ispost']) {
			// var_dump($_POST);
			// die;
			$update['name'] = $_GPC['name']; 
			$update['content'] = $_GPC['content']; 
			$update['status'] = $_GPC['status']; 
			
				$update['time'] = date("Y-m-d H:i:s",time());
				//执行修改
				// var_dump($update);
				$res = pdo_update("move_boss",$update,array("id"=>$_GPC['id']));
				// var_dump($res);
				// die;
				if (!empty($res)) {
					
					message("修改成功",$url);
				}else{
					message("修改失败");
				}
		}
	}else if ($operation == "interact_delete") {
		
		//判断是否为post
		if ($_W['ispost']) {
			
			//判断是否为空
			if (!empty($_POST['ipn-id'][0])) {
				
				// $pid = array();
				//接收数据
				$pid = implode(",",$_GPC['ipn-id']);
				// var_dump($pid);die();
				//修改数据
				$sql = "DELETE FROM ims_move_interact WHERE id in (".$pid.")";
				
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
	}else if ($operation == "bose_delete") {
		
		//判断是否为post
		if ($_W['ispost']) {
			
			//判断是否为空
			if (!empty($_POST['ipn-id'][0])) {
				
				// $pid = array();
				//接收数据
				$pid1 = implode(",",$_GPC['ipn-id']);
				// var_dump($pid);die();
				//修改数据
				$sql1 = "DELETE FROM ims_move_boss WHERE id in (".$pid1.")";
				
				$rese1 = pdo_query($sql1);
				//判断是否删除成功
				if (!empty($rese1)) {
					
					message("删除成功",$url,"success");
				}else{

					message("删除失败",$url,"error");
				}
			}else{

				message("对不起请选择要删除的数据",$url,"error");
			}

		}
	}





 ?>