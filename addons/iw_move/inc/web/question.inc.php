<?php 

	global $_W,$_GPC;
	$action = 'question';
	$uniacid = $_W['uniacid'];
	$uid = intval($_W['uid']);
	$admin = pdo_fetch('SELECT * FROM ' . tablename('users_permission') . ' WHERE uniacid = :aid AND uid = :uid AND type = :type', array(':aid' => $uniacid, ':uid' => $uid, ':type' => "system"));
	$url = $this->createWebUrl($action, array('op' => 'list'));
	$url1 = $this->createWebUrl($action, array('op' => 'type_list'));
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'type_list';

	if ($operation == "list") {
	
		//设置条件
		$where = ' WHERE status=0 ';
		$params = array();
		//判断是否有搜索
		if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

			$where.=' AND `time` LIKE :keyword ';
			$params[':keyword'] = "%{$_GPC['keyword']}%";
			
			
		}
		
		// //查取数据
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_question').$where);
		$pager = pagination($total, $pindex, $psize,$params);
		//查取用户信息
		$question_list = pdo_fetchall("SELECT * FROM ".tablename('move_question').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
		foreach ($question_list as $key => $value) {
			
			$question_list[$key]['content'] = htmlspecialchars_decode($value['content']);
			$question_list[$key]['type'] = pdo_fetch("SELECT * FROM ".tablename("move_type")." WHERE id=:id",array("id"=>$value['uid']));
		}
		// foreach ($question_list as $key1 => $value1) {
			
		// 	$question_list[$key]['content'] = substr($question_list[$key]['content'], 300,500);
		// }
		// var_dump($question_list);
		//加载页面
		include $this->template("question_list",$admin);

	}else if ($operation == "add") {
		
		//判断是否为添加
		if ($_W['ispost']) {
			// var_dump($_GPC);
			// die;
			if(empty($_GPC['name'])){

					message('问题主题不能为空');
			}
			if(empty($_GPC['content'])){

					message('问题解答不能为空');
			}
			
			//接收数据
			$add['content'] = $_GPC['content']; 
			$add['name'] = $_GPC['name']; 
			$add['uid'] = $_GPC['uid']; 
			$add['status'] = $_GPC['status']; 
			$add['time'] = date("Y-m-d H:i:s",time()); 
			
			//添加数据
			$result = pdo_insert("move_question",$add);
			//判断
			if (!empty($result)) {
				
				message("添加问题解答信息成功",$url);
			}else{

				message("添加失败");

			}
			
		}else{
			
			//查找分类信息
			$type = pdo_fetchall("SELECT * FROM ".tablename("move_type"));
			//加载添加页面
			include $this->template("question_add",$type);
		}
		

	}else if ($operation == "details") {
		
		//查取信息
		$question_details = pdo_fetch("SELECT * FROM ".tablename("move_question")." WHERE id=:id",array("id"=>$_GPC['id']));
		$question_details['content'] = htmlspecialchars_decode($question_details['content']);
		$question_details['type'] = pdo_fetch("SELECT * FROM ".tablename("move_type")." WHERE id=:id",array("id"=>$question_details['uid']));
		
		//加载页面
		include $this->template("question_details",$admin);

	}else if ($operation == "edit") {
		
		//查取信息
		$question_edit = pdo_fetch("SELECT * FROM ".tablename("move_question")." WHERE id=:id",array("id"=>$_GPC['id']));
		$activity = pdo_fetchall("SELECT * FROM ".tablename("move_type"));
		//加载页面
		include $this->template("question_edit",$activity);

	}else if ($operation == "delete") {
		
		
		//查取信息
		$result1 = pdo_delete("move_question",array("id"=>$_GPC['id']));
		// var_dump($office_edit);
		//加载页面
		if (!empty($result1)) {
			
			message("删除成功",$url);
		}else{

			message("删除失败");
		}
	}else if ($operation == "update") {
		
		if ($_W['ispost']) {
			
			
			$update['content'] = $_GPC['content']; 
			$update['status'] = $_GPC['status']; 
			$update['uid'] = $_GPC['uid']; 
			$update['name'] = $_GPC['name']; 
			
				$update['time'] = date("Y-m-d H:i:s",time());
				//执行修改
				// var_dump($update);
				$res = pdo_update("move_question",$update,array("id"=>$_GPC['id']));
				// var_dump($res);
				// die;
				if (!empty($res)) {
					
					message("修改成功",$url);
				}else{
					message("修改失败");
				}
		}
	}else if ($operation == "type_list") {
	
		//设置条件
		$where1 = '';
		$params1 = array();
		//判断是否有搜索
		if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

			$where1.=' WHERE `time` LIKE :keyword `name` LIKE :keywords ';
			$params1[':keyword'] = "%{$_GPC['keyword']}%";
			$params1[':keywords'] = "%{$_GPC['keyword']}%";
			
			
		}
		
		// //查取数据
		$pindex1 = max(1, intval($_GPC['page']));
		$psize1 = 20;
		$total1 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_type').$where1);
		$pager1 = pagination($total1, $pindex1, $psize1,$params1);
		//查取用户信息
		$type_list = pdo_fetchall("SELECT * FROM ".tablename('move_type').$where1." ORDER BY id ASC LIMIT ". ($pindex1 - 1) * $psize1 . ',' . $psize1,$params1);
		// var_dump($type_list);die();
		//加载页面
		include $this->template("type_list",$admin);

	}else if ($operation == "type_add") {
		
		//判断是否为添加
		if ($_W['ispost']) {
			// var_dump($_GPC);
			// die;
			if(empty($_GPC['name'])){

					message('分类名称');
			}
			if(empty($_GPC['content'])){

					message('分类简介');
			}
			
			//接收数据
			$type_add['content'] = $_GPC['content']; 
			$type_add['name'] = $_GPC['name']; 
			$type_add['time'] = date("Y-m-d H:i:s",time()); 
			
			//添加数据
			$resulta = pdo_insert("move_type",$type_add);
			//判断
			if (!empty($resulta)) {
				
				message("添加问题解答信息成功",$url1);
			}else{

				message("添加失败");

			}
			
		}else{
			
			//加载添加页面
			include $this->template("type_add");
		}
		

	}else if ($operation == "type_details") {
		
		//查取信息
		$type_details = pdo_fetch("SELECT * FROM ".tablename("move_type")." WHERE id=:id",array("id"=>$_GPC['id']));
		$type_details['content'] = htmlspecialchars_decode($type_details['content']);
		
		
		//加载页面
		include $this->template("type_details",$admin);

	}else if ($operation == "type_edit") {
		
		//查取信息
		$type_edit = pdo_fetch("SELECT * FROM ".tablename("move_type")." WHERE id=:id",array("id"=>$_GPC['id']));
		// $activity = pdo_fetchall("SELECT * FROM ".tablename("telecom_business"));
		//加载页面
		include $this->template("type_edit");

	}else if ($operation == "type_delete") {
		
		// echo "nice";
		// die;
		//查取信息
		$resulte = pdo_delete("move_type",array("id"=>$_GPC['id']));
		// var_dump($office_edit);
		//加载页面
		if (!empty($resulte)) {
			
			message("删除成功",$url1);
		}else{

			message("删除失败");
		}
	}else if ($operation == "type_update") {
		
		if ($_W['ispost']) {
			
			
			$type_update['content'] = $_GPC['content']; 
			$type_update['name'] = $_GPC['name']; 
			
				$type_update['time'] = date("Y-m-d H:i:s",time());
				//执行修改
				// var_dump($update);
				$resa = pdo_update("move_type",$type_update,array("id"=>$_GPC['id']));
				// var_dump($res);
				// die;
				if (!empty($resa)) {
					
					message("修改成功",$url1);
				}else{
					message("修改失败");
				}
		}
	}else if ($operation == "classfiy") {
	
		//设置条件
		$where2 = ' WHERE status=0 AND uid='.$_GPC['id'];
		$params2 = array();
		//判断是否有搜索
		if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

			$where2.=' AND `time` LIKE :keyword ';
			$params2[':keyword'] = "%{$_GPC['keyword']}%";
			
			
		}
		
		// //查取数据
		$pindex2 = max(1, intval($_GPC['page']));
		$psize2 = 20;
		$total2 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_question').$where2);
		$pager2 = pagination($total2, $pindex2, $psize2,$params2);
		//查取用户信息
		$classfiy = pdo_fetchall("SELECT * FROM ".tablename('move_question').$where2." ORDER BY id ASC LIMIT ". ($pindex2 - 1) * $psize2 . ',' . $psize2,$params2);
		foreach ($classfiy as $key2 => $value2) {
			
			$classfiy[$key2]['content'] = htmlspecialchars_decode($value2['content']);
			// $question_list[$key2]['type'] = pdo_fetch("SELECT * FROM ".tablename("move_type")." WHERE id=:id",array("id"=>$value2['uid']));
		}
		
		//加载页面
		include $this->template("classfiy",$admin);

	}else if ($operation == "question_delete") {
		
		//判断是否为post
		if ($_W['ispost']) {
			
			//判断是否为空
			if (!empty($_POST['ipn-id'][0])) {
				
				// $pid = array();
				//接收数据
				$pid = implode(",",$_GPC['ipn-id']);
				// var_dump($pid);die();
				//修改数据
				$sql = "DELETE FROM ims_move_type WHERE id in (".$pid.")";
				
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
	}else if ($operation == "boot_delete") {
		
		//判断是否为post
		if ($_W['ispost']) {
			
			//判断是否为空
			if (!empty($_POST['ipn-id'][0])) {
				
				// $pid = array();
				//接收数据
				$pid3 = implode(",",$_GPC['ipn-id']);
				// var_dump($pid);die();
				//修改数据
				$sql3 = "DELETE FROM ims_move_question WHERE id in (".$pid3.")";
				
				$rese3 = pdo_query($sql3);
				//判断是否删除成功
				if (!empty($rese3)) {
					
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