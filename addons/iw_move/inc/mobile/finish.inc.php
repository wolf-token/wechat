<?php 

	
	global $_W,$_GPC;
	$action = 'finish';
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
			$finish = pdo_fetchall("SELECT * FROM ".tablename("move_declare")." WHERE pid=:pid AND status=1 AND appraise=0 ",array("pid"=>$staff['id']));
			
			//判断是否为空
			if (!empty($finish)) {

				include $this->template("finish");

			}else if (empty($finish)) {
				
				message("对不起您没有需要确认的订单！！！",$this->createMobileUrl("staff",array("op"=>"list")),"info");
			}
			
		}else{

			message("对不起员工信息没有您的信息，请联系管理员。",$this->createMobileUrl("staff",array("op"=>"list")),"info");
		}
		
		
	}else if ($operation == "add") {
		
		//判断是否为空
		if (!empty($_GPC['id'])) {
			
			//接收数据
			$id = $_GPC['id'];
			//修改信息

			$result = pdo_update("move_declare",array("status"=>3),array("id"=>$id));

			//判断
			if (!empty($result)) {
				
				message("确认订单信息成功",$this->createMobileUrl("staff",array("op"=>'list')),"info");

			}else{

				message("确认订单信息失败",$this->createMobileUrl("finish",array("op"=>'list')),"info");
			}

		}else{

				message("确认订单信息失败",$this->createMobileUrl("finish",array("op"=>'list')),"info");
		}
		
	}




 ?>