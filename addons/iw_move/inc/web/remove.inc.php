<?php 


	global $_W,$_GPC;
	$action = 'remove';
	$uniacid = $_W['uniacid'];
	$uid = intval($_W['uid']);
	$part = pdo_fetch('SELECT * FROM ' . tablename('users_permission') . ' WHERE uniacid = :aid AND uid = :uid AND type = :type', array(':aid' => $uniacid, ':uid' => $uid, ':type' => "system"));
	$url = $this->createWebUrl($action, array('op' => 'list'));
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';

	if ($operation == "list") {
		
		//删除用户数据
		if ($_W['ispost']) {
			
			  $start = strtotime($_GPC['datelimit']['start']) ;
              $end = strtotime($_GPC['datelimit']['end']) ;
              $sql = "DELETE FROM ims_move_customer WHERE time>=".$start." AND time<=".$end;
              //删除数据
              $result = pdo_query($sql);

              //判断
              if (!empty($result)) {
              	
              		message("删除信息成功",$this->createWebUrl("user",array("op"=>'list')),"success");
              }else{

              		message("删除信息失败");
              }
		}
	}else if ($operation == "add") {
		
		//删除用户数据
		if ($_W['ispost']) {
			
			  $start1 = strtotime($_GPC['datelimit']['start']) ;
              $end1 = strtotime($_GPC['datelimit']['end']) ;
              $sql1 = "DELETE FROM ims_move_staff WHERE time>=".$start1." AND time<=".$end1;
              //删除数据
              $result1 = pdo_query($sql1);

              //判断
              if (!empty($result1)) {
              	
              		message("删除信息成功",$this->createWebUrl("staff",array("op"=>'list')),"success");
              }else{

              		message("删除信息失败");
              }
		}

	}else if ($operation == "edit") {
		
		//删除用户数据
		if ($_W['ispost']) {
			
			  $start2 = strtotime($_GPC['datelimit']['start']) ;
			  $type = $_GPC['type'];
              $end2 = strtotime($_GPC['datelimit']['end']) ;
              $sql2 = "DELETE FROM ims_move_recommend WHERE time>=".$start2." AND time<=".$end2." AND status=".$type;
              //删除数据
              $result2 = pdo_query($sql2);

              //判断
              if (!empty($result2)) {
              	
              		message("删除信息成功",$this->createWebUrl("recommend",array("op"=>'list')),"success");
              }else{

              		message("删除信息失败");
              }
		}
	}else if ($operation == "update") {
		
		//删除用户数据
		if ($_W['ispost']) {
			
			  $start3 = strtotime($_GPC['datelimit']['start']) ;
			  $appraise = $_GPC['appraise'];
              $end3 = strtotime($_GPC['datelimit']['end']) ;
              $sql3 = "DELETE FROM ims_move_declare WHERE time>=".$start3." AND time<=".$end3." AND appraise=".$appraise;
              //删除数据
              $result3 = pdo_query($sql3);

              //判断
              if (!empty($result3)) {
              	
              		message("删除信息成功",$this->createWebUrl("estimate",array("op"=>'list')),"success");
              }else{

              		message("删除信息失败");
              }
		}

	}else if ($operation == "delete") {
		
		//删除用户数据
		if ($_W['ispost']) {
			
			  $start4 = strtotime($_GPC['datelimit']['start']) ;
			  $status = $_GPC['status'];
              $end4 = strtotime($_GPC['datelimit']['end']) ;
              $sql4 = "DELETE FROM ims_move_declare WHERE time>=".$start4." AND time<=".$end4." AND status=".$status;
              //删除数据
              $result4 = pdo_query($sql4);

              //判断
              if (!empty($result4)) {
              	
              		message("删除信息成功",$this->createWebUrl("fault",array("op"=>'list')),"success");
              }else{

              		message("删除信息失败");
              }
		}
	}



 ?>