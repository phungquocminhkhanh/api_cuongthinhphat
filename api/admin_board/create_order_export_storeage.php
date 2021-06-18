<?php

$id_order = '';
if (isset($_REQUEST['id_order']) && ! empty($_REQUEST['id_order'])) {
    $id_order = $_REQUEST['id_order'];
} else {
    returnError("Chọn đơn hàng!");
}

$id_product_export = '';
if (isset($_REQUEST['id_product_export']) && ! empty($_REQUEST['id_product_export'])) {
    $id_product_export = explode(',', $_REQUEST['id_product_export']);
}else{
    returnError("Chọn sản phẩm!");
}

$specification_unit = '';
if (isset($_REQUEST['specification_unit']) && ! empty($_REQUEST['specification_unit'])) {
    $specification_unit = explode(',', $_REQUEST['specification_unit']);
} else {
    returnError("Chọn quy cách đóng gói!");
}

if (count($id_product_export) != count($specification_unit)) {
    returnError("array id_product_export and array specification_unit is not equal!");
}

$delivery_time = '';
if (isset($_REQUEST['delivery_time']) && ! empty($_REQUEST['delivery_time'])) {
    $delivery_time = $_REQUEST['delivery_time'];
} else {
    returnError("Nhập thời gian giao hàng!");
}
$delivery_note = '';
if (isset($_REQUEST['delivery_note']) && ! empty($_REQUEST['delivery_note'])) {
    $delivery_note = $_REQUEST['delivery_note'];
} 

$storage_export_code = 'LGH'.time();

$sql = ' INSERT INTO  tbl_export_storage
           SET
           id_order       ="' . $id_order . '",
           storage_export_code       ="' . $storage_export_code . '",
           delivery_time       = "'.$delivery_time.'",
           delivery_note      = "' . $delivery_note . '" ';

if ($conn->query($sql)) {
    
    $id_export = mysqli_insert_id($conn);
    
    if (! empty($id_product_export) && count($id_product_export) > 0) {
        for ($i = 0; $i < count($id_product_export); $i ++) {
            if (! empty($id_product_export[$i]) && ! empty($specification_unit[$i])) {
                
                $sql_import_specification_unit_product = 'INSERT INTO tbl_export_storage_detail
                                         SET
                                         id_export = "' . $id_export . '",
                                         id_product = "' . $id_product_export[$i] . '",
                                         specification_unit = "' . $specification_unit[$i] . '" ';
                mysqli_query($conn, $sql_import_specification_unit_product);
            }
        }
        
    }
    
    
    //push notity
    $title = "Thông báo giao hàng!!!";
    $bodyMessage = "Có lệnh giao hàng đang chờ xử lý";
    $action = "order_export_produt";
    
    $type_send = 'topic';
    $to = 'qlsx_order_export_product';
    
    pushNotification($title, $bodyMessage, $action, $to, $type_send);
    
    returnSuccess("Tạo lệnh giao hàng thành công!");
}else{
    returnError("Tạo lệnh giao hàng không thành công!");
}











