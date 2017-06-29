<?php 

	
	global $_W,$_GPC;
	$action = 'refuse';
	$url = $this->createMobileUrl($action, array('op' => 'list'));
	
	$uid = $_W['fans']['uid'];//获取当前用户的uid
	$openid = $_W['fans']['openid'];//获取当前用户的openid
	$avatar = $_W['fans']['tag']['avatar'];//获取当前用户的头像信息
	$nickname = $_W['fans']['tag']['nickname'];//获取当前用户的昵称信息
	$uniacid = $_W['fans']['uniacid'];//获取当前用户的昵称信息
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
	
	if ($operation == "list") {
	
		//查找员工信息
		$staff = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE openid=:openid AND uid=:uid ",array("openid"=>$openid,"uid"=>$uid));
		//判断是否有此员工的信息
		if (!empty($staff)) {
			
			//查找未确认的订单
			$refuse = pdo_fetchall("SELECT * FROM ".tablename("move_declare")." WHERE pid=:pid AND status=4 AND appraise=0 ",array("pid"=>$staff['id']));
			
			//判断是否为空
			if (!empty($refuse)) {

				include $this->template("refuse");

			}else if (empty($refuse)) {
				
				message("对不起您没有可以拒绝的订单！！！",$this->createMobileUrl("staff",array("op"=>"list")),"info");
			}
			
		}else{

			message("对不起员工信息没有您的信息，请联系管理员。",$this->createMobileUrl("staff",array("op"=>"list")),"info");
		}
		
		
	}else if ($operation == "update") {
		
		//判断是否为空
		if (!empty($_GPC['id'])) {
			
			//接收数据
			$id = $_GPC['id'];
			//修改信息

			$result = pdo_update("move_declare",array("status"=>5,"refuse"=>$_GPC['refuse']),array("id"=>$id));
			$add1 = pdo_fetch("SELECT * FROM ".tablename("move_declare")." WHERE id=:id",array("id"=>$id));
			//查找维修工信息
			$staff = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$add1['pid']));
			$time1 = date("Y-m-d H:i:s",$add1['time']);
			//判断
			if (!empty($result)) {
				
				//查找客服人员信息
				$message = pdo_fetch("SELECT * FROM ".tablename("users")." WHERE openid is not null ORDER BY service ASC LIMIT 1 ");
				// var_dump($message);
				//判断是否有客服人员信息
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
								//模板消息
								$template=array(
								        'touser'=>$message['openid'],
								        'template_id'=>"zSLGkMRn3K11zLTO_dGh84adZc1rlIVmhN5JHx7grac",
								        'url'=>"http://weixin.qq.com/download",
								        'topcolor'=>"#ED3124",
								        'data'=>array(
								                'first'=>array('value'=>urlencode($mcsinfo['mcs_name']."您好，有维修人员拒接订单！！！")),
								                'keyword1'=>array('value'=>urlencode($add1['details']),'color'=>'#ED3124 ','font-size'=>'30px'),
								                'keyword2'=>array('value'=>urlencode($staff['name']),'color'=>'#ED3124 ','font-size'=>'30px'),
								                'keyword3'=>array('value'=>urlencode($staff['phone']),'color'=>'#ED3124 ','font-size'=>'30px'),
								                'keyword4'=>array('value'=>urlencode($add1['refuse']),'color'=>'#ED3124 ','font-size'=>'30px'),
								                'remark'=>array('value'=>urlencode("请联系维修工确认信息，重新分配。"),'color'=>'#ED3124 ','font-size'=>'15px'), )
								            );
							
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

								$service = intval($message['service']) + 1;
								//修改信息
								$remark = pdo_update("users",array("service"=>$service),array("uid"=>$message['uid']));
								// message("拒绝接单订单信息成功",$this->createMobileUrl("staff",array("op"=>'list')),"info");

							}
							// }else{

							// 	message("拒绝接单订单信息成功",$this->createMobileUrl("staff",array("op"=>'list')),"info");
							// }
				}
				message("拒绝接单订单信息成功",$this->createMobileUrl("staff",array("op"=>'list')),"info");

			}else{

				message("拒绝接单订单信息失败",$this->createMobileUrl("refuse",array("op"=>'list')),"info");
			}

		}else{

				message("拒绝接单订单信息失败",$this->createMobileUrl("refuse",array("op"=>'list')),"info");
		}
		
	}else if ($operation == "details") {
		
		//查找信息
		$refuse_details = pdo_fetch("SELECT * FROM ".tablename("move_declare")." WHERE id=:id AND status=4 AND appraise=0 ",array("id"=>$_GPC['id']));

		//加载页面
		include $this->template("refuse_details");

	}




 ?>