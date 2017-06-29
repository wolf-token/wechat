<?php 


	global $_W,$_GPC;
	$action = 'plot';
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

			$where.=' WHERE `name` LIKE :keyword OR `pid` LIKE :keywordl OR `nickname` LIKE :keywords ';
			
			$params[':keyword'] = "%{$_GPC['keyword']}%";
			$params[':keywordl'] = "%{$_GPC['keyword']}%";
			$params[':keywords'] = "%{$_GPC['keyword']}%";
			// $params[':keyworda'] = "%{$_GPC['keyword']}%";
			
		}
		
		// //查取数据
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_team').$where);
		$pager = pagination($total, $pindex, $psize,$params);
		//查取用户信息
		$plot_list = pdo_fetchall("SELECT * FROM ".tablename('move_team').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
		
		
		//加载页面
		include $this->template("plot_list",$admin);

	}else if ($operation == "add") {
		
		//判断是否为添加
		if ($_W['ispost']) {
			
			if(empty($_GPC['name'])){

					message('小区名称不能为空');
			}
			if(empty($_GPC['address'])){

					message('地址不能为空');
			}
			if(empty($_GPC['pid'])){

					message('班组id不能为空');
			}
			if(empty($_GPC['nickname'])){

					message('班组名称不能为空');
			}
			if(empty($_GPC['aid'])){

					message('地市Id不能为空');
			}
			if(empty($_GPC['xid'])){

					message('区县Id不能为空');
			}
			if(empty($_GPC['jid'])){

					message('街道Id不能为空');
			}
			if(empty($_GPC['lid'])){

					message('路村Id不能为空');
			}
			if(empty($_GPC['qid'])){

					message('小区/组Id不能为空');
			}
			//接收数据
			$add['name'] = $_GPC['name']; 
			$add['pid'] = $_GPC['pid']; 
			$add['nickname'] = $_GPC['nickname']; 
			$add['aid'] = $_GPC['aid']; 
			$add['pid'] = $_GPC['pid']; 
			$add['address'] = $_GPC['address']; 
			$add['qid'] = $_GPC['qid']; 
			$add['xid'] = $_GPC['xid']; 
			$add['jid'] = $_GPC['jid']; 
			$add['lid'] = $_GPC['lid']; 
			// $add['lid'] = $_GPC['lid']; 
			$add['time'] = time(); 
			
			//添加数据
			$result = pdo_insert("move_team",$add);
			//判断
			if (!empty($result)) {
				
				message("添加员工信息成功",$url);
			}else{

				message("添加失败");

			}
			
		}else{
			
			//加载添加页面
			include $this->template("plot_add");
		}
		

	}else if ($operation == "details") {
		
		//查取信息
		$plot_details = pdo_fetch("SELECT * FROM ".tablename("move_team")." WHERE id=:id",array("id"=>$_GPC['id']));
		$params2[':address'] = "%{$plot_details['name']}%";
		$pindex2 = max(1, intval($_GPC['page']));
		$psize2 = 10;
		$total2 = pdo_fetchcolumn('SELECT COUNT(*) FROM '. tablename('move_customer')." WHERE `address` LIKE :address");
		$pager2 = pagination($total2, $pindex2, $psize2,$params2);
		//查取所属于其站点的员工信息
		$station_plot = pdo_fetchall("SELECT * FROM ".tablename("move_customer")." WHERE `address` LIKE :address ORDER BY id ASC LIMIT ". ($pindex2 - 1) * $psize2 . ',' . $psize2,$params2);
		// var_dump($station_plot);
		//查找所负责小区信息
		$pindex3 = max(1, intval($_GPC['page']));
		$psize3 = 10;
		$total3 = pdo_fetchcolumn('SELECT COUNT(*) FROM '. tablename('move_staff')." WHERE tid=:tid",array("tid"=>$plot_details['pid']));
		$pager3 = pagination($total3, $pindex3, $psize3);
		//查取所属于其站点的员工信息
		$staff = pdo_fetchall("SELECT * FROM ".tablename("move_staff")." WHERE tid=:tid ORDER BY id ASC LIMIT ". ($pindex3 - 1) * $psize3 . ',' . $psize3,array("tid"=>$plot_details['pid']));
		// var_dump($staff);
		//加载页面
		include $this->template("plot_details",$station_plot,$pager2,$admin,$staff,$pager3);

	}else if ($operation == "edit") {
		
		//查取信息
		$plot_edit = pdo_fetch("SELECT * FROM ".tablename("move_team")." WHERE id=:id",array("id"=>$_GPC['id']));
		
		
		//加载页面
		include $this->template("plot_edit");

	}else if ($operation == "delete") {
		
		//查取信息
		$result1 = pdo_delete("move_team",array("id"=>$_GPC['id']));
		// var_dump($plot_edit);
		//加载页面
		if (!empty($result1)) {
			
			message("删除成功",$url);
		}else{

			message("删除失败");
		}
	}else if ($operation == "update") {
		
		if ($_W['ispost']) {
			
			$update['name'] = $_GPC['name']; 
			$update['pid'] = $_GPC['pid']; 
			$update['nickname'] = $_GPC['nickname']; 
			$update['aid'] = $_GPC['aid']; 
			$update['pid'] = $_GPC['pid']; 
			$update['address'] = $_GPC['address']; 
			$update['qid'] = $_GPC['qid']; 
			$update['xid'] = $_GPC['xid']; 
			$update['jid'] = $_GPC['jid']; 
			$update['lid'] = $_GPC['lid']; 
			$update['time'] = time();
				//执行修改
				$res = pdo_update("move_team",$update,array("id"=>$_GPC['id']));
				if (!empty($res)) {
					
					message("修改成功",$url);
				}else{
					message("修改失败");
				}
		}
	
	}else if ($operation == "file") {
		
		//加载页面
		include $this->template("plot_file");

	}else if ($operation == "down") {
		
		//加载页面
		include $this->template("plot_down");

	}else if ($operation == "plot_delete") {
		
		//判断是否为post
		if ($_W['ispost']) {
			
			//判断是否为空
			if (!empty($_POST['ipn-id'][0])) {
				
				// $pid = array();
				//接收数据
				$pid = implode(",",$_GPC['ipn-id']);
				// var_dump($pid);die();
				//修改数据
				$sql = "DELETE FROM ims_move_team WHERE id in (".$pid.")";
				
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
		
		include $this->template("plot_delete");
	}else if ($operation == "remove") {
		
		//删除用户数据
		if ($_W['ispost']) {
			
			  $start4 = strtotime($_GPC['datelimit']['start']) ;
			  // $status = $_GPC['status'];
              $end4 = strtotime($_GPC['datelimit']['end']) ;
              $sql4 = "DELETE FROM ims_move_team WHERE time>=".$start4." AND time<=".$end4;
              //删除数据
              $result4 = pdo_query($sql4);

              //判断
              if (!empty($result4)) {
              	
              		message("删除信息成功",$this->createWebUrl("plot",array("op"=>'list')),"success");
              }else{

              		message("删除信息失败");
              }
		}
	}

 ?>