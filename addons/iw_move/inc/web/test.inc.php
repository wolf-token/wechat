<?php 

	global $_W,$_GPC;
	$action = 'test';
	$uniacid = $_W['uniacid'];
    
	$url = $this->createWebUrl($action, array('op' => 'add'));
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'add';

if ($operation == "add") {

        require_once IA_ROOT . '/framework/library/phpexcel/PHPExcel.php';
        require_once IA_ROOT . '/framework/library/phpexcel/PHPExcel/IOFactory.php';
        require_once IA_ROOT . '/framework/library/phpexcel/PHPExcel/Reader/Excel5.php';
        $path = IA_ROOT . "/addons/iw_move/data/";
        if (!is_dir($path)) {
            load()->func('file');
            mkdirs($path, '0777');
        }
        $file     = time() . $_W['uniacid'] . ".xlsx";
        $filename = $_FILES["file_stu"]['name'];
        $tmpname  = $_FILES["file_stu"]['tmp_name'];
        if (empty($tmpname)) {
            message('请选择要上传的Excel文件!', '', 'error');
        }
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($ext != 'xlsx') {
            message('请上传 xlsx 格式的Excel文件!', '', 'error');
        }
        $uploadfile = $path . $file;
        $result     = move_uploaded_file($tmpname, $uploadfile);
        if (!$result) {
            message('上传Excel 文件失败, 请重新上传!', '', 'error');
        } $reader             = PHPExcel_IOFactory::createReader('Excel2007');
        $excel              = $reader->load($uploadfile);
        $sheet              = $excel->getActiveSheet();
        $highestRow         = $sheet->getHighestRow();
        $highestColumn      = $sheet->getHighestColumn();
        $highestColumnCount = PHPExcel_Cell::columnIndexFromString($highestColumn);
        $values             = array();
        for ($row = 2; $row <= $highestRow; $row++) {
            $rowValue = array();
            for ($col = 0; $col < $highestColumnCount; $col++) {
                $rowValue[] = $sheet->getCellByColumnAndRow($col, $row)->getValue();
            }
            $values[] = $rowValue;
        }
        
        $time = time();
        $pass = md5("root");
        foreach($values as $v){

            $valueStr .= "('".$v[0]."','".$v[1]."','".$v[2]."','".$v[3]."','".$v[4]."','".$time."','0','3'),";
        } 

        $valueStr = rtrim($valueStr,',');
        $sql = "INSERT IGNORE  INTO `ims_move_customer`(name,account,phone,address,plot,time,pid,type) values $valueStr";
        $result1 = pdo_query($sql);
        // var_dump($result1);die();

        //判断是否导入
        if (!empty($result1)) {
            
            message("导入用户信息成功",$this->createWebUrl("user",array("op"=>"list")));
        }else{

            message("数据重复,导入用户信息失败",$this->createWebUrl("user",array("op"=>"file")));

        }
	 	
	}else if ($operation == "list") {
      
      //引入文件
        require_once "excel.php";
        //判断是否传入数据
        if ($_W['ispost']) {
           
           $where = '';
           $params = array();
           //判断导出数据类型
            if ($_GPC['all'] == 1) {
                
                // var_dump($_POST);die();
                $start = strtotime($_GPC['datelimit']['start']) ;
                $end = strtotime($_GPC['datelimit']['end']) ;
                $where.=" WHERE time>=".$start." AND time<=".$end ;

            }

           //查找数据
           
            $list = pdo_fetchall("SELECT id,name,address,plot,phone,account,math,time FROM ".tablename('move_customer').$where);

        // var_dump($list);die();
        if (empty($list)) {
            message('没有数据可以导出，请重新选择时间!', '', 'info');
        }
        foreach ($list as $key => $value) {
           
           $list[$key]['time'] = date("Y-m-d H:i:s",$value['time']);
        }
        $columns = array(
            array(
                'title' => '序号',
                'field' => 'id',
                'width' => 12
            ),
            array(
                'title' => '姓名',
                'field' => 'name',
                'width' => 20
            ),
            array(
                'title' => '地址',
                'field' => 'address',
                'width' => 40
            ),
             array(
                'title' => '所属小区',
                'field' => 'plot',
                'width' => 40
            ),
            array(
                'title' => '手机号',
                'field' => 'phone',
                'width' => 20
            ),
            array(
                'title' => '宽带账号',
                'field' => 'account',
                'width' => 24
            ),
            array(
                'title' => '推广人数',
                'field' => 'math',
                'width' => 12
            ),
            array(
                'title' => '注册时间',
                'field' => 'time',
                'width' => 12
            )
        );
      
        $model = New Sz_DYi_Excel();
        $model->export($list, array(
            "title" => "用户信息",
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
           $params1 = array();
           //判断导出数据类型
            if ($_GPC['all'] == 1) {
                
                // var_dump($_POST);die();
                $start1 = strtotime($_GPC['datelimit']['start']) ;
                $end1 = strtotime($_GPC['datelimit']['end']) ;
                $where1.=" WHERE time>=".$start1." AND time<=".$end1 ;

            }

           //查找数据
           
            $list1 = pdo_fetchall("SELECT id,name,tid,phone,team,math,service,time FROM ".tablename('move_staff').$where1);

        // var_dump($list);die();
        if (empty($list1)) {
            message('没有数据可以导出，请重新选择时间!', '', 'info');
        }
        foreach ($list1 as $key1 => $value1) {
           
           $list1[$key1]['time'] = date("Y-m-d H:i:s",$value1['time']);
        }
        $columns1 = array(
            array(
                'title' => '序号',
                'field' => 'id',
                'width' => 12
            ),
            array(
                'title' => '姓名',
                'field' => 'name',
                'width' => 20
            ),
            array(
                'title' => '班组ID',
                'field' => 'tid',
                'width' => 24
            ),
            array(
                'title' => '手机号',
                'field' => 'phone',
                'width' => 20
            ),
            array(
                'title' => '班组名称',
                'field' => 'team',
                'width' => 40
            ),
            array(
                'title' => '推广人数',
                'field' => 'math',
                'width' => 12
            ),
            array(
                'title' => '被分配订单数量',
                'field' => 'service',
                'width' => 12
            ),
            array(
                'title' => '注册时间',
                'field' => 'time',
                'width' => 12
            )
        );
      
        $model1 = New Sz_DYi_Excel();
        $model1->export($list1, array(
            "title" => "员工信息",
            "columns" => $columns1
        ));
        exit;
        }
        

   }else if ($operation == "load") {
      
      //引入文件
        require_once "excel.php";
        //判断是否传入数据
        if ($_W['ispost']) {
           
           $where2 = '';
           $params2 = array();
           //判断导出数据类型
            if ($_GPC['all'] == 1) {
                
                // var_dump($_POST);die();
                $start2 = strtotime($_GPC['datelimit']['start']) ;
                $end2 = strtotime($_GPC['datelimit']['end']) ;
                $where2.=" WHERE time>=".$start2." AND time<=".$end2 ;

            }

           //查找数据
           
            $list2 = pdo_fetchall("SELECT * FROM ".tablename('move_team').$where2);

        // var_dump($list);die();
        if (empty($list2)) {
            message('没有数据可以导出，请重新选择时间!', '', 'info');
        }
        foreach ($list2 as $key2 => $value2) {
           
           $list2[$key2]['time'] = date("Y-m-d H:i:s",$value2['time']);
        }
        $columns2 = array(
            array(
                'title' => '序号',
                'field' => 'id',
                'width' => 12
            ),
            array(
                'title' => '小区名称',
                'field' => 'name',
                'width' => 20
            ),
            array(
                'title' => '班组ID',
                'field' => 'pid',
                'width' => 24
            ),
            array(
                'title' => '地市ID',
                'field' => 'aid',
                'width' => 20
            ),
            array(
                'title' => '班组名称',
                'field' => 'nickname',
                'width' => 40
            ),
            array(
                'title' => '地址',
                'field' => 'address',
                'width' => 12
            ),
            array(
                'title' => '区县ID',
                'field' => 'xid',
                'width' => 12
            ),
            array(
                'title' => '街道ID',
                'field' => 'jid',
                'width' => 12
            ),
             array(
                'title' => '路/村ID',
                'field' => 'lid',
                'width' => 12
            ),
              array(
                'title' => '小区/班组ID',
                'field' => 'qid',
                'width' => 12
            ),
            array(
                'title' => '注册时间',
                'field' => 'time',
                'width' => 12
            )
        );
      
        $model2 = New Sz_DYi_Excel();
        $model2->export($list2, array(
            "title" => "小区信息",
            "columns" => $columns2
        ));
        exit;
        }
        

    }

    
 ?>