<?php 

	
	global $_W,$_GPC;
	$action = 'appraise';
	$url = $this->createMobileUrl($action, array('op' => 'list'));
	
	$uid = $_W['fans']['uid'];//获取当前用户的uid
	$openid = $_W['fans']['openid'];//获取当前用户的openid
	$avatar = $_W['fans']['tag']['avatar'];//获取当前用户的头像信息
	$nickname = $_W['fans']['tag']['nickname'];//获取当前用户的昵称信息
	$uniacid = $_W['fans']['uniacid'];//获取当前用户的昵称信息
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
	

	if ($operation == "list") {
		
		//查找用户信息
		$person = pdo_fetch("SELECT * FROM ".tablename("move_customer")." WHERE openid=:openid AND uid=:uid",array("openid"=>$openid,"uid"=>$uid));
		//判断是否有此客户信息
		if (empty($person)) {
			
			//加载登录页面
			include $this->template("login");
			
		}else if (!empty($person) && empty($person['name'])) {
			
			$pid = $person['id'];
			//加载注册页面
			include $this->template("enroll",$pid);	
		
		}else if(!empty($person) && !empty($person['name']) && !empty($person['openid'])){

			// openid=:openid AND
			//查找未评价的订单
			$appraise = pdo_fetchall("SELECT * FROM ".tablename("move_declare")." WHERE openid=:openid AND come!=2 AND status=3 AND appraise=0 ",array("openid"=>$openid));
			$evaluate = pdo_fetch("SELECT * FROM ".tablename("move_declare")." WHERE openid=:openid AND come!=2 AND status=3 AND appraise=1 ORDER BY time DESC ",array("openid"=>$openid));
			// var_dump($appraise);die();
			//判断是否为空
			if (!empty($appraise)) {

				include $this->template("appraise");

			}else if (empty($appraise) && !empty($evaluate)) {

				$evaluate['maintain'] = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$evaluate['pid']));
				include $this->template("evaluate");

			}else if (empty($appraise) && empty($evaluate)) {
				
				message("对不起您没有需要评价的订单！！！",$this->createMobileUrl("customer",array("op"=>"list")),"info");
			}

		}else{

			message("对不起未找到与您匹配信息！",$this->createMobileUrl("question",array("op"=>"list")),"info");
		}
		
		
		
		
	}else if ($operation == "add") {
		
		//判断
		if (!empty($_GPC['id'])) {
			
			//查找数据
			$add = pdo_fetch("SELECT * FROM ".tablename("move_declare")." WHERE id=:id",array("id"=>$_GPC['id']));
			//查找维修工信息
			$add['maintain'] = pdo_fetch("SELECT * FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$add['pid']));
			$eval = pdo_fetch("SELECT * FROM ".tablename("move_declare")." WHERE openid=:openid AND come!=2 AND status=3 AND appraise=1 ORDER BY ID DESC ",array("openid"=>$openid));
			//判断是否存在维修工
			if (!empty($add) && !empty($add['maintain']) ) {
				
				include $this->template("add",$eval);

			}else if(!empty($add) && empty($add['maintain'])){

				//修改此信息 员工信息不存在
				$rer = pdo_update("move_declare",array("appraise"=>3),array("id"=>$_GPC['id']));

				if (!empty($rer)) {
					
					message("您此订单评价已过期",$this->createMobileUrl("appraise",array("op"=>'list')));
				}else{

					message("您此订单评价已过期",$this->createMobileUrl("appraise",array("op"=>'list')));
				}
			}else{

				message("对不起没有找到与您此订单的匹配的维修工信息。",$this->createMobileUrl("appraise",array("op"=>"list")),"info");

			}

		}else{

			message("对不起您尚未有任何可评价的订单！！！",$this->createMobileUrl("customer",array("op"=>"list")),"info");
		}

	}else if ($operation == "estimate") {
		
		//判断是否为post
		if ($_W['ispost']) {
			
			
			//判断是否评价星级
			if ($_POST['star'] != "") {
				
				//接收数据
				$update['star'] = intval($_POST['star']);
				$update['appraise'] = 1;
				$update['time'] = time();
				//修改数据
				$res = pdo_update("move_declare",$update,array("id"=>$_POST['id']));

				//判断是否评价成功
				if (!empty($res)) {
					
					 header('Location: '.$this->createMobileUrl("customer",array("op"=>"list")));
				}else{

					message("对不起您评价失败！！！",$this->createMobileUrl("appraise",array("op"=>"list")),"info");
				}
			}else{

				message("对不起评价星级不能为0！！！",$this->createMobileUrl("appraise",array("op"=>"list")),"info");

			}
		}
	}




 ?>