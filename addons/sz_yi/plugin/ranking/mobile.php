<?php
if (!defined('IN_IA')) 
{
	exit('Access Denied');
}
class RankingMobile extends Plugin 
{
	protected $set;
	public function __construct() 
	{
		parent::__construct('ranking');
		$this->set = $this->getSet();
		global $_GPC;
	}
	public function ranking() 
	{
		$this->_exec_plugin('ranking', false);
	}
	public function commission() 
	{
		$this->_exec_plugin('commission', false);
	}
}
?>