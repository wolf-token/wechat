<?php 


	global $_W,$_GPC;
	$action = 'broadband';
	$uniacid = $_W['uniacid'];
	$uid = intval($_W['uid']);
	$admin = pdo_fetch('SELECT * FROM ' . tablename('users_permission') . ' WHERE uniacid = :aid AND uid = :uid AND type = :type', array(':aid' => $uniacid, ':uid' => $uid, ':type' => "system"));
	$url = $this->createWebUrl($action, array('op' => 'list'));
	$url1 = $this->createWebUrl($action, array('op' => 'photo_list'));
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';

	if ($operation == "list") {
		
		//设置条件
		$where = '';
		$params = array();
		//判断是否有搜索
		if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

			// $where.=' WHERE `name` LIKE :keyword OR `price` LIKE :keywordl OR `favorable` LIKE :keywords OR `new` LIKE :keyworda';
			$where.=' WHERE `name` LIKE :keyword OR `price` LIKE :keywordl ';
			$params[':keyword'] = "%{$_GPC['keyword']}%";
			$params[':keywordl'] = "%{$_GPC['keyword']}%";
			// $params[':keywords'] = "%{$_GPC['keyword']}%";
			// $params[':keyworda'] = "%{$_GPC['keyword']}%";
			
		}
		
		// //查取数据
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_broadband').$where);
		$pager = pagination($total, $pindex, $psize,$params);
		//查取用户信息
		$broadband_list = pdo_fetchall("SELECT * FROM ".tablename('move_broadband').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
		
		
		//加载页面
		include $this->template("broadband_list",$admin);

	}else if ($operation == "add") {
		
		//判断是否为添加
		if ($_W['ispost']) {
			
			if(empty($_GPC['name'])){

					message('宽带兆数不能为空');
			}
			
			if(empty($_GPC['price'])){

					message('宽带价格不能为空');
			}
			// if(empty($_GPC['favorable'])){

			// 		message('优惠话不能为空');
			// }
			// if(empty($_GPC['new'])){

			// 		message('续费优惠不能为空');
			// }
			// if(empty($_GPC['content'])){

			// 		message('详情介绍不能为空');
			// }
			
			//接收数据
			$add['name'] = $_GPC['name']; 
			$add['price'] = $_GPC['price']; 
			// $add['favorable'] = $_GPC['favorable']; 
			// $add['new'] = $_GPC['new']; 
			// $add['content'] = $_GPC['content']; 
			$add['status'] = $_GPC['status']; 
			$add['time'] = date("Y-m-d H:i:s",time()); 
			
			//添加数据
			$result = pdo_insert("move_broadband",$add);
			//判断
			if (!empty($result)) {
				
				message("添加宽带信息成功",$url);
			}else{

				message("添加失败");

			}
			
		}else{
			
			//加载添加页面
			include $this->template("broadband_add");
		}
		

	}else if ($operation == "details") {
		
		//查取信息
		$broadband_details = pdo_fetch("SELECT * FROM ".tablename("move_broadband")." WHERE id=:id",array("id"=>$_GPC['id']));
		$broadband_details['content'] = htmlspecialchars_decode($broadband_details['content']);
		//加载页面
		include $this->template("broadband_details",$admin);

	}else if ($operation == "edit") {
		
		//查取信息
		$broadband_edit = pdo_fetch("SELECT * FROM ".tablename("move_broadband")." WHERE id=:id",array("id"=>$_GPC['id']));
		
		
		//加载页面
		include $this->template("broadband_edit");

	}else if ($operation == "delete") {
		
		//查取信息
		$result1 = pdo_delete("move_broadband",array("id"=>$_GPC['id']));
		// var_dump($broadband_edit);
		//加载页面
		if (!empty($result1)) {
			
			message("删除成功",$url);
		}else{

			message("删除失败");
		}
	}else if ($operation == "update") {
		
		if ($_W['ispost']) {
			
			$update['name'] = $_GPC['name']; 
			$update['price'] = $_GPC['price']; 
			// $update['favorable'] = $_GPC['favorable']; 
			// $update['new'] = $_GPC['new']; 
			// $update['content'] = $_GPC['content']; 
			$update['status'] = $_GPC['status']; 
			$update['time'] = date("Y-m-d H:i:s",time());
				//执行修改
				$res = pdo_update("move_broadband",$update,array("id"=>$_GPC['id']));
				if (!empty($res)) {
					
					message("修改成功",$url);
				}else{
					message("修改失败");
				}
		}
	}else if ($operation == "photo_list") {
		
		//设置条件
		$where1 = '';
		$params1 = array();
		//判断是否有搜索
		if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

			// $where.=' WHERE `name` LIKE :keyword OR `price` LIKE :keywordl OR `favorable` LIKE :keywords OR `new` LIKE :keyworda';
			$where1.=' WHERE `time` LIKE :keyword ';
			$params1[':keyword'] = "%{$_GPC['keyword']}%";
			// $params[':keywordl'] = "%{$_GPC['keyword']}%";
			// $params[':keywords'] = "%{$_GPC['keyword']}%";
			// $params[':keyworda'] = "%{$_GPC['keyword']}%";
			
		}
		
		// //查取数据
		$pindex1 = max(1, intval($_GPC['page']));
		$psize1 = 20;
		$total1 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_photo').$where1);
		$pager1 = pagination($total1, $pindex1, $psize1,$params1);
		//查取用户信息
		$photo_list = pdo_fetchall("SELECT * FROM ".tablename('move_photo').$where1." ORDER BY id ASC LIMIT ". ($pindex1 - 1) * $psize1 . ',' . $psize1,$params1);
		
		
		//加载页面
		include $this->template("photo_list",$admin);

	}else if ($operation == "photo_add") {
		
		//判断是否为添加
		if ($_W['ispost']) {
			
			if(empty($_GPC['name'])){

					message('主题不能为空');
			}
			
			// if(empty($_GPC['price'])){

			// 		message('宽带价格不能为空');
			// }
			// if(empty($_GPC['favorable'])){

			// 		message('优惠话不能为空');
			// }
			// if(empty($_GPC['new'])){

			// 		message('续费优惠不能为空');
			// }
			if(empty($_GPC['content'])){

					message('宽带宣传图不能为空');
			}
			
			//接收数据
			$add2['name'] = $_GPC['name']; 
			// $add['price'] = $_GPC['price']; 
			// $add['favorable'] = $_GPC['favorable']; 
			// $add['new'] = $_GPC['new']; 
			$add2['content'] = $_GPC['content']; 
			$add2['status'] = $_GPC['status']; 
			$add2['time'] = date("Y-m-d H:i:s",time()); 
			
			//添加数据
			$result2 = pdo_insert("move_photo",$add2);
			//判断
			if (!empty($result2)) {
				
				message("添加宽带信息成功",$url1);
			}else{

				message("添加失败");

			}
			
		}else{
			
			//加载添加页面
			include $this->template("photo_add");
		}
		

	}else if ($operation == "photo_details") {
		
		//查取信息
		$photo_details = pdo_fetch("SELECT * FROM ".tablename("move_photo")." WHERE id=:id",array("id"=>$_GPC['id']));
		$photo_details['content'] = htmlspecialchars_decode($photo_details['content']);
		//加载页面
		include $this->template("photo_details",$admin);

	}else if ($operation == "photo_edit") {
		
		//查取信息
		$photo_edit = pdo_fetch("SELECT * FROM ".tablename("move_photo")." WHERE id=:id",array("id"=>$_GPC['id']));
		
		
		//加载页面
		include $this->template("photo_edit");

	}else if ($operation == "delete") {
		
		//查取信息
		$result3 = pdo_delete("move_photo",array("id"=>$_GPC['id']));
		// var_dump($broadband_edit);
		//加载页面
		if (!empty($result3)) {
			
			message("删除成功",$url1);
		}else{

			message("删除失败");
		}
	}else if ($operation == "photo_update") {
		
		if ($_W['ispost']) {
			
			$update1['name'] = $_GPC['name']; 
			// $update['price'] = $_GPC['price']; 
			// $update['favorable'] = $_GPC['favorable']; 
			// $update['new'] = $_GPC['new']; 
			$update1['content'] = $_GPC['content']; 
			$update1['status'] = $_GPC['status']; 
			$update1['time'] = date("Y-m-d H:i:s",time());
				//执行修改
				$res = pdo_update("move_photo",$update1,array("id"=>$_GPC['id']));
				if (!empty($res1)) {
					
					message("修改成功",$url1);
				}else{
					message("修改失败");
				}
		}

	}else if ($operation == "boot_delete") {
		
		//判断是否为post
		if ($_W['ispost']) {
			
			//判断是否为空
			if (!empty($_POST['ipn-id'][0])) {
				
				// $pid = array();
				//接收数据
				$pid = implode(",",$_GPC['ipn-id']);
				// var_dump($pid);die();
				//修改数据
				$sql = "DELETE FROM ims_move_broadband WHERE id in (".$pid.")";
				
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
	}else if ($operation == "foot_delete") {
		
		//判断是否为post
		if ($_W['ispost']) {
			
			//判断是否为空
			if (!empty($_POST['ipn-id'][0])) {
				
				// $pid = array();
				//接收数据
				$pid2 = implode(",",$_GPC['ipn-id']);
				// var_dump($pid);die();
				//修改数据
				$sql2 = "DELETE FROM ims_move_photo WHERE id in (".$pid2.")";
				
				$rese2 = pdo_query($sql2);
				//判断是否删除成功
				if (!empty($rese2)) {
					
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