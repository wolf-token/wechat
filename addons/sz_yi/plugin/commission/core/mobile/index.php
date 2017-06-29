<?php
global $_W;
global $_GPC;
$openid = m('user')->getOpenid();
$pluginbonus = p('bonus');
$bonus = 0;
$level = $this->model->getLevel($openid);
$total_all = 0;
if (!empty($pluginbonus)) 
{
	$bonus_set = $pluginbonus->getSet();
	$islevel = $pluginbonus->isLevel($openid);
	if ((!empty($bonus_set['start']) || !empty($bonus_set['area_start'])) && !empty($islevel)) 
	{
		if ($bonus_set['bonushow'] == 1) 
		{
			$bonus = 1;
			$member_bonus = $pluginbonus->getInfo($openid, array('total', 'ordercount', 'ordercount_area', 'ok'));
			$bonus_cansettle = (0 < $member_bonus['commission_ok']) && (floatval($bonus['withdraw']) <= $member_bonus['commission_ok']);
			$total_all = $member_bonus['commission_total'];
			$member_bonus['nickname'] = (empty($member_bonus['nickname']) ? $member_bonus['mobile'] : $member_bonus['nickname']);
			$member_bonus['ordercount0'] = intval($member_bonus['ordercount']);
			$member_bonus['ordercount_area0'] = intval($member_bonus['ordercount_area']);
			$member_bonus['commission_ok'] = number_format($member_bonus['commission_ok'], 2);
			$member_bonus['commission_pay'] = number_format($member_bonus['commission_pay'], 2);
			$member_bonus['commission_total'] = number_format($member_bonus['commission_total'], 2);
			$member_bonus['customercount'] = intval($member_bonus['agentcount']);
			$level = $pluginbonus->getLevel($openid);
			$areaname = '';
			if ($member_bonus['bonus_area'] != 0) 
			{
				if ($member_bonus['bonus_area'] == 1) 
				{
					$areaname = $bonus_set['texts']['agent_province'];
				}
				else if ($member_bonus['bonus_area'] == 2) 
				{
					$areaname = $bonus_set['texts']['agent_city'];
				}
				else if ($member_bonus['bonus_area'] == 3) 
				{
					$areaname = $bonus_set['texts']['agent_district'];
				}
			}
			if (!empty($level)) 
			{
				if (!empty($areaname)) 
				{
					$level['levelname'] = $level['levelname'] . '][' . $areaname;
				}
			}
			else if (!empty($areaname)) 
			{
				$level['levelname'] = $areaname;
			}
		}
	}
}
$member = $this->model->getInfo($openid, array('total', 'ordercount0', 'ok', 'myorder'));
if ($_W['isajax']) 
{
	$cansettle = (0 < $member['commission_ok']) && (floatval($this->set['withdraw']) <= $member['commission_ok']);
	$mycansettle = (0 < $member['commission_total']) && (floatval($this->set['consume_withdraw']) <= $member['myoedermoney']);
	$commission_ok = $member['commission_ok'];
	$member['nickname'] = (empty($member['nickname']) ? $member['mobile'] : $member['nickname']);
	$total_all += $member['commission_total'];
	$member['agentcount'] = number_format($member['agentcount'], 0);
	$member['ordercount0'] = number_format($member['ordercount0'], 0);
	$member['commission_ok'] = number_format($member['commission_ok'], 2);
	$member['commission_pay'] = number_format($member['commission_pay'], 2);
	$member['commission_total'] = number_format($member['commission_total'], 2);
	$member['total_all'] = number_format($total_all, 2);
	$member['customercount'] = pdo_fetchcolumn('select count(id) from ' . tablename('sz_yi_member') . ' where agentid=:agentid and ((isagent=1 and status=0) or isagent=0) and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
	if (6 < mb_strlen($member['nickname'], 'utf-8')) 
	{
		$member['nickname'] = mb_substr($member['nickname'], 0, 6, 'utf-8');
	}
	$openselect = false;
	if ($this->set['select_goods'] == '1') 
	{
		if (empty($member['agentselectgoods']) || ($member['agentselectgoods'] == 2)) 
		{
			$openselect = true;
		}
	}
	else if ($member['agentselectgoods'] == 2) 
	{
		$openselect = true;
	}
	$this->set['openselect'] = $openselect;
	$orders = array();
	$level1 = $member['level1'];
	$level2 = $member['level2'];
	$level3 = $member['level3'];
	$levels = intval($this->set['level']);
	$pricecount = 0;
	if (1 <= $levels) 
	{
		$level1_memberids = pdo_fetchall('select id from ' . tablename('sz_yi_member') . ' where uniacid=:uniacid and agentid=:agentid', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']), 'id');
		$level1_orders = pdo_fetchall('select commission1,o.id,o.createtime,o.price,og.commissions from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on og.orderid=o.id ' . ' where o.uniacid=:uniacid and o.agentid=:agentid ' . $condition . ' and og.status1>=0 and og.nocommission=0', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
		foreach ($level1_orders as $o ) 
		{
			if (empty($o['id'])) 
			{
				continue;
			}
			$orders[] = array('id' => $o['id'], 'price' => $o['price'], 'createtime' => $o['createtime'], 'level' => 1);
			$pricecount += $o['price'];
		}
	}
	if (2 <= $levels) 
	{
		if (0 < $level1) 
		{
			$level2_orders = pdo_fetchall('select commission2 ,o.id,o.createtime,o.price,og.commissions   from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on og.orderid=o.id ' . ' where o.uniacid=:uniacid and o.agentid in( ' . implode(',', array_keys($member['level1_agentids'])) . ')  ' . $condition . '  and og.status2>=0 and og.nocommission=0 ', array(':uniacid' => $_W['uniacid']));
			foreach ($level2_orders as $o ) 
			{
				if (empty($o['id'])) 
				{
					continue;
				}
				$orders[] = array('id' => $o['id'], 'price' => $o['price'], 'createtime' => $o['createtime'], 'level' => 2);
				$pricecount += $o['price'];
			}
		}
	}
	if (3 <= $levels) 
	{
		if (0 < $level2) 
		{
			$level3_orders = pdo_fetchall('select commission3 ,o.id,o.createtime,o.price,og.commissions  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join  ' . tablename('sz_yi_order') . ' o on og.orderid=o.id ' . ' where o.uniacid=:uniacid and o.agentid in( ' . implode(',', array_keys($member['level2_agentids'])) . ')  ' . $condition . ' and og.status3>=0 and og.nocommission=0', array(':uniacid' => $_W['uniacid']));
			foreach ($level3_orders as $o ) 
			{
				if (empty($o['id'])) 
				{
					continue;
				}
				$orders[] = array('id' => $o['id'], 'price' => $o['price'], 'createtime' => $o['createtime'], 'level' => 3);
				$pricecount += $o['price'];
			}
		}
	}
	show_json(1, array('commission_ok' => $commission_ok, 'pricecount' => $pricecount, 'member' => $member, 'level' => $level, 'cansettle' => $cansettle, 'mycansettle' => $mycansettle, 'settlemoney' => number_format(floatval($this->set['withdraw']), 2), 'mysettlemoney' => number_format(floatval($this->set['consume_withdraw']), 2), 'set' => $this->set));
}
$plugin_article = p('article');
if ($plugin_article) 
{
	$article_set = $plugin_article->getSys();
	$article_text = (($article_set['article_text'] ? $article_set['article_text'] : '文章管理'));
	$article_title = (($article_set['article_title'] ? $article_set['article_title'] : '进入文章列表'));
	$isarticle = $article_set['isarticle'];
}
include $this->template('index');
?>