<?php
if (!defined('IN_IA')) 
{
	exit('Access Denied');
}
global $_W;
global $_GPC;
$info = pdo_fetch('select * from ' . tablename('sz_yi_member') . ' where  openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $_POST['token']));
if ($info) 
{
	if (is_app()) 
	{
		$lifeTime = 24 * 3600 * 3 * 100;
	}
	else 
	{
		$lifeTime = 24 * 3600 * 3;
	}
	session_set_cookie_params($lifeTime);
	@session_start();
	$cookieid = '__cookie_sz_yi_userid_' . $_W['uniacid'];
	setcookie($cookieid, base64_encode($info['openid']));
	echo json_encode(array('status' => 1));
	return 1;
}
echo json_encode(array('status' => 0));
?>