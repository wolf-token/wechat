<?php
global $_W;
global $_GPC;
$openid = m('user')->getOpenid();
include $this->template('register');
?>