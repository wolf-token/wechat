<?php 


	global $_W,$_GPC;
	$action = 'recommend';
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

			$where.=' WHERE `name` LIKE :keyword OR `address` LIKE :keywordl OR `phone` LIKE :keywords ';
			
			$params[':keyword'] = "%{$_GPC['keyword']}%";
			$params[':keywordl'] = "%{$_GPC['keyword']}%";
			$params[':keywords'] = "%{$_GPC['keyword']}%";
			
		}
		
		// //查取数据
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_recommend').$where);
		$pager = pagination($total, $pindex, $psize,$params);
		//查取用户信息
		$recommend_list = pdo_fetchall("SELECT * FROM ".tablename('move_recommend').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
		
		
		//加载页面
		include $this->template("recommend_list",$admin);

	}else if ($operation == "add") {
		
		//判断是否为添加
		if ($_W['ispost']) {
			
			if(empty($_GPC['name'])){

					message('姓名不能为空');
			}
			
			if(empty($_GPC['address'])){

					message('用户地址不能为空');
			}
			if(empty($_GPC['phone'])){

					message('电话不能为空');
			}
			if(empty($_GPC['content'])){

					message('推荐业务描述不能为空');
			}
			
			
			//接收数据
			$add['name'] = $_GPC['name']; 
			$add['address'] = $_GPC['address']; 
			$add['content'] = $_GPC['content']; 
			
			$add['phone'] = $_GPC['phone']; 
			$add['come'] = 2; 
			
			$add['time'] = time(); 
			
			//添加数据
			$result = pdo_insert("move_recommend",$add);
			//判断
			if (!empty($result)) {
				
				message("添加推荐业务信息成功",$url);
			}else{

				message("添加失败");

			}
			
		}else{
			
			//加载添加页面
			include $this->template("recommend_add");
		}
		

	}else if ($operation == "details") {
		
		//查取信息
		$recommend_details = pdo_fetch("SELECT * FROM ".tablename("move_recommend")." WHERE id=:id",array("id"=>$_GPC['id']));

		//判断是否已经分配
		if ($recommend_details['status'] == 1) {
			
			 //查找分配的信息
			$declare = pdo_fetch("SELECT * FROM ".tablename("move_declare")." WHERE id=:id",array("id"=>$recommend_details['allot']));
			//查找员工信息
			$declare['staff'] = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$declare['pid']));

		}else{

			$declare = '';
		}
		//加载页面
		include $this->template("recommend_details",$declare,$admin);

	}else if ($operation == "edit") {
		
		//查取信息
		$recommend_edit = pdo_fetch("SELECT * FROM ".tablename("move_recommend")." WHERE id=:id",array("id"=>$_GPC['id']));
		
		
		//加载页面
		include $this->template("recommend_edit");

	}else if ($operation == "delete") {
		
		//查取信息
		$result1 = pdo_delete("move_recommend",array("id"=>$_GPC['id']));
		// var_dump($recommend_edit);
		//加载页面
		if (!empty($result1)) {
			
			message("删除成功",$url);
		}else{

			message("删除失败");
		}
	}else if ($operation == "update") {
		
		if ($_W['ispost']) {
			
			$update['name'] = $_GPC['name'];
			$update['address'] = $_GPC['address'];
			$update['phone'] = $_GPC['phone'];
			$update['content'] = $_GPC['content'];
			
			$update['time'] = time();
				//执行修改
				$res = pdo_update("move_recommend",$update,array("id"=>$_GPC['id']));
				if (!empty($res)) {
					
					message("修改成功",$url);
				}else{
					message("修改失败");
				}
		}
	}else if ($operation == "fault") {
		
		//接收数据
		if (!empty($_GPC['id'])) {
			
			//查找信息
			$business = pdo_fetch("SELECT * FROM ".tablename("move_recommend")." WHERE id=:id",array("id"=>$_GPC['id']));
			// $business = $_GPC['id'];
			//加载页面
			include $this->template("business",$admin);

		}else{

			message("错误信息");
		}

	}else if ($operation == "allot") {
		
		//判断是否为添加
		if ($_W['ispost']) {
			
			
			if(empty($_POST['name'])){

					message('姓名不能为空');
			}
			
			if(empty($_POST['plat'])){

					message('申报人所在小区不能为空');
			}
			if(empty($_POST['phone'])){

					message('电话不能为空');
			}
			if(empty($_POST['details'])){

					message('故障详情不能为空');
			}
			if ($_POST['type'] != 0 && $_POST['type'] !=1 ) {
				
				message('申报类型不能为空');
			}
			
			//接收数据
			$add['name'] = $_POST['name']; 
			$add['plat'] = $_POST['plat']; 
			$add['type'] = $_POST['type']; 
			$add['come'] = 2; 
			$add['uid'] = 0; 
			
			$add['phone'] = $_POST['phone']; 
			$add['details'] = $_POST['details']; 
			$add['time'] = time(); 
			$time = date("Y-m-d H:i:s",time());
			//添加数据
			$result = pdo_insert("move_declare",$add);
			$mid = pdo_insertid();
			$rer = pdo_update("move_recommend",array("status"=>1,'allot'=>$mid),array("id"=>$_GPC['rid']));
			//判断
			if (!empty($result) && !empty($rer)) {

				//模糊查找
				$wherea = '';

				if (!empty($_POST['plat']) && isset($_POST['plat'])) {
				    
				    $wherea = ' WHERE `name` LIKE :keyword ';
				    $paramsa[':keyword'] = "%{$_GPC['plat']}%"; 
				}
				//查找班组信息
				$team = pdo_fetch("SELECT * FROM ".tablename("move_team").$wherea." ORDER BY id ASC LIMIT 1 ",$paramsa);
				//查找维修工信息
				$user = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE tid=:tid ORDER BY service ASC LIMIT 1 ",array("tid"=>$team['pid']));
				//查找维修工信息
				    // $user = pdo_fetch("SELECT * FROM ".tablename("move_staff").$wherea." ORDER BY service ASC LIMIT 1 ",$paramsa);
				    
				    if (!empty($user)) {
				        
				        //发送消息通知
					    date_default_timezone_set('PRC');

					    $data4 = json_decode(file_get_contents("token.json"),true);

					    if ($data4->expire_time < time()) {

					        $account_api = WeAccount::create();
					        // $account_api->clearAccessToken();
					        $access_token3 = $account_api->getAccessToken();
					        date_default_timezone_set('PRC');
					        $data4->expire_time = time() + 7000;
					        $data4->access_token = $access_token3;
					         $fp = fopen("token.json", "w");
					        fwrite($fp, json_encode($data4));
					        fclose($fp);

					    }else{
					        
					        $access_token3 = $data4['access_token'];
					        
					    }

					    //echo $this->access_token;exit;
					    //判断类型
					    if ($add['type'] == 1) {
					        
					        //模板消息
					        $template3=array(
					                'touser'=>$user['openid'],
					                'template_id'=>"KTGd44Bn1AFLX-5TIn87Zz8toAvVyMdcKxCqoCITMNw",
					                'url'=>"http://weixin.qq.com/download",
					                'topcolor'=>"#53B7DB",
					                'data'=>array(
					                        'first'=>array('value'=>urlencode($mcsinfo['mcs_name']."你好，你收到一张宽带业务报修工单！！！")),
					                        'keyword1'=>array('value'=>urlencode($add['details']),'color'=>'#0A12B3','font-size'=>'30px'),
					                        'keyword2'=>array('value'=>urlencode($add['name']),'color'=>'#0A12B3','font-size'=>'30px'),
					                        'keyword3'=>array('value'=>urlencode($add['phone']),'color'=>'#0A12B3','font-size'=>'30px'),
					                        'keyword4'=>array('value'=>urlencode($add['plat']),'color'=>'#0A12B3','font-size'=>'30px'),
					                        'keyword5'=>array('value'=>urlencode($user['name']),'color'=>'#0A12B3','font-size'=>'30px'),
					                        'remark'=>array('value'=>urlencode("请不要点击详情，此单生成时间：".$time),'color'=>'#0A12B3','font-size'=>'15px'), )
					                    );
					    }else{
					        //模板消息
					        $template3=array(
					                'touser'=>$user['openid'],
					                'template_id'=>"ngxt5b7D35pGbeiAWqWUt3B98O7D2UfOjqRyhfag29o",
					                'url'=>"http://weixin.qq.com/download",
					                'topcolor'=>"#53B7DB",
					                'data'=>array(
					                        'first'=>array('value'=>urlencode($mcsinfo['mcs_name']."您好,您有新的订单！！！")),
					                        'keyword1'=>array('value'=>urlencode("报装"),'color'=>'#0A12B3','font-size'=>'30px'),
					                        'keyword2'=>array('value'=>urlencode($add['name']),'color'=>'#0A12B3','font-size'=>'30px'),
					                        'keyword3'=>array('value'=>urlencode($add['phone']),'color'=>'#0A12B3','font-size'=>'30px'),
					                        'keyword4'=>array('value'=>urlencode($add['plat']),'color'=>'#0A12B3','font-size'=>'30px'),
					                        'keyword5'=>array('value'=>urlencode($user['name']),'color'=>'#0A12B3','font-size'=>'30px'),
					                        'remark'=>array('value'=>urlencode('请不要点击详情，详情：'.$add['details']),'color'=>'#0A12B3','font-size'=>'15px'), )
					                    );
					    }
					    
					    $json_template3=json_encode($template3);
					    //echo $json_template;
					    //echo $this->access_token;
					    $url4="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token3;
					    $data6 = urldecode($json_template3);
					    $ch5 = curl_init();
					    curl_setopt($ch5, CURLOPT_URL, $url4);
					    curl_setopt($ch5, CURLOPT_RETURNTRANSFER, 1);
					    curl_setopt($ch5, CURLOPT_SSL_VERIFYPEER, FALSE);
					    curl_setopt($ch5, CURLOPT_SSL_VERIFYHOST, FALSE);
					    // POST数据
					    curl_setopt($ch5, CURLOPT_POST, 1);
					    // 把post的变量加上
					    curl_setopt($ch5, CURLOPT_POSTFIELDS, $data6);
					    $output0 = curl_exec($ch5);
					    curl_close($ch5);
					    $resa=$output0;
				        
				        if ($resa[errcode]==0){

				            //修改信息
				            pdo_update("move_declare",array("pid"=>$user['id'],'status'=>4),array("id"=>$mid));
				            $meth = intval($user['service'])+1;
				            pdo_update("move_staff",array("service"=>$meth),array("id"=>$user['id']));

				            message("添加故障申报信息成功",$url); 
				           
				        }else{

				            pdo_update("move_declare",array("pid"=>0,'status'=>2),array("id"=>$mid));      
				            message("分配员工失败,等待管理员分配",$this->createWebUrl("fault",array("op"=>"list")),"error");
				        }
				    }else{

				        pdo_update("move_declare",array("pid"=>0,'status'=>2),array("id"=>$mid));

				        message("未找到与之匹配的维修工,等待管理员分配",$this->createWebUrl("fault",array("op"=>"list")),"error");
				    }

			}else{

				message("添加失败");

			}
			
		}else{
			
			//加载添加页面
			include $this->template("fault_add");
		}
		

	}else if ($operation == "not") {
		
		//查找推荐业务未分配的订单
		//设置条件
		$where1 = '';
		$params1 = array();
		//判断是否有搜索
		if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

			$where1.=' AND `name` LIKE :keyword OR `address` LIKE :keywordl OR `phone` LIKE :keywords ';
			
			$params1[':keyword'] = "%{$_GPC['keyword']}%";
			$params1[':keywordl'] = "%{$_GPC['keyword']}%";
			$params1[':keywords'] = "%{$_GPC['keyword']}%";
			
		}
		
		// //查取数据
		$pindex1 = max(1, intval($_GPC['page']));
		$psize1 = 20;
		$total1 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_recommend')." WHERE status=0 ".$where1);
		$pager1 = pagination($total1, $pindex1, $psize1,$params1);
		//查取用户信息
		$recommend_not = pdo_fetchall("SELECT * FROM ".tablename('move_recommend')." WHERE status=0 ".$where1." ORDER BY id ASC LIMIT ". ($pindex1 - 1) * $psize1 . ',' . $psize1,$params1);
		
		
		//加载页面
		include $this->template("recommend_not",$admin);

	}else if ($operation == "has") {
		
		//查找推荐业务已经分配的订单
		//设置条件
		$where2 = '';
		$params2 = array();
		//判断是否有搜索
		if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

			$where2.=' AND `name` LIKE :keyword OR `address` LIKE :keywordl OR `phone` LIKE :keywords ';
			
			$params2[':keyword'] = "%{$_GPC['keyword']}%";
			$params2[':keywordl'] = "%{$_GPC['keyword']}%";
			$params2[':keywords'] = "%{$_GPC['keyword']}%";
			
		}
		
		// //查取数据
		$pindex2 = max(1, intval($_GPC['page']));
		$psize2 = 20;
		$total2 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_recommend')." WHERE status=1 ".$where2);
		$pager2 = pagination($total2, $pindex2, $psize2,$params2);
		//查取用户信息
		$recommend_has = pdo_fetchall("SELECT * FROM ".tablename('move_recommend')." WHERE status=1 ".$where2." ORDER BY id ASC LIMIT ". ($pindex2 - 1) * $psize2 . ',' . $psize2,$params2);
		
		
		//加载页面
		include $this->template("recommend_has",$admin);

	}else if ($operation == "down") {
		
		include $this->template("recommend_down");
	}else if ($operation == "recommend_delete") {
		
		//判断是否为post
		if ($_W['ispost']) {
			
			//判断是否为空
			if (!empty($_POST['ipn-id'][0])) {
				
				// $pid = array();
				//接收数据
				$pid = implode(",",$_GPC['ipn-id']);
				// var_dump($pid);die();
				//修改数据
				$sql = "DELETE FROM ims_move_recommend WHERE id in (".$pid.")";
				
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
		
		include $this->template("recommend_delete");
	}

 ?>