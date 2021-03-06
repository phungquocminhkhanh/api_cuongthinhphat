<?php
$id_customer = '';
if (isset($_REQUEST['id_customer']) && ! empty($_REQUEST['id_customer'])) {
    $id_customer = $_REQUEST['id_customer'];
} else {
    returnError("Nhập id_customer!");
}
if (isset($_REQUEST['customer_code']) && ! empty($_REQUEST['customer_code'])) {
    $customer_code = $_REQUEST['customer_code'];
} else {
    returnError("Nhập customer_code!");
}

$order_code = '';
$order_code = "DH-" .$customer_code."-".date("Ymd-His",time()) ;

$order_date_delivery = '';
if (isset($_REQUEST['order_date_delivery']) && ! empty($_REQUEST['order_date_delivery'])) {
    $order_date_delivery = $_REQUEST['order_date_delivery'];
} else {
    returnError("Chọn ngày nhận hàng!");
}

$order_record_delivery = '';
if (isset($_REQUEST['order_record_delivery']) && ! empty($_REQUEST['order_record_delivery'])) {
    $order_record_delivery = $_REQUEST['order_record_delivery'];
} else {
    returnError("Chọn địa chỉ nhận hàng!");
}

$order_record_shipping = '';
if (isset($_REQUEST['order_record_shipping']) && ! empty($_REQUEST['order_record_shipping'])) {
    $order_record_shipping = $_REQUEST['order_record_shipping'];
}


$order_note = '';
if (isset($_REQUEST['order_note']) && ! empty($_REQUEST['order_note'])) {
    $order_note = $_REQUEST['order_note'];
}

$order_status = '1';


$id_product = '';
if (isset($_REQUEST['id_product']) && ! empty($_REQUEST['id_product'])) {
    $id_product = explode(',', $_REQUEST['id_product']);
}else{
    returnError("Chọn sản phẩm!");
}

$quantity_packet = '';
if (isset($_REQUEST['quantity_packet']) && ! empty($_REQUEST['quantity_packet'])) {
    $quantity_packet = explode(',', $_REQUEST['quantity_packet']);
}else{
    returnError("Nhập số lượng sản phẩm!");
}

if (count($id_product) != count($quantity_packet)) {
    returnError("array id_product and array quantity_packet is not equal!");
}

// insert into table_order
$sql = ' INSERT INTO tbl_order_order    
           SET
           id_customer       ="' . $id_customer . '",
           order_code       ="' . $order_code . '",
           order_date_delivery       ="' . $order_date_delivery . '",
           order_record_delivery        ="' . $order_record_delivery . '",
           order_record_shipping        ="' . $order_record_shipping . '",
           order_note                     = "' . $order_note . '",
           order_status          = "' . $order_status . '" ';

//$result = mysqli_query($conn, $sql);

if ($conn->query($sql)) {

    // insert into table_order_detail
    $id_order = mysqli_insert_id($conn);
    
    // ghi log
    $sql_log = 'INSERT INTO tbl_order_process_log
                                         SET
                                         id_order = "' . $id_order . '",
                                         order_status = "1"';
                mysqli_query($conn, $sql_log);

    // cập nhật thông tin order
    if (! empty($id_product) && count($id_product) > 0) {
        for ($i = 0; $i < count($id_product); $i++) {
           
                
                $sql_insert_order_detail = 'INSERT INTO tbl_order_detail
                                         SET
                                         id_order = "' . $id_order . '",
                                         id_product = "' . $id_product[$i] . '",
                                         quantity_packet = "' . $quantity_packet[$i] . '" ';
                mysqli_query($conn, $sql_insert_order_detail);
           
        }
    }
    // cập nhật tiến trình (ngày) xử lý cho từng công đoạn
    
    $sql_insert_order_process = 'INSERT INTO tbl_order_process
                                         SET
                                         id_order = "' . $id_order . '",
                                         order_status = "1" ';
    mysqli_query($conn, $sql_insert_order_process);
    
    
    // // push notity admin
    // $title = "Thông báo đặt hàng!!!";
    // $bodyMessage = "Có đơn đặt hàng đang chờ xử lý";
    // $action = "check_booking_admin";
    
    // $type_send = 'topic';
    // $to = 'qlsx_admin_booking';
    
    // pushNotification($title, $bodyMessage, $action, $to, $type_send);
    
    returnSuccess("Yêu cầu đặt hàng của bạn đã được ghi nhận và chờ xử lý!");
} else {
    returnError("Yêu cầu đặt hàng không thành công!");
}

?>