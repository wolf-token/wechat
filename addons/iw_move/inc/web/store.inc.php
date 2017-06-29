<?php 


	global $_W,$_GPC;
	$action = 'store';
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

			$where.=' WHERE `name` LIKE :keyword OR `position` LIKE :keywordl';
			
			$params[':keyword'] = "%{$_GPC['keyword']}%";
			$params[':keywordl'] = "%{$_GPC['keyword']}%";
			
		}
		
		// //查取数据
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_nearby').$where);
		$pager = pagination($total, $pindex, $psize,$params);
		//查取用户信息
		$store_list = pdo_fetchall("SELECT * FROM ".tablename('move_nearby').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
		foreach ($store_list as $key => $value) {
			
			$store_list[$key]['photo'] = json_decode($value['photo']);
		}
		
	

		//加载页面
		include $this->template("store_list",$admin);

	}else if ($operation == "add") {
		
		//判断是否为添加
		if ($_W['ispost']) {
			
			if(empty($_GPC['name'])){

					message('门店名称不能为空');
			}
			
			if(empty($_GPC['position'])){

					message('地址不能为空');
			}
			if(empty($_GPC['multi-image'])){

					message('门店展示图不能为空');
			}
			if(empty($_GPC['lng'])){

					message('经度不能为空');
			}
			if(empty($_GPC['lat'])){

					message('纬度不能为空');
			}
			
			//接收数据
			$add['name'] = $_GPC['name']; 
			$add['position'] = $_GPC['position']; 
			$add['photo'] = json_encode($_GPC['multi-image']); 
			$add['lng'] = $_GPC['lng']; 
			$add['lat'] = $_GPC['lat']; 
			$add['time'] = date("Y-m-d H:i:s",time()); 

			//添加数据
			$result = pdo_insert("move_nearby",$add);
			//判断
			if (!empty($result)) {
				
				message("添加门店成功",$url);
			}else{

				message("添加失败");

			}
			
		}else{
			
			//加载添加页面
			include $this->template("store_add");
		}
		

	}else if ($operation == "details") {
		
		//查取信息
		$store_details = pdo_fetch("SELECT * FROM ".tablename("move_nearby")." WHERE id=:id",array("id"=>$_GPC['id']));

		$store_details['photo'] = json_decode($store_details['photo']);
		//加载页面
		include $this->template("store_details",$admin);

	}else if ($operation == "edit") {
		
		//查取信息
		$store_edit = pdo_fetch("SELECT * FROM ".tablename("move_nearby")." WHERE id=:id",array("id"=>$_GPC['id']));
		
		$store_edit['photo'] = json_decode($store_edit['photo']);
		//加载页面
		include $this->template("store_edit");

	}else if ($operation == "delete") {
		
		//查取信息
		$result1 = pdo_delete("move_nearby",array("id"=>$_GPC['id']));
		// var_dump($store_edit);
		//加载页面
		if (!empty($result1)) {
			
			message("删除成功",$url);
		}else{

			message("删除失败");
		}
	}else if ($operation == "update") {
		
		if ($_W['ispost']) {
			
			$update['name'] = $_GPC['name'];
			$update['position'] = $_GPC['position'];
			$update['photo'] = json_encode($_GPC['multi-image']);
			$update['lng'] = $_GPC['lng'];
			$update['lat'] = $_GPC['lat'];
			$update['time'] = date("Y-m-d H:i:s",time());
				//执行修改
				$res = pdo_update("move_nearby",$update,array("id"=>$_GPC['id']));
				if (!empty($res)) {
					
					message("修改成功",$url);
				}else{
					message("修改失败");
				}
		}
	}else if ($operation == "store_delete") {
		
		//判断是否为post
		if ($_W['ispost']) {
			
			//判断是否为空
			if (!empty($_POST['ipn-id'][0])) {
				
				// $pid = array();
				//接收数据
				$pid = implode(",",$_GPC['ipn-id']);
				// var_dump($pid);die();
				//修改数据
				$sql = "DELETE FROM ims_move_nearby WHERE id in (".$pid.")";
				
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
	}


 ?>