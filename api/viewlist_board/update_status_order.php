<?php
if (isset($_REQUEST['id_order'])) {
    if ($_REQUEST['id_order'] == '') {
        unset($_REQUEST['id_order']);
    }
}

if (! isset($_REQUEST['id_order'])) {
    echo json_encode(array(
        'success' => 'false',
        'message' => 'Nhập id_order !'
    ));
    exit();
}

if (isset($_REQUEST['order_status'])) {
    if ($_REQUEST['order_status'] == '') {
        unset($_REQUEST['order_status']);
    }
}

if (! isset($_REQUEST['order_status'])) {
    echo json_encode(array(
        'success' => 'false',
        'message' => 'Nhập order_status !'
    ));
    exit();
}

if ($_REQUEST['order_status'] > 5 || $_REQUEST['order_status'] <= 0) {
    echo json_encode(array(
        'success' => 'false',
        'message' => 'order_status không thỏa mãn!'
    ));
    exit();
}
$orderStatus = $_REQUEST['order_status'];
$idOrder = $_REQUEST['id_order'];
$orderStatusCheck = '';

$sql_check_order = "
                SELECT * FROM tbl_order_order
                    WHERE id = '" . $idOrder . "'
            ";

$resultCheckOrder = mysqli_query($conn, $sql_check_order);
$num_resultCheckOrder = mysqli_num_rows($resultCheckOrder);
if ($num_resultCheckOrder > 0) {
    while ($rowItemCheck = $resultCheckOrder->fetch_assoc()) {
        $orderStatusCheck = $rowItemCheck['order_status'];
    }
}

if ($orderStatusCheck >= $orderStatus) {
    switch ($orderStatusCheck) {
        case '2':
            returnError("Đơn hàng đã xác nhận & gia công, không thể cập nhật trạng thái!");
            break;
        case '3':
            returnError("Đơn hàng đã đóng gói & giao hàng, không thể cập nhật trạng thái!");
            break;
        
        case '4':
            returnError("Đơn hàng đã hoàn thành, không thể cập nhật trạng thái!");
            break;
        
        case '5':
            returnError("Đơn hàng đã hủy, không thể cập nhật trạng thái!");
            break;
    }
}

if ($orderStatus == 5) { // cập nhật hủy đơn hàng
    if ($orderStatusCheck == '2') {
        returnError("Đơn hàng đã xác nhận & gia công, không thể hủy!");
    }
    if ($orderStatusCheck == '3') {
        returnError("Đơn hàng đã đóng gói & vận chuyển, không thể hủy!");
    }
    
    if ($orderStatusCheck == '4') {
        returnError("Đơn hàng đã hoàn tất, không thể hủy!");
    }
}

if (isset($orderStatus)) {
    $query = "UPDATE tbl_order_order SET ";
    
    $query .= "order_status  = '" . mysqli_real_escape_string($conn, $orderStatus) . "' ";
    
    if ($orderStatus == 5) {
        
        // trang thai huy don hang thÃ¬ kiem tra them ghi chu
        if (isset($_REQUEST['cancel_comment']) && ! empty($_REQUEST['cancel_comment'])) {
            $query .= " ,cancel_comment  = '" . mysqli_real_escape_string($conn, $_REQUEST['cancel_comment']) . "' ";
        }
    } else if ($orderStatus == 2) {
        
        // trang thai đã xác nhận
        if (isset($_REQUEST['order_date_delivery']) && ! empty($_REQUEST['order_date_delivery'])) {
            $query .= " ,order_date_delivery  = '" . mysqli_real_escape_string($conn, $_REQUEST['order_date_delivery']) . "' ";
        }
        if (isset($_REQUEST['order_total_cost']) && ! empty($_REQUEST['order_total_cost'])) {
            $query .= " ,order_total_cost  = '" . mysqli_real_escape_string($conn, $_REQUEST['order_total_cost']) . "' ";
        } else {
            returnError("Nhập tổng giá trị đơn hàng!");
        }
        
        //cập nhật hành trình đơn hàng (tbl_order_process)
        $sql_insert_order_process = 'INSERT INTO tbl_order_process
                                         SET
                                         id_order = "' . $idOrder . '",
                                         order_status = "2" ';
        mysqli_query($conn, $sql_insert_order_process);
    }
    
    $query .= "WHERE id = '" . mysqli_real_escape_string($conn, $idOrder) . "'";
    
    // Create post
    if ($conn->query($query)) {
        
        // tra lai ket qua
        echo json_encode(array(
            'success' => 'true',
            'message' => 'Cập nhật thành công!'
        ));
        
        $title = "Thông báo đơn hàng!!!";
        $type_send = 'topic';
        $to = "";
        $bodyMessage = "";
        $action = "check_order";
        
        if (! empty($to)) {
            pushNotification($title, $bodyMessage, $action, $to, $type_send);
        }
    } else {
        returnError("Cập nhật không thành công!");
    }
}

?>