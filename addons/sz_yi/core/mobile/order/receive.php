<?php
global $_W;
global $_GPC;
$sets = pdo_fetchall('select uniacid from ' . tablename('sz_yi_sysset'));
foreach ($sets as $set ) 
{
	$_W['uniacid'] = $set['uniacid'];
	if (empty($_W['uniacid'])) 
	{
		continue;
	}
	$trade = m('common')->getSysset('trade', $_W['uniacid']);
	$days = intval($trade['receive']);
	if ($days <= 0) 
	{
		continue;
	}
	$daytimes = 86400 * $days;
	$p = p('commission');
	$pcoupon = p('coupon');
	$orders = pdo_fetchall('select id,couponid from ' . tablename('sz_yi_order') . ' where uniacid=' . $_W['uniacid'] . ' and status=2 and sendtime + ' . $daytimes . ' <=unix_timestamp() ', array(), 'id');
	if (!empty($orders)) 
	{
		$orderkeys = array_keys($orders);
		$orderids = implode(',', $orderkeys);
		if (!empty($orderids)) 
		{
			pdo_query('update ' . tablename('sz_yi_order') . ' set status=3,finishtime=' . time() . ' where id in (' . $orderids . ')');
			foreach ($orders as $orderid => $o ) 
			{
				m('notice')->sendOrderMessage($orderid);
				if ($pcoupon) 
				{
					if (!empty($o['couponid'])) 
					{
						$pcoupon->backConsumeCoupon($o['id']);
					}
				}
				if ($p) 
				{
					$p->checkOrderFinish($orderid);
				}
			}
		}
	}
}
$pbonus = p('bonus');
if (!empty($pbonus)) 
{
	foreach ($sets as $set ) 
	{
		$_W['uniacid'] = $set['uniacid'];
		if (empty($_W['uniacid'])) 
		{
			continue;
		}
		$daytime = strtotime(date('Y-m-d 00:00:00'));
		$isbonus = false;
		$bonus_set = $pbonus->getSet();
		if (empty($bonus_set['sendmethod'])) 
		{
			continue;
		}
		if ($bonus_set['sendmonth'] == 1) 
		{
			$monthtime = strtotime(date('Y-m-1 00:00:00'));
			$bonus_data = pdo_fetch('select id from ' . tablename('sz_yi_bonus') . ' where ctime>' . $monthtime . ' and isglobal=0 and uniacid=' . $_W['uniacid'] . '  order by id desc');
			$bonus_data_isglobal = pdo_fetch('select id from ' . tablename('sz_yi_bonus') . ' where ctime>' . $monthtime . ' and isglobal=1 and uniacid=' . $_W['uniacid'] . '  order by id desc');
		}
		else 
		{
			$bonus_data = pdo_fetch('select * from ' . tablename('sz_yi_bonus') . ' where ctime>' . $daytime . ' and isglobal=0 and uniacid=' . $_W['uniacid'] . '  order by id desc');
			$bonus_data_isglobal = pdo_fetch('select * from ' . tablename('sz_yi_bonus') . ' where ctime>' . $daytime . ' and isglobal=1 and uniacid=' . $_W['uniacid'] . '  order by id desc');
		}
		if (!empty($bonus_set['start'])) 
		{
			if (empty($bonus_data)) 
			{
				$pbonus->autosend();
			}
			if (empty($bonus_data_isglobal)) 
			{
				$pbonus->autosendall();
			}
		}
	}
}
echo 'ok...';
?>