<?php 


	global $_W,$_GPC;
	$action = 'fixed';
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

			$where.=' WHERE `name` LIKE :keyword OR `theme` LIKE :keywordl OR `time` LIKE :keywords OR `introduce` LIKE :keyworda';
			
			$params[':keyword'] = "%{$_GPC['keyword']}%";
			$params[':keywordl'] = "%{$_GPC['keyword']}%";
			$params[':keywords'] = "%{$_GPC['keyword']}%";
			$params[':keyworda'] = "%{$_GPC['keyword']}%";
			
		}
		
		// //查取数据
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_fixed').$where);
		$pager = pagination($total, $pindex, $psize,$params);
		//查取用户信息
		$fixed_list = pdo_fetchall("SELECT * FROM ".tablename('move_fixed').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
		
		
		//加载页面
		include $this->template("fixed_list",$admin);

	}else if ($operation == "add") {
		
		//判断是否为添加
		if ($_W['ispost']) {
			
			if(empty($_GPC['name'])){

					message('固化名称不能为空');
			}
			
			if(empty($_GPC['theme'])){

					message('固话主题不能为空');
			}
			if(empty($_GPC['introduce'])){

					message('详情介绍不能为空');
			}
			if(empty($_GPC['content'])){

					message('详细信息不能为空');
			}
			if(empty($_GPC['photo'])){

					message('展示图不能为空');
			}
			
			//接收数据
			$add['name'] = $_GPC['name']; 
			$add['theme'] = $_GPC['theme']; 
			$add['photo'] = $_GPC['photo']; 
			$add['introduce'] = $_GPC['introduce']; 
			$add['content'] = $_GPC['content']; 
			$add['status'] = $_GPC['status']; 
			$add['time'] = date("Y-m-d H:i:s",time()); 
			
			//添加数据
			$result = pdo_insert("move_fixed",$add);
			//判断
			if (!empty($result)) {
				
				message("添加固话信息成功",$url);
			}else{

				message("添加失败");

			}
			
		}else{
			
			//加载添加页面
			include $this->template("fixed_add");
		}
		

	}else if ($operation == "details") {
		
		//查取信息
		$fixed_details = pdo_fetch("SELECT * FROM ".tablename("move_fixed")." WHERE id=:id",array("id"=>$_GPC['id']));

		$fixed_details['content'] = htmlspecialchars_decode($fixed_details['content']);
		//加载页面
		include $this->template("fixed_details",$admin);

	}else if ($operation == "edit") {
		
		//查取信息
		$fixed_edit = pdo_fetch("SELECT * FROM ".tablename("move_fixed")." WHERE id=:id",array("id"=>$_GPC['id']));
		
		
		//加载页面
		include $this->template("fixed_edit");

	}else if ($operation == "delete") {
		
		//查取信息
		$result1 = pdo_delete("move_fixed",array("id"=>$_GPC['id']));
		// var_dump($fixed_edit);
		//加载页面
		if (!empty($result1)) {
			
			message("删除成功",$url);
		}else{

			message("删除失败");
		}
	}else if ($operation == "update") {
		
		if ($_W['ispost']) {
			
			$update['name'] = $_GPC['name']; 
			$update['theme'] = $_GPC['theme']; 
			$update['photo'] = $_GPC['photo']; 
			$update['introduce'] = $_GPC['introduce']; 
			$update['content'] = $_GPC['content']; 
			$update['status'] = $_GPC['status']; 
			$update['time'] = date("Y-m-d H:i:s",time());
				//执行修改
				$res = pdo_update("move_fixed",$update,array("id"=>$_GPC['id']));
				if (!empty($res)) {
					
					message("修改成功",$url);
				}else{
					message("修改失败");
				}
		}
	}else if ($operation == "fixed_delete") {
		
		//判断是否为post
		if ($_W['ispost']) {
			
			//判断是否为空
			if (!empty($_POST['ipn-id'][0])) {
				
				// $pid = array();
				//接收数据
				$pid = implode(",",$_GPC['ipn-id']);
				// var_dump($pid);die();
				//修改数据
				$sql = "DELETE FROM ims_move_fixed WHERE id in (".$pid.")";
				
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