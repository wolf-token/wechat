<?php
if (!defined('IN_IA')) 
{
	exit('Access Denied');
}
global $_W;
global $_GPC;
$operation = ((!empty($_GPC['op']) ? $_GPC['op'] : 'display'));
$openid = m('user')->getOpenid();
if (empty($openid)) 
{
	$openid = $_GPC['openid'];
}
$member = m('member')->getMember($openid);
$commission = p('commission')->getSet();
$uniacid = $_W['uniacid'];
$orderid = intval($_GPC['orderid']);
if (($operation == 'display') && $_W['isajax']) 
{
	if (empty($orderid)) 
	{
		show_json(0, '参数错误!');
	}
	$order = pdo_fetch('select * from ' . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));
	$store = pdo_fetch('select * from ' . tablename('sz_yi_cashier_order') . ' o inner join ' . tablename('sz_yi_cashier_store') . ' s on o.cashier_store_id = s.id where o.order_id=:orderid and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));
	$couponurl = '';
	if (p('coupon') && (0 < $store['coupon_id'])) 
	{
		$couponurl = $this->createPluginMobileUrl('coupon/detail', array('id' => $store['coupon_id']));
	}
	else 
	{
		$couponUrl = $this->createMobileUrl('member');
	}
	if (empty($order)) 
	{
		show_json(0, '订单未找到!');
	}
	if ($order['status'] == -1) 
	{
		show_json(-1, '订单已关闭, 无法付款!');
	}
	else if (1 <= $order['status']) 
	{
		show_json(-1, '订单已付款, 无需重复支付!');
	}
	$log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(':uniacid' => $uniacid, ':module' => 'sz_yi', ':tid' => $order['ordersn']));
	if (!empty($log) && ($log['status'] != '0')) 
	{
		show_json(-1, '订单已支付, 无需重复支付!');
	}
	if (!empty($log) && ($log['status'] == '0')) 
	{
		pdo_delete('core_paylog', array('plid' => $log['plid']));
		$log = NULL;
	}
	$plid = $log['plid'];
	if (empty($log)) 
	{
		$log = array('uniacid' => $uniacid, 'openid' => $member['uid'], 'module' => 'sz_yi', 'tid' => $order['ordersn'], 'fee' => $order['price'], 'status' => 0);
		pdo_insert('core_paylog', $log);
		$plid = pdo_insertid();
	}
	$set = m('common')->getSysset(array('shop', 'pay'));
	$credit = array('success' => false);
	$currentcredit = 0;
	if (isset($set['pay']) && ($set['pay']['credit'] == 1)) 
	{
		if ($order['deductcredit2'] <= 0) 
		{
			$credit = array('success' => true, 'current' => m('member')->getCredit($openid, 'credit2'));
		}
	}
	load()->model('payment');
	$setting = uni_setting($_W['uniacid'], array('payment'));
	$wechat = array('success' => false);
	if (is_weixin()) 
	{
		if (isset($set['pay']) && ($set['pay']['weixin'] == 1)) 
		{
			if (is_array($setting['payment']['wechat']) && $setting['payment']['wechat']['switch']) 
			{
				$wechat['success'] = true;
			}
		}
	}
	$alipay = array('success' => false);
	if (isset($set['pay']) && ($set['pay']['alipay'] == 1)) 
	{
		if (is_array($setting['payment']['alipay']) && $setting['payment']['alipay']['switch']) 
		{
			$alipay['success'] = true;
		}
	}
	$pluginy = p('yunpay');
	$yunpay = array('success' => false);
	if ($pluginy) 
	{
		$yunpayinfo = $pluginy->getYunpay();
		if (isset($yunpayinfo) && $yunpayinfo['switch']) 
		{
			$yunpay['success'] = true;
		}
	}
	$unionpay = array('success' => false);
	if (isset($set['pay']) && ($set['pay']['unionpay'] == 1)) 
	{
		if (is_array($setting['payment']['unionpay']) && $setting['payment']['unionpay']['switch']) 
		{
			$unionpay['success'] = true;
		}
	}
	$returnurl = urlencode($this->createPluginMobileUrl('cashier/order_pay', array('orderid' => $orderid)));
	show_json(1, array('order' => $order, 'set' => $set, 'credit' => $credit, 'wechat' => $wechat, 'alipay' => $alipay, 'unionpay' => $unionpay, 'yunpay' => $yunpay, 'cash' => $cash, 'isweixin' => is_weixin(), 'currentcredit' => $currentcredit, 'returnurl' => $returnurl, 'couponurl' => $couponurl));
}
else if (($operation == 'pay') && $_W['ispost']) 
{
	$set = m('common')->getSysset(array('shop', 'pay'));
	$order = pdo_fetch('select * from ' . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));
	if (empty($order)) 
	{
		show_json(0, '订单未找到!');
	}
	$type = $_GPC['type'];
	if (!in_array($type, array('weixin', 'alipay', 'unionpay', 'yunpay'))) 
	{
		show_json(0, '未找到支付方式');
	}
	if (($member['credit2'] < $order['deductcredit2']) && (0 < $order['deductcredit2'])) 
	{
		show_json(0, '余额不足，请充值后在试！');
	}
	$log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(':uniacid' => $uniacid, ':module' => 'sz_yi', ':tid' => $order['ordersn']));
	if (empty($log)) 
	{
		show_json(0, '支付出错,请重试!');
	}
	$plid = $log['plid'];
	$param_title = $set['shop']['name'] . '订单: ' . $order['ordersn'];
	if ($type == 'weixin') 
	{
		if (!is_weixin()) 
		{
			show_json(0, '非微信环境!');
		}
		if (empty($set['pay']['weixin'])) 
		{
			show_json(0, '未开启微信支付!');
		}
		$wechat = array('success' => false);
		$params = array();
		$params['tid'] = $log['tid'];
		if (!empty($order['ordersn2'])) 
		{
			$var = sprintf('%02d', $order['ordersn2']);
			$params['tid'] .= 'GJ' . $var;
		}
		$params['user'] = $openid;
		$params['fee'] = $order['price'];
		$params['title'] = $param_title;
		load()->model('payment');
		$setting = uni_setting($_W['uniacid'], array('payment'));
		if (is_array($setting['payment'])) 
		{
			$options = $setting['payment']['wechat'];
			$options['appid'] = $_W['account']['key'];
			$options['secret'] = $_W['account']['secret'];
			$wechat = m('common')->wechat_build($params, $options, 0);
			$wechat['success'] = false;
			if (!is_error($wechat)) 
			{
				$wechat['success'] = true;
			}
			else 
			{
				show_json(0, $wechat['message']);
			}
		}
		if (!$wechat['success']) 
		{
			show_json(0, '微信支付参数错误!');
		}
		pdo_update('sz_yi_order', array('paytype' => 21), array('id' => $order['id']));
		if ($commission['become_child'] == 2) 
		{
			p('commission')->checkOrderPay($orderid);
		}
		show_json(1, array('wechat' => $wechat));
	}
	else if ($type == 'alipay') 
	{
		pdo_update('sz_yi_order', array('paytype' => 22), array('id' => $order['id']));
		if ($commission['become_child'] == 2) 
		{
			p('commission')->checkOrderPay($orderid);
		}
		show_json(1);
	}
	else if ($type == 'yunpay') 
	{
		pdo_update('sz_yi_order', array('paytype' => 24), array('id' => $order['id']));
		if ($commission['become_child'] == 2) 
		{
			p('commission')->checkOrderPay($orderid);
		}
		show_json(1);
	}
}
else if (($operation == 'complete') && $_W['ispost']) 
{
	$order = pdo_fetch('select * from ' . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));
	$store = pdo_fetch('select * from ' . tablename('sz_yi_cashier_order') . ' o inner join ' . tablename('sz_yi_cashier_store') . ' s on o.cashier_store_id = s.id where o.order_id=:orderid and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));
	if (p('coupon') && (0 < $store['coupon_id'])) 
	{
		$couponUrl = $this->createPluginMobileUrl('coupon/detail', array('id' => $store['coupon_id']));
	}
	else 
	{
		$couponUrl = $this->createMobileUrl('member');
	}
	if (empty($order)) 
	{
		show_json(0, '订单未找到!');
	}
	$type = $_GPC['type'];
	if (!in_array($type, array('weixin', 'alipay', 'credit'))) 
	{
		show_json(0, '未找到支付方式');
	}
	if (($member['credit2'] < $order['deductcredit2']) && (0 < $order['deductcredit2'])) 
	{
		show_json(0, '余额不足，请充值后在试！');
	}
	$log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(':uniacid' => $uniacid, ':module' => 'sz_yi', ':tid' => $order['ordersn']));
	if (empty($log)) 
	{
		show_json(0, '支付出错,请重试!');
	}
	$plid = $log['plid'];
	$ps = array();
	$ps['tid'] = $log['tid'];
	$ps['user'] = $openid;
	$ps['fee'] = $log['fee'];
	$ps['title'] = $log['title'];
	if ($type == 'credit') 
	{
		$credits = m('member')->getCredit($openid, 'credit2');
		if ($credits < $ps['fee']) 
		{
			show_json(0, '余额不足,请充值');
		}
		$fee = floatval($ps['fee']);
		$result = m('member')->setCredit($openid, 'credit2', -$fee, array($_W['member']['uid'], '消费' . $setting['creditbehaviors']['currency'] . ':' . $fee));
		if (is_error($result)) 
		{
			show_json(0, $result['message']);
		}
		$record = array();
		$record['status'] = '1';
		$record['type'] = 'cash';
		pdo_update('core_paylog', $record, array('plid' => $log['plid']));
		pdo_update('sz_yi_order', array('paytype' => 1), array('id' => $order['id']));
		$ret = array();
		$ret['result'] = 'success';
		$ret['type'] = $log['type'];
		$ret['from'] = 'return';
		$ret['tid'] = $log['tid'];
		$ret['user'] = $log['openid'];
		$ret['fee'] = $log['fee'];
		$ret['weid'] = $log['weid'];
		$ret['uniacid'] = $log['uniacid'];
		$pay_result = $this->model->payResult($ret);
		$pay_result['couponurl'] = $couponUrl;
		$pay_result['order'] = $order;
		if ($commission['become_child'] == 2) 
		{
			p('commission')->checkOrderPay($orderid);
		}
		$this->model->redpack($openid, $orderid);
		$this->model->setCredits($orderid);
		$this->model->setCredits2($orderid);
		show_json(1, $pay_result);
	}
	else if ($type == 'weixin') 
	{
		$ordersn = $order['ordersn'];
		if (!empty($order['ordersn2'])) 
		{
			$ordersn .= 'GJ' . sprintf('%02d', $order['ordersn2']);
		}
		$payquery = m('finance')->isWeixinPay($ordersn);
		if (!is_error($payquery)) 
		{
			$record = array();
			$record['status'] = '1';
			$record['type'] = 'wechat';
			pdo_update('core_paylog', $record, array('plid' => $log['plid']));
			$ret = array();
			$ret['result'] = 'success';
			$ret['type'] = 'wechat';
			$ret['from'] = 'return';
			$ret['tid'] = $log['tid'];
			$ret['user'] = $log['openid'];
			$ret['fee'] = $log['fee'];
			$ret['weid'] = $log['weid'];
			$ret['uniacid'] = $log['uniacid'];
			$ret['deduct'] = intval($_GPC['deduct']) == 1;
			$pay_result = $this->model->payResult($ret);
			if ($commission['become_child'] == 2) 
			{
				p('commission')->checkOrderPay($orderid);
			}
			$this->model->redpack($openid, $orderid);
			$this->model->setCredits($orderid);
			$this->model->setCredits2($orderid);
			$pay_result['couponurl'] = $couponUrl;
			$pay_result['order'] = $order;
			show_json(1, $pay_result);
		}
		show_json(0, '支付出错,请重试!');
	}
}
else if ($operation == 'return') 
{
	$tid = $_GPC['out_trade_no'];
	if (!m('finance')->isAlipayNotify($_GET)) 
	{
		exit('支付出现错误，请重试!');
	}
	$log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(':uniacid' => $uniacid, ':module' => 'sz_yi', ':tid' => $tid));
	if (empty($log)) 
	{
		exit('支付出现错误，请重试!');
	}
	if ($log['status'] != 1) 
	{
		$record = array();
		$record['status'] = '1';
		$record['type'] = 'alipay';
		pdo_update('core_paylog', $record, array('plid' => $log['plid']));
		$ret = array();
		$ret['result'] = 'success';
		$ret['type'] = 'alipay';
		$ret['from'] = 'return';
		$ret['tid'] = $log['tid'];
		$ret['user'] = $log['openid'];
		$ret['fee'] = $log['fee'];
		$ret['weid'] = $log['weid'];
		$ret['uniacid'] = $log['uniacid'];
		$this->model->payResult($ret);
	}
	$orderid = pdo_fetchcolumn('select id from ' . tablename('sz_yi_order') . ' where ordersn=:ordersn and uniacid=:uniacid', array(':ordersn' => $log['tid'], ':uniacid' => $_W['uniacid']));
	$store = pdo_fetch('select * from ' . tablename('sz_yi_cashier_order') . ' o inner join ' . tablename('sz_yi_cashier_store') . ' s on o.cashier_store_id = s.id where o.order_id=:orderid and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));
	if (p('coupon') && (0 < $store['coupon_id'])) 
	{
		$url = $this->createPluginMobileUrl('coupon/detail', array('id' => $store['coupon_id']));
	}
	else 
	{
		$url = $this->createMobileUrl('member');
	}
	exit('<script>top.window.location.href=\'' . $url . '\'</script>');
}
else if ($operation == 'returnyunpay') 
{
	$tids = $_REQUEST['i2'];
	$strs = explode(':', $tids);
	$tid = $strs[0];
	$pluginy = p('yunpay');
	if (!$pluginy->isYunpayNotify($_GET)) 
	{
		exit('支付出现错误，请重试!');
	}
	$log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(':uniacid' => $uniacid, ':module' => 'sz_yi', ':tid' => $tid));
	if (empty($log)) 
	{
		exit('支付出现错误，请重试!');
	}
	if ($log['status'] != 1) 
	{
		$record = array();
		$record['status'] = '1';
		$record['type'] = 'yunpay';
		pdo_update('core_paylog', $record, array('plid' => $log['plid']));
		$ret = array();
		$ret['result'] = 'success';
		$ret['type'] = 'yunpay';
		$ret['from'] = 'return';
		$ret['tid'] = $log['tid'];
		$ret['user'] = $log['openid'];
		$ret['fee'] = $log['fee'];
		$ret['weid'] = $log['weid'];
		$ret['uniacid'] = $log['uniacid'];
		$this->model->payResult($ret);
	}
	$orderid = pdo_fetchcolumn('select id from ' . tablename('sz_yi_order') . ' where ordersn=:ordersn and uniacid=:uniacid', array(':ordersn' => $log['tid'], ':uniacid' => $_W['uniacid']));
	$store = pdo_fetch('select * from ' . tablename('sz_yi_cashier_order') . ' o inner join ' . tablename('sz_yi_cashier_store') . ' s on o.cashier_store_id = s.id where o.order_id=:orderid and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));
	if (p('coupon') && (0 < $store['coupon_id'])) 
	{
		$url = $this->createPluginMobileUrl('coupon/detail', array('id' => $store['coupon_id']));
	}
	else 
	{
		$url = $this->createMobileUrl('member');
	}
	exit('<script>top.window.location.href=\'' . $url . '\'</script>');
}
if ($operation == 'display') 
{
	include $this->template('cashier/order_pay');
}
?>