<?php
$order_id = '';
if (isset($_REQUEST['order_id']) && ! empty($_REQUEST['order_id'])) {
    $order_id = $_REQUEST['order_id'];
}else{
    returnError("Nhập order_id!");
}

$order_status = '';
if (isset($_REQUEST['order_status']) && ! empty($_REQUEST['order_status'])) {
    $order_status = $_REQUEST['order_status'];
}else{
    returnError("Nhập order_status!");
}

$sql_check_production_info = "SELECT * FROM tbl_production_production WHERE id = '" . $order_id . "'";
$result_check_production_info = mysqli_query($conn, $sql_check_production_info);
$num_check_production_info = mysqli_num_rows($result_check_production_info);
$old_status_production = '';
$id_production = $order_id;
if ($num_check_production_info > 0) {
    while ($rowProductionInfo = $result_check_production_info->fetch_assoc()) {
        $old_status_production = $rowProductionInfo['production_status'];
    }
}

$sql = "UPDATE tbl_production_production SET production_status = '" . $order_status . "' WHERE id = '" . $order_id . "'";

switch ($order_status){
    case 'INV': //INV: Nhận NVL từ Kho (manufacturing update status)
        //check material order
        $sql_check_material_order = "SELECT * FROM tbl_production_material WHERE id_production = '" . $id_production . "' ";
        $result_check_material_order = mysqli_query($conn, $sql_check_material_order);
        $num_result_check_material_order = mysqli_num_rows($result_check_material_order);
        
        if ($num_result_check_material_order > 0) {
            while ($rowItemMaterialOrder = $result_check_material_order->fetch_assoc()) {
                
                $id_material_production =  $rowItemMaterialOrder['id_material'];
                $quantity_expected = $rowItemMaterialOrder['quantity_expected'];
                
                //check material storeage
                $sql_check_material_storeage = "SELECT * FROM tbl_storage_material WHERE id_material = '".$id_material_production."'";
                $result_check_material_storeage = mysqli_query($conn, $sql_check_material_storeage);
                $num_result_check_material_storeage = mysqli_num_rows($result_check_material_storeage);
                
                $old_value_storeage = '0';
                
                if ($num_result_check_material_storeage > 0) {
                    while ($rowItemMaterialStoreage = $result_check_material_storeage->fetch_assoc()) {
                        $old_value_storeage = $rowItemMaterialStoreage['storage_quantity'];
                    }
                    // update new value storeage
                    $new_value_storeage = $old_value_storeage - $quantity_expected;
                    
                    $sql_update_new_value_storeage = "UPDATE tbl_storage_material SET
                            storage_quantity = '" . $new_value_storeage . "'
                            WHERE id_material = '" . $id_material_production . "'
                    ";
                    mysqli_query($conn, $sql_update_new_value_storeage);
                }
            }
        }
        break;
        
    case 'FIS': //FIS: Hoàn tất (manufacturing update status)
        
        if ($old_status_production == 'FIS') {
            returnError("Lệnh sản xuất đã hoàn thành!");
        }
        
        if ($old_status_production == 'IMP') {
            returnError("Lệnh sản xuất đã được chuyển qua nhập kho!");
        }
        
        if ($old_status_production == 'COMP') {
            returnError("Lệnh sản xuất đã hoàn tất nhập kho!");
        }
        
        $production_actual_date = '';
        if (isset($_REQUEST['production_actual_date']) && ! empty($_REQUEST['production_actual_date'])) {
            $production_actual_date = $_REQUEST['production_actual_date'];
        }else{
            returnError("Nhập ngày hoàn thành!");
        }
        
        $production_reason = '';
        if (isset($_REQUEST['production_reason']) && ! empty($_REQUEST['production_reason'])) {
            $production_reason = $_REQUEST['production_reason'];
        }
        
        $sql = "UPDATE tbl_production_production SET 
                production_status = '" . $order_status . "', 
                production_actual_date = '" . $production_actual_date . "', 
                production_reason = '" . $production_reason . "' 
                WHERE id = '" . $order_id . "'
        ";
        
        //push notity
        $title = "Thông báo sản xuất!!!";
        $bodyMessage = "Có đơn hàng sản xuất đã hoàn thành";
        $action = "admin_order_production";
        
        $type_send = 'topic';
        $to = 'qlsx_admin_order_production';
        
        pushNotification($title, $bodyMessage, $action, $to, $type_send);
        
        break;
        
    case 'IMP'://IMP: Nhập kho (admin update status)
        
        if ($old_status_production == 'IMP') {
            returnError("Lệnh sản xuất đã được chuyển qua nhập kho!");
        }
        
        if ($old_status_production == 'COMP') {
            returnError("Lệnh sản xuất đã hoàn tất nhập kho!");
        }
        break;
        
    case 'COMP'://COMP: Hoàn thành lệnh (warehouse update status). 
        //Cập nhật tồn kho sản phẩm
        if ($old_status_production == 'COMP') {
            returnError("Lệnh sản xuất đã hoàn tất nhập kho!");
        }
        
        // check product order
        $sql_check_product_order = "SELECT * FROM tbl_production_product WHERE id_production = '" . $id_production . "' ";
        $result_check_product_order = mysqli_query($conn, $sql_check_product_order);
        $num_result_check_product_order = mysqli_num_rows($result_check_product_order);
        
        if ($num_result_check_product_order > 0) {
            while ($rowItemProductOrder = $result_check_product_order->fetch_assoc()) {
                
                $id_product_production =  $rowItemProductOrder['id_product'];
                $quantity_actual = $rowItemProductOrder['quantity_actual'];
                
                //check product storeage
                $sql_check_product_storeage = "SELECT * FROM tbl_storage_product WHERE id_product = '".$id_product_production."'";
                $result_check_product_storeage = mysqli_query($conn, $sql_check_product_storeage);
                $num_result_check_product_storeage = mysqli_num_rows($result_check_product_storeage);
                
                $old_value_storeage = '0';
                
                if ($num_result_check_product_storeage > 0) {
                    while ($rowItemProductStoreage = $result_check_product_storeage->fetch_assoc()) {
                        $old_value_storeage = $rowItemProductStoreage['storage_quantity'];
                    }
                    // update new value storeage
                    $new_value_storeage = $old_value_storeage + $quantity_actual;
                    
                    $sql_update_new_value_storeage = "UPDATE tbl_storage_product SET
                            storage_quantity = '" . $new_value_storeage . "'
                            WHERE id_product = '" . $id_product_production . "'
                    ";
                    mysqli_query($conn, $sql_update_new_value_storeage);
                }else{
                    //create new value product storeage
                    $new_value_storeage =  $quantity_actual;
                    
                    $sql_update_new_value_storeage = "INSERT INTO tbl_storage_product SET
                            storage_quantity = '" . $new_value_storeage . "',
                            id_product = '" . $id_product_production . "'
                    ";
                    mysqli_query($conn, $sql_update_new_value_storeage);
                }
            }
        }
        
        //check material order
        $sql_check_material_order = "SELECT * FROM tbl_production_material WHERE id_production = '" . $id_production . "' ";
        $result_check_material_order = mysqli_query($conn, $sql_check_material_order);
        $num_result_check_material_order = mysqli_num_rows($result_check_material_order);
        
        if ($num_result_check_material_order > 0) {
            while ($rowItemMaterialOrder = $result_check_material_order->fetch_assoc()) {
                
                $id_material_production =  $rowItemMaterialOrder['id_material'];
                $quantity_actual = $rowItemMaterialOrder['quantity_actual'] != null ? $rowItemMaterialOrder['quantity_actual'] :'0';
                
                //check material storeage
                $sql_check_material_storeage = "SELECT * FROM tbl_storage_material WHERE id_material = '".$id_material_production."'";
                $result_check_material_storeage = mysqli_query($conn, $sql_check_material_storeage);
                $num_result_check_material_storeage = mysqli_num_rows($result_check_material_storeage);
                
                $old_value_storeage = '0';
                
                if ($num_result_check_material_storeage > 0) {
                    while ($rowItemMaterialStoreage = $result_check_material_storeage->fetch_assoc()) {
                        $old_value_storeage = $rowItemMaterialStoreage['storage_quantity'];
                    }
                    // update new value storeage
                    $new_value_storeage = $old_value_storeage + $quantity_actual;
                    
                    $sql_update_new_value_storeage = "UPDATE tbl_storage_material SET
                            storage_quantity = '" . $new_value_storeage . "'
                            WHERE id_material = '" . $id_product_production . "'
                    ";
                    mysqli_query($conn, $sql_update_new_value_storeage);
                }else{
                    //create new value product storeage
                    $new_value_storeage =  $quantity_actual;
                    
                    $sql_update_new_value_storeage = "INSERT INTO tbl_storage_material SET
                            storage_quantity = '" . $new_value_storeage . "',
                            id_material = '" . $id_material_production . "'
                    ";
                    mysqli_query($conn, $sql_update_new_value_storeage);
                }
            }
        }
        
        break;
}

if ($conn->query($sql)) {
    returnSuccess("Cập nhật lệnh sản xuất thành công!");
} else {
    returnError("Cập nhật lệnh sản xuất không thành công!");
}