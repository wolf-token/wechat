<?php 

    global $_W,$_GPC;
    $uniacid = $_W['uniacid'];
    $action = 'login';
    $url = $this->createWebUrl($action, array('op' => 'check'));
    //判断是否为空
    $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'check';

    if ($operation == "check") {
        
        //查找数据
        $res = pdo_fetchall("SELECT * FROM ".tablename("move_declare")." WHERE status=0 OR status=2 OR status=5 ");

        //判断是否有数据
        if (!empty($res)) {
           
           echo "yes";
        }else{

            echo "no";
        }
    }


 ?>