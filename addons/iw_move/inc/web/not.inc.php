<?php 


	global $_W,$_GPC;
	$action = 'not';
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

			$where.=' WHERE `name` LIKE :keyword OR `phone` LIKE :keywordl OR `plat` LIKE :keywords ';
			
			$params[':keyword'] = "%{$_GPC['keyword']}%";
			$params[':keywordl'] = "%{$_GPC['keyword']}%";
			$params[':keywords'] = "%{$_GPC['keyword']}%";
			
		}
		
		// //查取数据
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('move_declare').$where);
		$pager = pagination($total, $pindex, $psize,$params);
		//查取用户信息
		$indent_list = pdo_fetchall("SELECT * FROM ".tablename('move_declare').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
		
		
		//加载页面
		include $this->template("indent_list",$admin);

	}else if ($operation == "update") {
		
		//判断是否为添加
		if ($_W['ispost']) {
			
			
			// if(empty($_GPC['name'])){

			// 		message('维修工的姓名不能为空');
			// }
			
			// if(empty($_GPC['phone'])){

			// 		message('维修工的电话不能为空');
			// }
			if(empty($_GPC['tid'])){

					message('维修工的班组ID不能为空');
			}
			
			
			//判断
			if (!empty($_GPC['id'])) {

				// $message = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE tid=:tid ORDER BY service ASC LIMIT 1 ",array("tid"=>$_GPC['tid']));
				//查找维修工信息
				$message = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE tid=:tid ORDER BY service ASC LIMIT 1 ",array("tid"=>$_GPC['tid']));
				// $message = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE name=:name AND phone=:phone AND tid=:tid ORDER BY service ASC LIMIT 1 ",array("name"=>$_GPC['name'],"phone"=>$_GPC['phone'],"tid"=>$_GPC['tid']));
				//查找订单信息
				$add1 = pdo_fetch("SELECT * FROM ".tablename("move_declare")." WHERE id=:id ",array("id"=>$_GPC['id']));
				//判断是否已经分配信息
				// if (!empty($add1['openid']) && !empty($add1['wid']) && !empty($add1['pid'])) {
					
				// 	//查找原来的员工的数据
				// 	$old = 	pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE id=:id ",array("id"=>$add1['id']));
				// 	//修改原来已经分配员工的维修数量
				// 	//判断是否为0
				// 	if ($old['math'] != 0 ) {
						
						
				// 	}
				// 	$max = intval($old['math'])
				// }
				$add1['time'] = date("Y-m-d H:i:s",$add1['time']);
				$mid = $_GPC['id']; 
				    if (!empty($message)) {
				        
				       
				        //发送消息通知
				    		date_default_timezone_set('PRC');
				
							$data1 = json_decode(file_get_contents("token.json"),true);

							if ($data1->expire_time < time()) {

								$account_api = WeAccount::create();
								// $account_api->clearAccessToken();
								$access_token = $account_api->getAccessToken();
								date_default_timezone_set('PRC');
								$data1->expire_time = time() + 7000;
								$data1->access_token = $access_token;
								 $fp = fopen("token.json", "w");
							    fwrite($fp, json_encode($data1));
							    fclose($fp);

							}else{
								
								$access_token = $data1['access_token'];
								
							}

							//echo $this->access_token;exit;
							//判断类型
							if ($add1['type'] == 1) {
								
								//模板消息
								$template=array(
								        'touser'=>$message['openid'],
								        'template_id'=>"KTGd44Bn1AFLX-5TIn87Zz8toAvVyMdcKxCqoCITMNw",
								        'url'=>"http://weixin.qq.com/download",
								        'topcolor'=>"#53B7DB",
								        'data'=>array(
								                'first'=>array('value'=>urlencode($mcsinfo['mcs_name']."你好，你收到一张宽带业务报修工单！！！")),
								                'keyword1'=>array('value'=>urlencode($add1['details']),'color'=>'#0A12B3','font-size'=>'30px'),
								                'keyword2'=>array('value'=>urlencode($add1['name']),'color'=>'#0A12B3','font-size'=>'30px'),
								                'keyword3'=>array('value'=>urlencode($add1['phone']),'color'=>'#0A12B3','font-size'=>'30px'),
								                'keyword4'=>array('value'=>urlencode($add1['plat']),'color'=>'#0A12B3','font-size'=>'30px'),
								                'keyword5'=>array('value'=>urlencode($message['name']),'color'=>'#0A12B3','font-size'=>'30px'),
								                'remark'=>array('value'=>urlencode("请不要点击详情，此单生成时间：".$add1['time']),'color'=>'#0A12B3','font-size'=>'15px'), )
								            );
							}else{
								//模板消息
								$template=array(
								        'touser'=>$message['openid'],
								        'template_id'=>"ngxt5b7D35pGbeiAWqWUt3B98O7D2UfOjqRyhfag29o",
								        'url'=>"http://weixin.qq.com/download",
								        'topcolor'=>"#53B7DB",
								        'data'=>array(
								                'first'=>array('value'=>urlencode($mcsinfo['mcs_name']."您好,您有新的订单！！！")),
								                'keyword1'=>array('value'=>urlencode("报装"),'color'=>'#0A12B3','font-size'=>'30px'),
								                'keyword2'=>array('value'=>urlencode($add1['name']),'color'=>'#0A12B3','font-size'=>'30px'),
								                'keyword3'=>array('value'=>urlencode($add1['phone']),'color'=>'#0A12B3','font-size'=>'30px'),
								                'keyword4'=>array('value'=>urlencode($add1['plat']),'color'=>'#0A12B3','font-size'=>'30px'),
								                'keyword5'=>array('value'=>urlencode($message['name']),'color'=>'#0A12B3','font-size'=>'30px'),
								                'remark'=>array('value'=>urlencode('请不要点击详情，详情：'.$add1['details']),'color'=>'#0A12B3','font-size'=>'15px'), )
								            );
							}
							
							$json_template=json_encode($template);
							//echo $json_template;
							//echo $this->access_token;
							$url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
							$data = urldecode($json_template);
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
							curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
							// POST数据
							curl_setopt($ch, CURLOPT_POST, 1);
							// 把post的变量加上
							curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
							$output = curl_exec($ch);
							curl_close($ch);
							$res=$output;
				        
				        if ($res[errcode]==0){

				            //修改信息
				            pdo_update("move_declare",array("pid"=>$message['id'],'status'=>4),array("id"=>$mid));
				            $meth = intval($message['service'])+1;
				            pdo_update("move_staff",array("service"=>$meth),array("id"=>$message['id']));

				            // message("分配订单信息成功",$url); 
				           header('Location: '.$this->createWebUrl("fault",array("op"=>"list")));
				        }else{

				            pdo_update("move_declare",array("pid"=>0,'status'=>2),array("id"=>$mid));      
				            message("分配员工失败,等待管理员分配",$this->createWebUrl("not",array("op"=>"list")),"error");
				        }

				    }else{

				        pdo_update("move_declare",array("pid"=>0,'status'=>2),array("id"=>$mid));

				        message("未找到与之匹配的维修工,等待管理员分配",$this->createWebUrl("not",array("op"=>"list")),"error");
				    }

			}else{

				message("加载修改信息失败");

			}
			
		}else{
			
			//加载添加页面
			message("加载修改信息失败",$this->createWebUrl("indent",array('op'=>'list')),"error");
		}
	

	}else if ($operation == "edit") {
		
		//查取信息
		$not_edit = pdo_fetch("SELECT * FROM ".tablename("move_declare")." WHERE id=:id",array("id"=>$_GPC['id']));
		
		
		//加载页面
		include $this->template("not_edit");

	}

 ?>