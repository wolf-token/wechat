<?php 

	
	global $_W,$_GPC;
	$action = 'recommend';
	$url = $this->createMobileUrl($action, array('op' => 'list'));
	
	$uid = $_W['fans']['uid'];//获取当前用户的uid
	$openid = $_W['fans']['openid'];//获取当前用户的openid
	$avatar = $_W['fans']['tag']['avatar'];//获取当前用户的头像信息
	$nickname = $_W['fans']['tag']['nickname'];//获取当前用户的昵称信息
	$uniacid = $_W['fans']['uniacid'];//获取当前用户的昵称信息
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
	

	if ($operation == "list") {
	
		//查找此人信息
		$person = pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE openid=:openid AND uid=:uid",array("openid"=>$openid,"uid"=>$uid));
		// var_dump($person);die();
		//判断是否有此客户信息
		// if (empty($person)) {
			
		// 	//加载登录页面
		// 	include $this->template("login");

		// }else if (!empty($person) && empty($person['name'])) {
		
		// 	$pid = $person['id'];
		// 	//加载注册页面
		// 	include $this->template("enroll",$pid);	
		
		// }else if(!empty($person) && !empty($person['name']) && !empty($person['openid'])){

			include $this->template("recommend");

		// }else{

		// 	message("对不起未找到与您匹配信息！",$this->createMobileUrl("question",array("op"=>"list")));
		// }
		
		
	}else if ($operation == "add") {
	
		if ($_W['ispost']) {
			
		// $recommend_id = pdo_fetch("SELECT * FROM ".tablename("qrcode_stat")." WHERE openid=:openid AND uniacid=uniacid ORDER BY id DESC LIMIT 1",array("openid"=>$openid,"uniacid"=>$uniacid));
		// $scene_str = explode(":",$recommend_id['scene_str']);
		// $type = $scene_str[1];
		// $recommend = $scene_str[3];

		// //判断此人是否已经注册信息
		// $customer = pdo_fetchall("SELECT * FROM ".tablename("move_customer")." WHERE openid=:openid ",array("openid"=>$openid));
		
		// if (empty($customer)) {
			
		// 	$add['openid'] = $openid;
		// 	$add['nickname'] = $nickname;//获取当前用户的昵称信息
		// 	$add['avatar'] = $avatar;//获取当前用户的头像信息
		// 	$add['uid'] = $uid;//获取当前用户的uid
		// 	$add['time'] = time();

		// 	//判断此人扫码是扫的和人的码
		// 	//2为扫员工码，1为扫客户码
		// 	if (!empty($type) && !empty($recommend)) {
	
				
		// 		//判断扫码的类型
		// 		//扫客户码

		// 		if ($type == 1) {
					
		// 			$add['type'] = $type;
		// 			$add['pid'] = $recommend;

		// 			//插入到数据库
		// 			$result = pdo_insert("move_customer",$add);
		// 			pdo_insert("move_record",$add1);
		// 			//查找客户信息
		// 			$info = pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE id=:id ",array("id"=>$recommend));
		// 			$infos = intval($info['math'])+1;
		// 			$rr = pdo_update("move_customer",array("math"=>$infos),array("id"=>$info['id']));
		// 			//判断是否插入成功
		// 			if (empty($result)) {
						
		// 				message("推荐业务信息错误",$this->createMobileUrl("recommend",array("op"=>"list")),"error");
		// 			}

		// 		}else if ($type == 2) {
					
		// 			$add['type'] = $type;
		// 			$add['pid'] = $recommend;

		// 			//插入到数据库
		// 			// $result1 = pdo_insert("move_customer",$add);
		// 			// pdo_insert("move_record",$add1);
		// 			$too = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$recommend));
		// 			$toos = intval($too['math'])+1;
		// 			pdo_update("move_staff",array("math"=>$toos),array("id"=>$too['id']));
					
		// 			if (empty($result1)) {

		// 				message("推荐业务信息错误",$this->createMobileUrl("recommend",array("op"=>"list")),"error");
		// 			}

		// 		}

		// 	}
		// 	// else{

		// 	// 		$add['type'] = 3;
		// 	// 		$add['pid'] = 0;

		// 	// 		//插入到数据库
		// 	// 		$result2 = pdo_insert("move_customer",$add);

		// 	// 		if (empty($result2)) {
						
		// 	// 		message("推荐业务信息错误",$this->createMobileUrl("recommend",array("op"=>"list")),"error");
		// 	// 		}
		// 	// }


		// }

		
					//查找申报人的来源
					$come = pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE openid=:openid ",array("openid"=>$openid));
					$comea = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE openid=:openid ",array("openid"=>$openid));
					if (!empty($come) && !empty($comea)) {
						
						$add1['come'] = 1;
						$add1['pid'] = $come['id'];

					}
					if (empty($come) && empty($comea)) {
						
						$add1['come'] = 1;
						$add1['pid'] = 0;

					}
					if (empty($come) && !empty($comea)) {
						
						$add1['come'] = 0;
						$add1['pid'] = $comea['id'];
						
					}
					if (!empty($come) && empty($comea)) {
						
						$add1['come'] = 1;
						$add1['pid'] = $come['id'];
						
					}
					$lu = pdo_fetchall("SELECT * FROM ".tablename("move_recommend")." WHERE name=:name AND phone=:phone AND address=:address AND content=:content AND openid=:openid",array("name"=>$_GPC['name'],"openid"=>$openid,"phone"=>$_GPC['phone'],"content"=>$_GPC['content'],"address"=>$_GPC['address']));

					$add1['name'] = $_GPC['name'];
					$add1['address'] = $_GPC['address'];
					$add1['content'] = $_GPC['content'];
					$add1['phone'] = $_GPC['phone'];
					$add1['openid'] = $openid;
					$add1['uid'] = $uid;
					$add1['avatar'] = $avatar;
					$add1['nickname'] = $nickname;
					$add1['time'] = time();

					//判断
					if (empty($lu)) {
						
						//添加数据
						$re = pdo_insert("move_recommend",$add1);

						//判断
						if (!empty($re)) {

						 // $this->createMobileUrl("recommend",array("op"=>"list"));
							header('Location: '.$this->createMobileUrl("interact",array("op"=>"list")));
						
							
						}else{

						  header('Location: '.$this->createMobileUrl("recommend",array("op"=>"list")));

						}
					}else{

							message("您已经录入信息",$this->createMobileUrl("customer",array("op"=>"list")),"info");
					}
					

		}

	}




 ?>