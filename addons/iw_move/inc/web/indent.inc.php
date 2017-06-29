<?php 


	global $_W,$_GPC;
	$action = 'indent';
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
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_declare')." WHERE status=3 ".$where);
		$pager = pagination($total, $pindex, $psize,$params);
		//查取用户信息
		$indent_list = pdo_fetchall("SELECT * FROM ".tablename('move_declare')." WHERE status=3 ".$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
		
		
		//加载页面
		include $this->template("indent_list",$admin);

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
		$total1 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_declare')." WHERE status=1 ".$where1);
		$pager1 = pagination($total1, $pindex1, $psize1,$params1);
		//查取用户信息
		$dispose = pdo_fetchall("SELECT * FROM ".tablename('move_declare')." WHERE status=1 ".$where1." ORDER BY id ASC LIMIT ". ($pindex1 - 1) * $psize1 . ',' . $psize1,$params1);
		
		
		//加载页面
		include $this->template("dispose",$admin);

	}else if ($operation == "not") {
		
		//设置条件
		$where2 = '';
		$params2 = array();
		//判断是否有搜索
		if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

			$where2.=' AND `name` LIKE :keyword OR `phone` LIKE :keywordl OR `plat` LIKE :keywords ';
			
			$params2[':keyword'] = "%{$_GPC['keyword']}%";
			$params2[':keywordl'] = "%{$_GPC['keyword']}%";
			$params2[':keywords'] = "%{$_GPC['keyword']}%";
			
		}
		
		// //查取数据
		$pindex2 = max(1, intval($_GPC['page']));
		$psize2 = 20;
		$tota2 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_declare')." WHERE status=0 ".$where2);
		$pager2 = pagination($tota2, $pindex2, $psize2,$params2);
		//查取用户信息
		$not = pdo_fetchall("SELECT * FROM ".tablename('move_declare')." WHERE status=0 ".$where2." ORDER BY id ASC LIMIT ". ($pindex2 - 1) * $psize2 . ',' . $psize2,$params2);
		
		
		//加载页面
		include $this->template("not",$admin);

	}else if ($operation == "already") {
		
		//设置条件
		$where3 = '';
		$params3 = array();
		//判断是否有搜索
		if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

			$where3.=' AND `name` LIKE :keyword OR `phone` LIKE :keywordl OR `plat` LIKE :keywords ';
			
			$params3[':keyword'] = "%{$_GPC['keyword']}%";
			$params3[':keywordl'] = "%{$_GPC['keyword']}%";
			$params3[':keywords'] = "%{$_GPC['keyword']}%";
			
		}
		
		// //查取数据
		$pindex3 = max(1, intval($_GPC['page']));
		$psize3 = 20;
		$tota3 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_declare')." WHERE status=4 ".$where3);
		$pager3 = pagination($tota3, $pindex3, $psize3,$params3);
		//查取用户信息
		$already = pdo_fetchall("SELECT * FROM ".tablename('move_declare')." WHERE status=4 ".$where3." ORDER BY id ASC LIMIT ". ($pindex3 - 1) * $psize3 . ',' . $psize3,$params3);
		
		
		//加载页面
		include $this->template("already",$admin);

	}else if ($operation == "fail") {
		
		//设置条件
		$where4 = '';
		$params4 = array();
		//判断是否有搜索
		if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

			$where4.=' AND `name` LIKE :keyword OR `phone` LIKE :keywordl OR `plat` LIKE :keywords ';
			
			$params4[':keyword'] = "%{$_GPC['keyword']}%";
			$params4[':keywordl'] = "%{$_GPC['keyword']}%";
			$params4[':keywords'] = "%{$_GPC['keyword']}%";
			
		}
		
		// //查取数据
		$pindex4 = max(1, intval($_GPC['page']));
		$psize4 = 20;
		$tota4 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_declare')." WHERE status=2 ".$where4);
		$pager4 = pagination($tota4, $pindex4, $psize4,$params4);
		//查取用户信息
		$fail = pdo_fetchall("SELECT * FROM ".tablename('move_declare')." WHERE status=2 ".$where4." ORDER BY id ASC LIMIT ". ($pindex4 - 1) * $psize4 . ',' . $psize4,$params4);
		
		
		//加载页面
		include $this->template("fail",$admin);

	}else if ($operation == "any") {
		
		//设置条件
		$where5 = '';
		$params5 = array();
		//判断是否有搜索
		if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

			$where5.=' AND `name` LIKE :keyword OR `phone` LIKE :keywordl OR `plat` LIKE :keywords ';
			
			$params5[':keyword'] = "%{$_GPC['keyword']}%";
			$params5[':keywordl'] = "%{$_GPC['keyword']}%";
			$params5[':keywords'] = "%{$_GPC['keyword']}%";
			
		}
		
		// //查取数据
		$pindex5 = max(1, intval($_GPC['page']));
		$psize5 = 20;
		$tota5 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_declare')." WHERE come=2 ".$where5);
		$pager5 = pagination($tota5, $pindex5, $psize5,$params5);
		//查取用户信息
		$any = pdo_fetchall("SELECT * FROM ".tablename('move_declare')." WHERE come=2 ".$where5." ORDER BY id ASC LIMIT ". ($pindex5 - 1) * $psize5 . ',' . $psize5,$params5);
		
		
		//加载页面
		include $this->template("any",$admin);
	}else if ($operation == "refuse") {
		
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

	}else if ($operation == "indent_delete") {
		
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
	}



 ?>