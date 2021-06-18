<?php
$id_customer = '';
if (isset($_REQUEST['id_customer']) && ! empty($_REQUEST['id_customer'])) {
    $id_customer = $_REQUEST['id_customer'];
} else {
    returnError("Nhập id_customer!");
}

$order_code = '';
$order_code = "SO" . time();

$order_date_delivery = '';
if (isset($_REQUEST['order_date_delivery']) && ! empty($_REQUEST['order_date_delivery'])) {
    $order_date_delivery = $_REQUEST['order_date_delivery'];
} else {
    returnError("Chọn ngày nhận hàng!");
}

$order_branch_addr_delivery = '';
if (isset($_REQUEST['order_branch_addr_delivery']) && ! empty($_REQUEST['order_branch_addr_delivery'])) {
    $order_branch_addr_delivery = $_REQUEST['order_branch_addr_delivery'];
} else {
    returnError("Chọn địa chỉ nhận hàng!");
}
$order_branch_phone_delivery = '';
if (isset($_REQUEST['order_branch_phone_delivery']) && ! empty($_REQUEST['order_branch_phone_delivery'])) {
    $order_branch_phone_delivery = $_REQUEST['order_branch_phone_delivery'];
} else {
    returnError("Chọn số điện thoại nhận hàng!");
}
$order_branch_title_delivery = '';
if (isset($_REQUEST['order_branch_title_delivery']) && ! empty($_REQUEST['order_branch_title_delivery'])) {
    $order_branch_title_delivery = $_REQUEST['order_branch_title_delivery'];
} else {
    returnError("Chọn tên công ty nhận hàng!");
}

$order_customer_note = '';
if (isset($_REQUEST['order_customer_note']) && ! empty($_REQUEST['order_customer_note'])) {
    $order_customer_note = $_REQUEST['order_customer_note'];
}

$order_status = '1';


$id_product = '';
if (isset($_REQUEST['id_product']) && ! empty($_REQUEST['id_product'])) {
    $id_product = explode(',', $_REQUEST['id_product']);
}else{
    returnError("Chọn sản phẩm!");
}

$quantity_product = '';
if (isset($_REQUEST['quantity_product']) && ! empty($_REQUEST['quantity_product'])) {
    $quantity_product = explode(',', $_REQUEST['quantity_product']);
}else{
    returnError("Nhập số lượng sản phẩm!");
}

if (count($id_product) != count($quantity_product)) {
    returnError("array id_product and array quantity_product is not equal!");
}

// insert into table_order
$sql = ' INSERT INTO tbl_order_order
           SET
           id_customer       ="' . $id_customer . '",
           order_code       ="' . $order_code . '",
           order_date_delivery       ="' . $order_date_delivery . '",
           order_branch_addr_delivery        ="' . $order_branch_addr_delivery . '",
           order_branch_phone_delivery      = "' . $order_branch_phone_delivery . '",
           order_branch_title_delivery      = "' . $order_branch_title_delivery . '",
           order_customer_note      = "' . $order_customer_note . '",
           order_status          = "' . $order_status . '" ';

// $result = mysqli_query($conn, $sql);

if ($conn->query($sql)) {
    // insert into table_order_detail
    $id_order = mysqli_insert_id($conn);
    
    // cập nhật thông tin order
    if (! empty($id_product) && count($id_product) > 0) {
        for ($i = 0; $i < count($id_product); $i ++) {
            if (! empty($id_product[$i]) && ! empty($quantity_product[$i])) {
                
                $sql_insert_order_detail = 'INSERT INTO tbl_order_detail
                                         SET
                                         id_order = "' . $id_order . '",
                                         id_product = "' . $id_product[$i] . '",
                                         detail_quantity = "' . $quantity_product[$i] . '" ';
                mysqli_query($conn, $sql_insert_order_detail);
            }
        }
    }
    // cập nhật tiến trình (ngày) xử lý cho từng công đoạn
    
    $sql_insert_order_process = 'INSERT INTO tbl_order_process
                                         SET
                                         id_order = "' . $id_order . '",
                                         order_status = "1" ';
    mysqli_query($conn, $sql_insert_order_process);
    
    
    // push notity admin
    $title = "Thông báo đặt hàng!!!";
    $bodyMessage = "Có đơn đặt hàng đang chờ xử lý";
    $action = "check_booking_admin";
    
    $type_send = 'topic';
    $to = 'qlsx_admin_booking';
    
    pushNotification($title, $bodyMessage, $action, $to, $type_send);
    
    returnSuccess("Yêu cầu đặt hàng của bạn đã được ghi nhận và chờ xử lý!");
} else {
    returnError("Yêu cầu đặt hàng không thành công!");
}

?>