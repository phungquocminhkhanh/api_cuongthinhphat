<?php
$order_id = '';
if (isset($_REQUEST['order_id']) && ! empty($_REQUEST['order_id'])) {
    $order_id = $_REQUEST['order_id'];
}else{
    returnError("Nhập order_id!");
}

$delivery_status = '';
if (isset($_REQUEST['delivery_status']) && ! empty($_REQUEST['delivery_status'])) {
    $delivery_status = $_REQUEST['delivery_status'];
}else{
    returnError("Nhập delivery_status!");
}

$sql_check_export_info = "SELECT * FROM tbl_export_storage WHERE id = '" . $order_id . "'";
$result_check_export_info = mysqli_query($conn, $sql_check_export_info);
$num_check_export_info = mysqli_num_rows($result_check_export_info);
$old_status_export = '';
$id_export = $order_id;
$customer_order_id = '';
if ($num_check_export_info > 0) {
    while ($rowExportInfo = $result_check_export_info->fetch_assoc()) {
        $old_status_export = $rowExportInfo['delivery_status'];
        $customer_order_id = $rowExportInfo['id_order'];
    }
    
}else{
    returnError("Không tìm thấy thông tin lệnh giao hàng!");
}

$sql = "UPDATE tbl_export_storage SET delivery_status = '" . $delivery_status . "' WHERE id = '" . $id_export . "'";

switch ($delivery_status){
    case 'SET': //(admin update status)
        break;
        
    case 'PACK': //(warehouse update status)
        
        if ($old_status_export == 'PACK') {
            returnError("Lệnh giao hàng đã được đóng gói!");
        }
        
        if ($old_status_export == 'TRANS') {
            returnError("Lệnh giao hàng đã được vận chuyển!");
        }
        
        if ($old_status_export == 'FIS') {
            returnError("Lệnh giao hàng đã hoàn tất!");
        }
        
        //cập nhật tồn kho của sản phẩm
        // check product order
        $sql_check_product_order = "SELECT * FROM tbl_order_detail WHERE id_order = '" . $customer_order_id . "' ";
        $result_check_product_order = mysqli_query($conn, $sql_check_product_order);
        $num_result_check_product_order = mysqli_num_rows($result_check_product_order);
        
        if ($num_result_check_product_order > 0) {
            while ($rowItemProductOrder = $result_check_product_order->fetch_assoc()) {
                
                $id_product_order =  $rowItemProductOrder['id_product'];
                $quantity_product_order = $rowItemProductOrder['detail_quantity'];
                
                //check product storeage
                $sql_check_product_storeage = "SELECT * FROM tbl_storage_product WHERE id_product = '".$id_product_order."'";
                $result_check_product_storeage = mysqli_query($conn, $sql_check_product_storeage);
                $num_result_check_product_storeage = mysqli_num_rows($result_check_product_storeage);
                
                $old_value_storeage = '0';
                
                if ($num_result_check_product_storeage > 0) {
                    while ($rowItemProductStoreage = $result_check_product_storeage->fetch_assoc()) {
                        $old_value_storeage = $rowItemProductStoreage['storage_quantity'];
                    }
                    // update new value storeage
                    $new_value_storeage = $old_value_storeage - $quantity_product_order;
                    
                    $sql_update_new_value_storeage = "UPDATE tbl_storage_product SET
                            storage_quantity = '" . $new_value_storeage . "'
                            WHERE id_product = '" . $id_product_order . "'
                    ";
                    mysqli_query($conn, $sql_update_new_value_storeage);
                }else{
                    returnError("Không đủ số lượng sản phẩm trong kho!");
                }
            }
        }
        
        break;
        
    case 'TRANS'://(warehouse update status)
        
        if ($old_status_export == 'TRANS') {
            returnError("Lệnh giao hàng đã được vận chuyển!");
        }
        
        if ($old_status_export == 'FIS') {
            returnError("Lệnh giao hàng đã hoàn tất!");
        }
        //cập nhật trạng thái đơn hàng (tbl_order_order)
        $sql_update_order_status = "UPDATE tbl_order_order SET
                            order_status = '3'
                            WHERE id = '" . $customer_order_id . "'
                    ";
        mysqli_query($conn, $sql_update_order_status);
        
        //cập nhật hành trình đơn hàng (tbl_order_process)
        $sql_insert_order_process = 'INSERT INTO tbl_order_process
                                         SET
                                         id_order = "' . $customer_order_id . '",
                                         order_status = "3" ';
        mysqli_query($conn, $sql_insert_order_process);
        
        break;
        
    case 'FIS':// (warehouse update status).
        if ($old_status_export == 'FIS') {
            returnError("Lệnh giao hàng đã hoàn tất!");
        }
        //cập nhật trạng thái đơn hàng (tbl_order_order)
        $sql_update_order_status = "UPDATE tbl_order_order SET
                            order_status = '4'
                            WHERE id = '" . $customer_order_id . "'
                    ";
        mysqli_query($conn, $sql_update_order_status);
        
        //cập nhật hành trình đơn hàng (tbl_order_process)
        $sql_insert_order_process = 'INSERT INTO tbl_order_process
                                         SET
                                         id_order = "' . $customer_order_id . '",
                                         order_status = "4" ';
        mysqli_query($conn, $sql_insert_order_process);
        
        break;
}

if ($conn->query($sql)) {
    returnSuccess("Cập nhật lệnh giao hàng thành công!");
} else {
    returnError("Cập nhật lệnh giao hàng không thành công!");
}