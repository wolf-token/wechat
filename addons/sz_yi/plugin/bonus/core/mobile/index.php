<?php
global $_W;
global $_GPC;
$openid = m('user')->getOpenid();
$level = $this->model->getLevel($openid);
$set = $this->getSet();
$member = $this->model->getInfo($openid, array('total', 'ordercount', 'ordercount_area', 'ok'));
$cansettle = (0 < $member['commission_ok']) && (floatval($this->set['withdraw']) <= $member['commission_ok']);
$commission_ok = $member['commission_ok'];
$member['nickname'] = (empty($member['nickname']) ? $member['mobile'] : $member['nickname']);
$member['ordercount0'] = number_format($member['ordercount'], 0);
$member['commission_ok'] = number_format($member['commission_ok'], 2);
$member['commission_pay'] = number_format($member['commission_pay'], 2);
$member['commission_total'] = number_format($member['commission_total'], 2);
$member['customercount'] = intval($member['agentcount']);
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
include $this->template('index');
?>