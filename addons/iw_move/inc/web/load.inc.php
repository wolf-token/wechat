<?php 

    global $_W,$_GPC;
    $action = 'test';
    $uniacid = $_W['uniacid'];
    
    $url = $this->createWebUrl($action, array('op' => 'add'));
    //判断是否为空
    $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'add';

if ($operation == "list") {
      
      //引入文件
        require_once "excel.php";
        //判断是否传入数据
        if ($_W['ispost']) {
           
           $where = '';
           $params = array();
           //判断导出数据类型
           if (isset($_GPC['type']) && !empty($_GPC['type'])) {
              
              $where.=" WHERE status=".$_GPC['type'];
           }
           //判断导出数据的时间
            if ($_GPC['all'] == 1) {
                
                // var_dump($_POST);die();
                $start = strtotime($_GPC['datelimit']['start']) ;
                $end = strtotime($_GPC['datelimit']['end']) ;
                $where.=" AND time>=".$start." AND time<=".$end ;

            }

           //查找数据
           $staff = array();
           $user = array();
            $list = pdo_fetchall("SELECT * FROM ".tablename('move_recommend').$where);
            //查找相关的信息
            foreach ($list as $key => $value) {
                $list[$key]['time'] = date("Y-m-d H:i:s",$value['time']);
                //判断来源信息
                if ($value['come'] == 0) {
                    $list[$key]['type'] = "员工推荐";
                    //员工推荐
                    $user = pdo_fetch("SELECT name,phone FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$value['pid']));
                    $list[$key]['username'] = $user['name'];
                    $list[$key]['userphone'] = $user['phone'];
                }else if ($value['come'] == 1) {
                    $list[$key]['type'] = "客户推荐";
                    //客户推荐
                    $user = pdo_fetch("SELECT name,phone FROM ".tablename("move_customer")." WHERE id=:id",array("id"=>$value['pid']));
                     $list[$key]['username'] = $user['name'];
                     $list[$key]['userphone'] = $user['phone'];
                }else{
                    $list[$key]['type'] = "后台添加";
                    //后台添加
                    // $user = array();
                }
                //判断会否分配
                if ($value['status'] == 1) {
                    
                    //查找分配人的信息
                    $staff = pdo_fetch("SELECT name,tid,phone,team FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$value['allot']));
                     $list[$key]['staffname'] = $staff['name'];
                     $list[$key]['staffphone'] = $staff['phone'];
                     $list[$key]['stafftid'] = $staff['tid'];
                     $list[$key]['staffteam'] = $staff['team'];
                     // $list[$key]['staffphone'] = $staff['phone'];
                }
            }

        // var_dump($list);die();
        if (empty($list)) {
            message('没有数据可以导出，请重新选择时间!', '', 'info');
        }
     
        $columns = array(
            array(
                'title' => '序号',
                'field' => 'id',
                'width' => 12
            ),
            array(
                'title' => '客户姓名',
                'field' => 'name',
                'width' => 20
            ),
            array(
                'title' => '客户地址',
                'field' => 'address',
                'width' => 40
            ),
            array(
                'title' => '客户手机号',
                'field' => 'phone',
                'width' => 20
            ),
            array(
                'title' => '业务描述',
                'field' => 'content',
                'width' => 50
            ),
            array(
                'title' => '推荐时间',
                'field' => 'time',
                'width' => 30
            ),
            array(
                'title' => '业务来源',
                'field' => 'type',
                'width' => 24
            ),
            array(
                'title' => '推荐人姓名',
                'field' => "username",
                'width' => 50
            ),
            array(
                'title' => '推荐人电话',
                'field' => "userphone",
                'width' => 50
            ),
            array(
                'title' => '分配维修工姓名',
                'field' => "staffname",
                'width' => 50
            ),
            array(
                'title' => '分配维修工班组ID',
                'field' => "stafftid",
                'width' => 50
            ),
            array(
                'title' => '分配维修工手机号',
                'field' => "staffphone",
                'width' => 50
            ),
            array(
                'title' => '分配维修工班组名称',
                'field' => "staffteam",
                'width' => 50
            )
        );
      
        $model = New Sz_DYi_Excel();
        $model->export($list, array(
            "title" => "推荐业务信息",
            "columns" => $columns
        ));
        exit;
        }
        

   }else if ($operation == "down") {
      
           //引入文件
        require_once "excel.php";
        //判断是否传入数据
        if ($_W['ispost']) {
           
           $where1 = '';
           // $params = array();
           //判断导出数据类型
           if (isset($_GPC['appraise']) && !empty($_GPC['appraise'])) {
              
              $where1.=" WHERE status=3 AND appraise=".$_GPC['appraise'];
           }
           //判断导出数据的时间
            if ($_GPC['all'] == 1) {
                
                // var_dump($_POST);die();
                $start1 = strtotime($_GPC['datelimit']['start']) ;
                $end1 = strtotime($_GPC['datelimit']['end']) ;
                $where1.=" AND time>=".$start1." AND time<=".$end1 ;

            }

           //查找数据
           $staffa = array();
           $usera = array();
            $list1 = pdo_fetchall("SELECT * FROM ".tablename('move_declare').$where1);
            //查找相关的信息
            foreach ($list1 as $ke => $val) {
                $list1[$ke]['time'] = date("Y-m-d H:i:s",$val['time']);
                //判断报装类型
                if ($val['type'] == 0) {
                	$list1[$ke]['type'] = "报装";
                }else{
                	$list1[$ke]['type'] = "报修";
                }
                 if ($val['appraise'] == 0) {
                	$list1[$ke]['appraisea'] = "未平价";
                }else{
                	$list1[$ke]['appraisea'] = "已评价";
                }
               
                //判断来源信息
                if ($val['come'] == 0) {
                    $list1[$ke]['typea'] = "员工申报";
                    //员工推荐
                    $usera = pdo_fetch("SELECT name,phone FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$val['uid']));
                    $list1[$ke]['username'] = $usera['name'];
                    $list1[$ke]['userphone'] = $usera['phone'];
                }else if ($val['come'] == 1) {
                    $list1[$ke]['typea'] = "客户申报";
                    //客户推荐
                    $usera = pdo_fetch("SELECT name,phone FROM ".tablename("move_customer")." WHERE id=:id",array("id"=>$val['uid']));
                     $list1[$ke]['username'] = $usera['name'];
                     $list1[$ke]['userphone'] = $usera['phone'];
                }else{
                    $list1[$ke]['typea'] = "后台添加";
                    //后台添加
                    // $user = array();
                }
                //判断会否分配
                if ($val['status'] != 0 || $val['status'] != 2) {
                    
                    //查找分配人的信息
                    $staffa = pdo_fetch("SELECT name,tid,phone,team FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$val['pid']));
                     $list1[$ke]['staffname'] = $staffa['name'];
                     $list1[$ke]['staffphone'] = $staffa['phone'];
                     $list1[$ke]['stafftid'] = $staffa['tid'];
                     $list1[$ke]['staffteam'] = $staffa['team'];
                     // $list1[$ke]['staffphone'] = $staff['phone'];
                }
            }

        // var_dump($list1);die();
        if (empty($list1)) {
            message('没有数据可以导出，请重新选择时间!', '', 'info');
        }
     
        $columns1 = array(
            array(
                'title' => '序号',
                'field' => 'id',
                'width' => 12
            ),
            array(
                'title' => '客户姓名',
                'field' => 'name',
                'width' => 20
            ),
            array(
                'title' => '客户所在小区',
                'field' => 'plat',
                'width' => 40
            ),
            array(
                'title' => '客户手机号',
                'field' => 'phone',
                'width' => 20
            ),
            array(
                'title' => '详情描述',
                'field' => 'details',
                'width' => 50
            ),
            array(
                'title' => '报装故障类型',
                'field' => 'type',
                'width' => 12
            ),
            array(
                'title' => '评价状态',
                'field' => 'appraisea',
                'width' => 12
            ),
            array(
                'title' => '评价星级',
                'field' => 'star',
                'width' => 12
            ),
            array(
                'title' => '申报来源',
                'field' => 'typea',
                'width' => 24
            ),
            array(
                'title' => '申报人姓名',
                'field' => "username",
                'width' => 24
            ),
            array(
                'title' => '申报人电话',
                'field' => "userphone",
                'width' => 24
            ),
            array(
                'title' => '分配维修工姓名',
                'field' => "staffname",
                'width' => 24
            ),
            array(
                'title' => '分配维修工班组ID',
                'field' => "stafftid",
                'width' => 24
            ),
            array(
                'title' => '分配维修工手机号',
                'field' => "staffphone",
                'width' => 30
            ),
            array(
                'title' => '分配维修工班组名称',
                'field' => "staffteam",
                'width' => 40
            )
        );
      
        $model1 = New Sz_DYi_Excel();
        $model1->export($list1, array(
            "title" => "评价订单信息",
            "columns" => $columns1
        ));
        exit;
        }
        
        

    }else if ($operation == "file") {
      
           //引入文件
        require_once "excel.php";
        //判断是否传入数据
        if ($_W['ispost']) {
           
           $where2 = '';
           // $params = array();
           //判断导出数据类型
           if (isset($_GPC['status']) && !empty($_GPC['status'])) {
              
              $where2.=" WHERE status=".$_GPC['status'];
           }
           if (isset($_GPC['come']) && !empty($_GPC['come'])) {
              
              $where2.=" WHERE come=".$_GPC['come'];
           }
           //判断导出数据的时间
            if ($_GPC['all'] == 1) {
                
                // var_dump($_POST);die();
                $start2 = strtotime($_GPC['datelimit']['start']) ;
                $end2 = strtotime($_GPC['datelimit']['end']) ;
                $where2.=" AND time>=".$start2." AND time<=".$end2 ;

            }

           //查找数据
           $staffe = array();
           $usere = array();
            $list2 = pdo_fetchall("SELECT * FROM ".tablename('move_declare').$where2);
            //查找相关的信息
            foreach ($list2 as $ko => $vale) {
                $list2[$ko]['time'] = date("Y-m-d H:i:s",$vale['time']);
                //判断报装类型
                if ($vale['type'] == 0) {
                    $list2[$ko]['type'] = "报装";
                }else{
                    $list2[$ko]['type'] = "报修";
                }
                 if ($vale['appraise'] == 0) {
                    $list2[$ko]['appraisea'] = "未平价";
                }else{
                    $list2[$ko]['appraisea'] = "已评价";
                }
               //判断订单状态
                if ($vale['status'] == 0) {
                    $list2[$ko]['state'] = "未做任何处理";

                }elseif ($vale['status'] == 1){

                    $list2[$ko]['state'] = "订单处理中";
                }elseif ($vale['status'] == 2){
                    $list2[$ko]['state'] = "订单分配失败"
                    ;
                }elseif ($vale['status'] == 3){

                    $list2[$ko]['state'] = "订单已完成";
                }elseif ($vale['status'] == 4){

                    $list2[$ko]['state'] = "已分配维修工";
                }elseif ($vale['status'] == 5){

                    $list2[$ko]['state'] = "维修工拒绝接单";
                }
                //判断来源信息
                if ($vale['come'] == 0) {
                    $list2[$ko]['typea'] = "员工申报";
                    //员工推荐
                    $usere = pdo_fetch("SELECT name,phone FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$vale['uid']));
                    $list2[$ko]['username'] = $usere['name'];
                    $list2[$ko]['userphone'] = $usere['phone'];
                }else if ($vale['come'] == 1) {
                    $list2[$ko]['typea'] = "客户申报";
                    //客户推荐
                    $usere = pdo_fetch("SELECT name,phone FROM ".tablename("move_customer")." WHERE id=:id",array("id"=>$vale['uid']));
                     $list2[$ko]['username'] = $usere['name'];
                     $list2[$ko]['userphone'] = $usere['phone'];
                }else{
                    $list2[$ko]['typea'] = "后台添加";
                    //后台添加
                    // $user = array();
                }
                //判断会否分配
                if ($vale['status'] != 0 || $vale['status'] != 2) {
                    
                    //查找分配人的信息
                    $staffe = pdo_fetch("SELECT name,tid,phone,team FROM ".tablename("move_staff")." WHERE id=:id",array("id"=>$vale['pid']));
                     $list2[$ko]['staffname'] = $staffe['name'];
                     $list2[$ko]['staffphone'] = $staffe['phone'];
                     $list2[$ko]['stafftid'] = $staffe['tid'];
                     $list2[$ko]['staffteam'] = $staffe['team'];
                     // $list[$ko]['staffphone'] = $staff['phone'];
                }
            }

        // var_dump($list);die();
        if (empty($list2)) {
            message('没有数据可以导出，请重新选择时间!', '', 'info');
        }
     
        $columns2 = array(
            array(
                'title' => '序号',
                'field' => 'id',
                'width' => 12
            ),
            array(
                'title' => '客户姓名',
                'field' => 'name',
                'width' => 20
            ),
            array(
                'title' => '客户所在小区',
                'field' => 'plat',
                'width' => 40
            ),
            array(
                'title' => '客户手机号',
                'field' => 'phone',
                'width' => 20
            ),
            array(
                'title' => '详情描述',
                'field' => 'details',
                'width' => 50
            ),
            array(
                'title' => '报装故障类型',
                'field' => 'type',
                'width' => 12
            ),
            array(
                'title' => '订单状态',
                'field' => 'state',
                'width' => 20
            ),
             array(
                'title' => '维修工拒绝接单原因',
                'field' => 'refuse',
                'width' => 50
            ),
            array(
                'title' => '评价状态',
                'field' => 'appraisea',
                'width' => 12
            ),
            array(
                'title' => '评价星级',
                'field' => 'star',
                'width' => 12
            ),
            array(
                'title' => '申报来源',
                'field' => 'typea',
                'width' => 24
            ),
            array(
                'title' => '申报人姓名',
                'field' => "username",
                'width' => 24
            ),
            array(
                'title' => '申报人电话',
                'field' => "userphone",
                'width' => 24
            ),
            array(
                'title' => '分配维修工姓名',
                'field' => "staffname",
                'width' => 24
            ),
            array(
                'title' => '分配维修工班组ID',
                'field' => "stafftid",
                'width' => 24
            ),
            array(
                'title' => '分配维修工手机号',
                'field' => "staffphone",
                'width' => 30
            ),
            array(
                'title' => '分配维修工班组名称',
                'field' => "staffteam",
                'width' => 40
            )
        );
      
        $model2 = New Sz_DYi_Excel();
        $model2->export($list2, array(
            "title" => "报装故障订单信息",
            "columns" => $columns2
        ));
        exit;
        }
        
        

    }

    
 ?>