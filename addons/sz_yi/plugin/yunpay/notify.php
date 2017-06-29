<?php
error_reporting(0);
define('IN_MOBILE', true);
if (!empty($_POST)) {
	require '../../../../framework/bootstrap.inc.php';
	require '../../../../addons/sz_yi/defines.php';
	require '../../../../addons/sz_yi/core/inc/functions.php';
	require '../../../../addons/sz_yi/core/inc/plugin/plugin_model.php';
	$body          = $_REQUEST['i2'];
	$strs          = explode(':', $body);
	$out_trade_no  = $strs[0];
	$_W['uniacid'] = $_W['weid'] = intval($strs[1]);
	$type          = intval($strs[2]);
	if ($type == 0) {
		$paylog = '
-------------------------------------------------
';
		$paylog .= 'orderno: ' . $out_trade_no . '
';
		$paylog .= 'paytype: alipay
';
		$paylog .= 'data: ' . json_encode($_POST) . '
';
		m('common')->paylog($paylog);
	}
	$pluginy = p('yunpay');
	if ($pluginy) {
		$yunpayinfo = $pluginy->getYunpay();
		if (!isset($yunpayinfo) or !$yunpayinfo['switch']) {
			exit('fail');
		}
	}
	m('common')->paylog('setting: ok
');
	$prestr = $_REQUEST['i1'] . $_REQUEST['i2'] . $yunpayinfo['partner'] . $yunpayinfo['secret'];
	$mysgin = md5($prestr);
	if ($mysgin == $_REQUEST['i3']) {
		m('common')->paylog('sign: ok
');
		if (empty($type)) {
			$tid = $out_trade_no;
			if (strexists($tid, 'GJ')) {
				$tids = explode('GJ', $tid);
				$tid  = $tids[0];
			}
			$sql               = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `tid`=:tid and `module`=:module limit 1';
			$params            = array();
			$params[':tid']    = $tid;
			$params[':module'] = 'sz_yi';
			$log               = pdo_fetch($sql, $params);
			m('common')->paylog('log: ' . (empty($log) ? '' : json_encode($log)) . '
');
			if (!empty($log) && $log['status'] == '0') {
				m('common')->paylog('corelog: ok
');
				$site = WeUtility::createModuleSite($log['module']);
				if (!is_error($site)) {
					$method = 'payResult';
					if (method_exists($site, $method)) {
						$ret               = array();
						$ret['weid']       = $log['weid'];
						$ret['uniacid']    = $log['uniacid'];
						$ret['result']     = 'success';
						$ret['type']       = $log['type'];
						$ret['from']       = 'return';
						$ret['tid']        = $log['tid'];
						$ret['user']       = $log['openid'];
						$ret['fee']        = $log['fee'];
						$ret['is_usecard'] = $log['is_usecard'];
						$ret['card_type']  = $log['card_type'];
						$ret['card_fee']   = $log['card_fee'];
						$ret['card_id']    = $log['card_id'];
						m('common')->paylog('method: execute
');
						$result = $site->$method($ret);
						if (is_array($result) && $result['result'] == 'success') {
							$record           = array();
							$record['status'] = '1';
							pdo_update('core_paylog', $record, array(
								'plid' => $log['plid']
							));
							exit('success');
						}
					} else {
						m('common')->paylog('method not found!
');
					}
				} else {
					m('common')->paylog('error: ' . json_encode($site) . '
');
				}
			}
		} else if ($type == 1) {
			$logno = trim($out_trade_no);
			if (empty($logno)) {
				exit;
			}
			$log = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_member_log') . ' WHERE `uniacid`=:uniacid and `logno`=:logno limit 1', array(
				':uniacid' => $_W['uniacid'],
				':logno' => $logno
			));
			if (!empty($log) && empty($log['status'])) {
				pdo_update('sz_yi_member_log', array(
					'status' => 1,
					'rechargetype' => 'yunpay'
				), array(
					'id' => $log['id']
				));
				m('member')->setCredit($log['openid'], 'credit2', $log['money'], array(
					0,
					'芸众商城会员充值:credit2:' . $log['money']
				));
				m('member')->setRechargeCredit($log['openid'], $log['money']);
				if (p('sale')) {
					p('sale')->setRechargeActivity($log);
				}
				m('notice')->sendMemberLogMessage($log['id']);
			}
		}
	}
}
exit('fail');
?>