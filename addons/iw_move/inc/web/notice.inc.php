<?php 

	global $_W,$_GPC;
	$action = 'notice';
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
        // $pass = md5("root");
        foreach($values as $v){

            $valueStr .= "('".$v[9]."','".$v[10]."','".$v[11]."','".$v[0]."','".$v[1].$v[3].$v[5].$v[7].$v[9]."','".$v[2]."','".$v[4]."','".$v[6]."','".$v[8]."','".$time."'),";
        } 

        $valueStr = rtrim($valueStr,',');
        $sql = "INSERT IGNORE INTO `ims_move_team`(name,pid,nickname,aid,address,xid,jid,lid,qid,time) values $valueStr";
        $result1 = pdo_query($sql);
        // var_dump($result1);die();

        //判断是否导入
        if (!empty($result1)) {
            
            message("导入小区信息成功",$this->createWebUrl("plot",array("op"=>"list")));
        }else{

            message("数据重复，导入小区信息失败",$this->createWebUrl("plot",array("op"=>"file")));

        }
	}


 ?>