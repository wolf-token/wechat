<?php 

	global $_W,$_GPC;
	$action = 'account';
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
            // $valuesa[] = $rowValue[3];
        }
        // var_dump($values);die();
  //       $ids = implode(',', array_keys($values));
  //       // var_dump($ids); 
		// $sql = "UPDATE ims_move_customer SET account = CASE position "; 
		// foreach ($values as $position => $ordinal) { 
		//     $sql .= sprintf("WHEN %s THEN %d ", $position, $ordinal); 
		// } 
		// $sql .= "END WHERE position IN ($ids)";
  //       // echo $sql;die();
  //       $result1 = pdo_query($sql);
        // var_dump($result1);die();
        $time = time();
         foreach ($values as $key => $value) {
            
            $result1 = pdo_update("move_customer",array("account"=>$value[1],'time'=>$time),array("address"=>$value[3]));
        }
        //判断是否导入
        if (!empty($result1)) {
            
            message("导入用户信息成功",$this->createWebUrl("user",array("op"=>"list")));
        }else{

            message("导入用户信息失败",$this->createWebUrl("user",array("op"=>"file")));

        }
	}


 ?>