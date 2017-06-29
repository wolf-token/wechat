<?php 


	global $_W,$_GPC;
	$action = 'fault';
	$uniacid = $_W['uniacid'];
	$uid = intval($_W['uid']);
	$part = pdo_fetch('SELECT * FROM ' . tablename('users_permission') . ' WHERE uniacid = :aid AND uid = :uid AND type = :type', array(':aid' => $uniacid, ':uid' => $uid, ':type' => "system"));
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
		$fault_list = pdo_fetchall("SELECT * FROM ".tablename('move_declare').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
		
		
		//加载页面
		include $this->template("fault_list",$part);

	}else if ($operation == "add") {
		
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
			$tt = date("Y-m-d H:i:s",time());
			//添加数据
			$result = pdo_insert("move_declare",$add);
			$mid = pdo_insertid();
			//判断
			if (!empty($result)) {

				//模糊查找
				$wherea = '';

				if (!empty($_POST['plat']) && isset($_POST['plat'])) {
				    
				    $wherea = ' WHERE `name` LIKE :keyword ';
				    $paramsa[':keyword'] = "%{$_GPC['plat']}%"; 
				}
				$team = pdo_fetch("SELECT * FROM ".tablename("move_team").$wherea." ORDER BY id ASC LIMIT 1 ",$paramsa);
				// var_dump($team);die();
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
					                        'remark'=>array('value'=>urlencode("请不要点击详情，此单生成时间：".$tt),'color'=>'#0A12B3','font-size'=>'15px'), )
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
		

	}else if ($operation == "details") {
		
		//查取信息
		$fault_details = pdo_fetch("SELECT * FROM ".tablename("move_declare")." WHERE id=:id",array("id"=>$_GPC['id']));
		//判断
		if (!empty($fault_details['pid'])) {
			
			//查找分配员工信息
			$staff = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$fault_details['pid']));

		}
		//查找推荐人信息
		if ($fault_details['come'] == 0 ) {
			
			$recommend = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$fault_details['uid']));
			//判断是否为空
			if (empty($recommend)) {
				
				$recommend = pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE openid=:openid",array("openid"=>$fault_details['openid']));
			}
		}
		if ($fault_details['come'] == 1 ) {
			
			//判断是否有信息
			if ($fault_details['uid'] == 0) {
				
				//查找信息
				$recommend =  pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE openid=:openid",array("openid"=>$fault_details['openid']));
				if (!empty($recommend)) {
					//修改信息
					$rr1 = pdo_update("move_declare",array("uid"=>$recommend['id']));
				}
				
			}else{

				$recommend = pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE id=:id",array("id"=>$fault_details['uid']));
			}
			
		}
		// var_dump($recommend);
		// var_dump($staff);
		//加载页面
		include $this->template("fault_details",$recommend,$staff,$part);

	}else if ($operation == "edit") {
		
		//查取信息
		$fault_edit = pdo_fetch("SELECT * FROM ".tablename("move_declare")." WHERE id=:id",array("id"=>$_GPC['id']));
		
		
		//加载页面
		include $this->template("fault_edit");

	}else if ($operation == "delete") {
		
		//查取信息
		$delete = pdo_fetch("SELECT * FROM ".tablename("move_declare")." WHERE id=:id ",array("id"=>$_GPC['id']));
		//删除信息
		$result1 = pdo_delete("move_declare",array("id"=>$_GPC['id']));
		// var_dump($declare_edit);
		//加载页面
		if (!empty($result1)) {
			//判断是否达到通知的标准
			if ($delete['status'] == 1 || $delete['status'] == 4) {
				
				//查找维修工信息
				$admin = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE id=:id ",array("id"=>$delete['pid']));
				$tiem = date("Y-m-d H:i:s",time());
				$delete['time'] = date("Y-m-d H:i:s",$delete['time']);
				if (!empty($admin)) {
					
						date_default_timezone_set('PRC');

						$date = json_decode(file_get_contents("token.json"),true);

						if ($date->expire_time < time()) {

						    $account_api = WeAccount::create();
						    // $account_api->clearAccessToken();
						    $access_taken = $account_api->getAccessToken();
						    date_default_timezone_set('PRC');
						    $date->expire_time = time() + 7000;
						    $date->access_token = $access_taken;
						     $fpa = fopen("token.json", "w");
						    fwrite($fpa, json_encode($date));
						    fclose($fpa);

						}else{
						    
						    $access_taken = $date['access_token'];
						    
						}

						//echo $this->access_token;exi
						    
						    //模板消息
						    $templatea=array(
						            'touser'=>$admin['openid'],
						            'template_id'=>"B8PuYUxMJzW5FxHt_9SFVwE0D2TqWQLWjLpOwS25f7c",
						            'url'=>"http://weixin.qq.com/download",
						            'topcolor'=>"#53B7DB",
						            'data'=>array(
						                    'first'=>array('value'=>urlencode($mcsinfo['mcs_name']."你好，你有工单已经关闭！！！")),
						                    'keyword1'=>array('value'=>urlencode($delete['details']),'color'=>'#0A12B3','font-size'=>'30px'),
						                    'keyword2'=>array('value'=>urlencode($delete['plat']),'color'=>'#0A12B3','font-size'=>'30px'),
						                    'keyword3'=>array('value'=>urlencode($delete['time']),'color'=>'#0A12B3','font-size'=>'30px'),
						                    'keyword4'=>array('value'=>urlencode($tiem),'color'=>'#0A12B3','font-size'=>'30px'),
						                    'keyword5'=>array('value'=>urlencode("管理员删除此订单，不需要再提供服务！！！"),'color'=>'#0A12B3','font-size'=>'30px'),
						                    'remark'=>array('value'=>urlencode("感谢您的使用。"),'color'=>'#0A12B3','font-size'=>'15px'), )
						                );


						$json_templatea=json_encode($templatea);
						//echo $json_template;
						//echo $this->access_token;
						$urla="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_taken;
						$date1 = urldecode($json_templatea);
						$cha = curl_init();
						curl_setopt($cha, CURLOPT_URL, $urla);
						curl_setopt($cha, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($cha, CURLOPT_SSL_VERIFYPEER, FALSE);
						curl_setopt($cha, CURLOPT_SSL_VERIFYHOST, FALSE);
						// POST数据
						curl_setopt($cha, CURLOPT_POST, 1);
						// 把post的变量加上
						curl_setopt($cha, CURLOPT_POSTFIELDS, $date1);
						$outpute = curl_exec($cha);
						curl_close($cha);
						$stop=$outpute;

						 if ($stop[errcode]==0){
							
								message("删除成功",$url);
						}
				}

			}
			message("删除成功",$url);
		}else{

			message("删除失败");
		}
	}else if ($operation == "update") {
		
		if ($_W['ispost']) {
			
			$update['name'] = $_GPC['name'];
			$update['plat'] = $_GPC['plat'];
			$update['phone'] = $_GPC['phone'];
			$update['type'] = $_GPC['type'];
			$update['status'] = $_GPC['status'];
			$update['details'] = $_GPC['details'];
			$update['time'] = time();
				//执行修改
				$res = pdo_update("move_declare",$update,array("id"=>$_GPC['id']));
				if (!empty($res)) {
					
					if ($_GPC['status'] == 3) {
						
						$add1 = pdo_fetch("SELECT * FROM ".tablename("move_declare")." WHERE id=:id",array("id"=>$_GPC['id']));
						$message = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$add1['pid']));
						//判断是否为空
						if ($add1['uid'] == 0) {
							
							$customer['openid'] = $add1['openid'];
							$customer['avatar'] = $add1['avatar'];
							$customer['nickname'] = $add1['nickname'];
							
						}else{

							$customer = pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE id=:id",array("id"=>$add1['uid']));

						}
						
						$time = date("Y-m-d H:i:s",time());
						$add1['time'] = date("Y-m-d H:i:s",$add1['time']);
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

						//模板消息
						$template=array(
						        'touser'=>$message['openid'],
						        'template_id'=>"lI0UJ1VYyyzak47KGJzcOtYVa38Ub2YsUGTMSOPJUZs",
						        'url'=>"http://weixin.qq.com/download",
						        'topcolor'=>"#53B7DB",
						        'data'=>array(
						                'first'=>array('value'=>urlencode($mcsinfo['mcs_name']."您好，您已完成工单任务！！！")),
						                'keyword1'=>array('value'=>urlencode($add1['details']),'color'=>'#0A12B3','font-size'=>'30px'),
						                'keyword2'=>array('value'=>urlencode($add1['plat']),'color'=>'#0A12B3','font-size'=>'30px'),
						                'keyword3'=>array('value'=>urlencode($add1['time']),'color'=>'#0A12B3','font-size'=>'30px'),
						                'keyword4'=>array('value'=>urlencode($time),'color'=>'#0A12B3','font-size'=>'30px'),
						                'keyword5'=>array('value'=>urlencode($message['name']),'color'=>'#0A12B3','font-size'=>'30px'),
						                'remark'=>array('value'=>urlencode("感谢您的使用。"),'color'=>'#0A12B3','font-size'=>'15px'), )

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



						 $template1=array(
                                
                                'touser'=>$customer['openid'],
                                'template_id'=>"lI0UJ1VYyyzak47KGJzcOtYVa38Ub2YsUGTMSOPJUZs",
                                'url'=>"http://weixin.qq.com/download",
                                'topcolor'=>"#53B7DB",
                                'data'=>array(
                                        'first'=>array('value'=>urlencode($mcsinfo['mcs_name']."您好，您已完成工单任务！！！")),
                                        'keyword1'=>array('value'=>urlencode($add1['details']),'color'=>'#0A12B3','font-size'=>'30px'),
                                        'keyword2'=>array('value'=>urlencode($add1['plat']),'color'=>'#0A12B3','font-size'=>'30px'),
                                        'keyword3'=>array('value'=>urlencode($add1['time']),'color'=>'#0A12B3','font-size'=>'30px'),
                                        'keyword4'=>array('value'=>urlencode($time),'color'=>'#0A12B3','font-size'=>'30px'),
                                        'keyword5'=>array('value'=>urlencode($message['name']),'color'=>'#0A12B3','font-size'=>'30px'),
                                        'remark'=>array('value'=>urlencode("感谢您的使用。"),'color'=>'#0A12B3','font-size'=>'15px'), 
                                    )

                            );
                        $json_template1=json_encode($template1);
                    
                        $url1="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
                        $data4 = urldecode($json_template1);
                        $ch1 = curl_init();
                        curl_setopt($ch1, CURLOPT_URL, $url1);
                        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, FALSE);
                        curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, FALSE);
                        // POST数据
                        curl_setopt($ch1, CURLOPT_POST, 1);
                        // 把post的变量加上
                        curl_setopt($ch1, CURLOPT_POSTFIELDS, $data4);
                        $output1 = curl_exec($ch1);
                        curl_close($ch1);
					
					}
					message("修改成功",$url);

					
				}else{
					message("修改失败");
				}
		}
	}else if ($operation == "down") {
		
		include $this->template("fault_down");

	}else if ($operation == "fault_delete") {
		
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
	}else if ($operation == "time") {
		
		include $this->template("fault_delete");
	}


 ?>