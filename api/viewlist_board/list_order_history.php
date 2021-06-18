<?php
$typeManager = '';
if (isset($_REQUEST['type_manager'])) {
    if ($_REQUEST['type_manager'] == '') {
        unset($_REQUEST['type_manager']);
    } else {
        $typeManager = $_REQUEST['type_manager'];
    }
}

$order_type = '';
if (isset($_REQUEST['order_type']) && ! empty($_REQUEST['order_type'])) {
    $order_type = $_REQUEST['order_type'];
} else {
    returnError("Nhập order_type!");
}

$thoi_gian_bat_dau = '';
if (isset($_REQUEST['date_begin']) && ! empty($_REQUEST['date_begin'])) {
    $thoi_gian_bat_dau = $_REQUEST['date_begin'];
}
$thoi_gian_ket_thuc = '';
if (isset($_REQUEST['date_end']) && ! empty($_REQUEST['date_end'])) {
    $thoi_gian_ket_thuc = $_REQUEST['date_end'];
}

$filter = '';
if (isset($_REQUEST['filter']) && ! empty($_REQUEST['filter'])) {
    $filter = $_REQUEST['filter'];
}

$sql = "";

switch ($order_type) {
    case 'export_product':
        
        $sql = " SELECT
            tbl_export_storage.id as id,
            tbl_export_storage.storage_export_code as storage_export_code,
            tbl_export_storage.id_order as id_order,
            tbl_export_storage.specification_unit as specification_unit,
            tbl_export_storage.specification_quantity as specification_quantity,
            tbl_export_storage.delivery_time as delivery_time,
            tbl_export_storage.delivery_note as delivery_note,
            tbl_export_storage.delivery_status as delivery_status,

            tbl_order_order.id  as customer_order_id,
            tbl_order_order.order_date_delivery  as customer_order_date_delivery,
            tbl_order_order.order_branch_addr_delivery  as customer_order_branch_addr_delivery,
            tbl_order_order.order_branch_phone_delivery  as customer_order_branch_phone_delivery,
            tbl_order_order.order_branch_title_delivery  as customer_order_branch_title_delivery,
            tbl_order_order.order_customer_note  as customer_order_customer_note,
            tbl_order_order.order_status  as customer_order_status,
            tbl_order_order.order_total_cost  as customer_order_total_cost,
            tbl_order_order.order_date_create  as customer_order_date_create,
            tbl_order_order.cancel_comment  as customer_order_cancel_comment,

            tbl_product_unit.unit_title  as product_unit_title,
            tbl_product_unit.unit  as product_unit,

            tbl_customer_customer.customer_name  as customer_name,
            tbl_customer_customer.customer_phone  as customer_phone
    
            FROM tbl_export_storage
    
            LEFT JOIN tbl_order_order
            ON tbl_order_order.id = tbl_export_storage.id_order

            LEFT JOIN tbl_product_unit
            ON tbl_product_unit.id = tbl_export_storage.specification_unit

            LEFT JOIN tbl_customer_customer
            ON tbl_customer_customer.id = tbl_order_order.id_customer
    
            WHERE tbl_export_storage.delivery_status = 'FIS'
        ";
        
        if (! empty($thoi_gian_bat_dau) && ! empty($thoi_gian_ket_thuc)) {
            if ($thoi_gian_bat_dau == $thoi_gian_ket_thuc) {
                $sql .= " AND DATE(tbl_export_storage.delivery_time) = '" . $thoi_gian_bat_dau . "'
                               ";
            } else {
                $sql .= " AND (DATE(tbl_export_storage.delivery_time) >= '" . $thoi_gian_bat_dau . "'
                                AND  DATE(tbl_export_storage.delivery_time) <= '" . $thoi_gian_ket_thuc . "' )";
            }
        }
        
        $sql .= " ORDER BY DATE(tbl_export_storage.delivery_time) DESC ";
        
        break;
    
    case 'import_product':
    case 'export_material':
        $sql = " SELECT
            tbl_production_production.id as id,
            tbl_production_production.production_code as production_code,
            tbl_production_production.production_expected_date as production_expected_date,
            tbl_production_production.production_actual_date as production_actual_date,
            tbl_production_production.production_reason as production_reason,
            tbl_production_production.production_status as production_status,
            tbl_production_production.create_production as create_production,
            
            tbl_production_production.id_machine as id_machine,
            tbl_production_machine.machine_title as machine_title,
            tbl_production_machine.machine_description as machine_description
            
            FROM tbl_production_production
            
            LEFT JOIN tbl_production_machine
            ON tbl_production_machine.id = tbl_production_production.id_machine
            
            WHERE tbl_production_production.production_status = 'COMP'
        ";
        if (! empty($thoi_gian_bat_dau) && ! empty($thoi_gian_ket_thuc)) {
            if ($thoi_gian_bat_dau == $thoi_gian_ket_thuc) {
                $sql .= " AND DATE(tbl_production_production.create_production) = '" . $thoi_gian_bat_dau . "'
                               ";
            } else {
                $sql .= " AND (DATE(tbl_production_production.create_production) >= '" . $thoi_gian_bat_dau . "'
                                AND  DATE(tbl_production_production.create_production) <= '" . $thoi_gian_ket_thuc . "' )";
            }
        }
        
        $sql .= " ORDER BY tbl_production_production.create_production DESC ";
        
        break;
    
    case 'import_material':
        // nhập kho từ nhà cung ứng
        $sql = " SELECT 
                    tbl_import_supplier.id as id,
                    tbl_import_supplier.id_supplier as id_supplier,
                    tbl_import_supplier.storage_import_code as storage_import_code,
                    tbl_import_supplier.storage_import_note as storage_import_note,
                    tbl_import_supplier.import_date as import_date,

                    tbl_material_supplier.supplier_name as supplier_name,
                    tbl_material_supplier.supplier_code as supplier_code,
                    tbl_material_supplier.supplier_address as supplier_address,
                    tbl_material_supplier.supplier_email as supplier_email,
                    tbl_material_supplier.supplier_phone as supplier_phone

                FROM tbl_import_supplier
                LEFT JOIN tbl_material_supplier
                ON tbl_material_supplier.id = tbl_import_supplier.id_supplier

                WHERE 1=1

        ";
        
        if (! empty($thoi_gian_bat_dau) && ! empty($thoi_gian_ket_thuc)) {
            if ($thoi_gian_bat_dau == $thoi_gian_ket_thuc) {
                $sql .= " AND DATE(tbl_import_supplier.import_date) = '" . $thoi_gian_bat_dau . "'
                               ";
            } else {
                $sql .= " AND (DATE(tbl_import_supplier.import_date) >= '" . $thoi_gian_bat_dau . "'
                                AND  DATE(tbl_import_supplier.import_date) <= '" . $thoi_gian_ket_thuc . "' )";
            }
        }
        
        $sql .= " ORDER BY tbl_import_supplier.import_date DESC ";
        
        break;
}

$order_arr = array();

// echo $sql;
// exit;
$result = mysqli_query($conn, $sql);
$num = mysqli_num_rows($result);

$order_arr['success'] = 'true';
$order_arr['data'] = array();

if ($num > 0) {
    while ($row = $result->fetch_assoc()) {
        
        switch ($order_type) {
            case 'export_product':
                $order_item = array(
                    'id_order_export' => $row['id'],
                    'storage_export_code' => $row['storage_export_code'],
                    'id_order' => $row['id_order'],
                    'specification_unit' => $row['specification_unit'],
                    'specification_quantity' => $row['specification_quantity'],
                    'delivery_time' => $row['delivery_time'],
                    'delivery_note' => $row['delivery_note'],
                    'delivery_status' => $row['delivery_status'],
                    
                    'customer_name' => $row['customer_name'],
                    'customer_phone' => $row['customer_phone'],
                    
                    'product_unit_title' => $row['product_unit_title'],
                    'product_unit' => $row['product_unit'],
                    
                    'customer_order_id' => $row['customer_order_id'],
                    'customer_order_date_delivery' => $row['customer_order_date_delivery'],
                    'customer_order_branch_addr_delivery' => $row['customer_order_branch_addr_delivery'],
                    'customer_order_branch_phone_delivery' => $row['customer_order_branch_phone_delivery'],
                    'customer_order_branch_title_delivery' => $row['customer_order_branch_title_delivery'],
                    'customer_order_customer_note' => $row['customer_order_customer_note'],
                    'customer_order_status' => $row['customer_order_status'],
                    'customer_order_total_cost' => $row['customer_order_total_cost'],
                    'customer_order_date_create' => $row['customer_order_date_create'],
                    'customer_order_cancel_comment' => $row['customer_order_cancel_comment'] != null ? $row['customer_order_cancel_comment'] : "",
                    
                    'order_item_product' => array()
                );
                
                // check product order
                $sql_check_product_order = "SELECT * FROM tbl_order_detail WHERE id_order = '" . $row['customer_order_id'] . "' ";
                $result_check_product_order = mysqli_query($conn, $sql_check_product_order);
                $num_result_check_product_order = mysqli_num_rows($result_check_product_order);
                
                if ($num_result_check_product_order > 0) {
                    while ($rowItemProductOrder = $result_check_product_order->fetch_assoc()) {
                        
                        $sql_get_product_info = "SELECT
                                                        tbl_product_product.id as id,
                                                        tbl_product_product.id_category as id_category,
                                                        tbl_product_product.product_name as product_name,
                                                        tbl_product_product.product_code as product_code,
                                                        tbl_product_product.product_description as product_description,
                                                        tbl_product_product.product_img as product_img,
                                                        tbl_product_product.id_unit as id_unit
                                    
                             FROM tbl_product_product
                             WHERE tbl_product_product.id = '" . $rowItemProductOrder['id_product'] . "'
                        ";
                        $result_get_product_info = mysqli_query($conn, $sql_get_product_info);
                        $num_result_get_product_info = mysqli_num_rows($result_get_product_info);
                        if ($num_result_get_product_info > 0) {
                            while ($rowItemProductInfo = $result_get_product_info->fetch_assoc()) {
                                $product_item = array(
                                    'item_export_name' => $rowItemProductInfo['product_name'],
                                    'export_code' => $row['storage_export_code'],
                                    'export_date' => $row['delivery_time'],
                                    'export_quantity' => $rowItemProductOrder['detail_quantity']
                                );
                                
                                array_push($order_item['order_item_product'], $product_item);
                            }
                        }
                    }
                }
                
                // Push to "data"
                array_push($order_arr['data'], $order_item);
                break;
            
            case 'import_product': // production_status = 'COMP'
            case 'export_material': // production_status = 'COMP'
                $order_item = array(
                    'id_order_production' => $row['id'],
                    'production_code' => $row['production_code'],
                    'production_expected_date' => $row['production_expected_date'],
                    'production_actual_date' => $row['production_actual_date'],
                    'production_reason' => $row['production_reason'],
                    'production_status' => $row['production_status'],
                    'id_machine' => $row['id_machine'],
                    'machine_title' => $row['machine_title'],
                    'machine_description' => $row['machine_description'],
                    'create_production' => $row['create_production'],
                    
                    'order_item_product' => array(),
                    'order_item_material' => array()
                );
                
                // check product order
                $sql_check_product_order = "SELECT * FROM tbl_production_product WHERE id_production = '" . $row['id'] . "' ";
                $result_check_product_order = mysqli_query($conn, $sql_check_product_order);
                $num_result_check_product_order = mysqli_num_rows($result_check_product_order);
                
                if ($num_result_check_product_order > 0) {
                    while ($rowItemProductOrder = $result_check_product_order->fetch_assoc()) {
                        
                        $sql_get_product_info = "SELECT
                                                tbl_product_product.id as id,
                                                tbl_product_product.id_category as id_category,
                                                tbl_product_product.product_name as product_name,
                                                tbl_product_product.product_code as product_code,
                                                tbl_product_product.product_description as product_description,
                                                tbl_product_product.product_img as product_img,
                                                tbl_product_product.id_unit as id_unit
                            
                     FROM tbl_product_product
                     WHERE tbl_product_product.id = '" . $rowItemProductOrder['id_product'] . "'
                ";
                        $result_get_product_info = mysqli_query($conn, $sql_get_product_info);
                        $num_result_get_product_info = mysqli_num_rows($result_get_product_info);
                        if ($num_result_get_product_info > 0) {
                            while ($rowItemProductInfo = $result_get_product_info->fetch_assoc()) {
                                $product_item = array(
                                    'import_code' => $row['production_code'],
                                    'import_date' => $row['production_actual_date'],
                                    'import_item_name' => $rowItemProductInfo['product_name'],
                                    'quantity_expected' => $rowItemProductOrder['quantity_expected'],
                                    'import_quantity' => $rowItemProductOrder['quantity_actual'] != null ? $rowItemProductOrder['quantity_actual'] : ""
                                );
                                
                                array_push($order_item['order_item_product'], $product_item);
                            }
                        }
                    }
                }
                
                // check material order
                $sql_check_material_order = "SELECT * FROM tbl_production_material WHERE id_production = '" . $row['id'] . "' ";
                $result_check_material_order = mysqli_query($conn, $sql_check_material_order);
                $num_result_check_material_order = mysqli_num_rows($result_check_material_order);
                
                if ($num_result_check_material_order > 0) {
                    while ($rowItemMaterialOrder = $result_check_material_order->fetch_assoc()) {
                        
                        $sql_get_material_info = "SELECT
                                                        tbl_material_material.id as id,
                                                        tbl_material_material.id_supplier as id_supplier,
                                                        tbl_material_material.material_name as material_name,
                                                        tbl_material_material.material_code as material_code,
                                                        tbl_material_material.material_spec as material_spec,
                                                        tbl_material_material.id_unit as id_unit
                                    
                             FROM tbl_material_material
                             WHERE tbl_material_material.id = '" . $rowItemMaterialOrder['id_material'] . "'
                        ";
                        $result_get_material_info = mysqli_query($conn, $sql_get_material_info);
                        $num_result_get_material_info = mysqli_num_rows($result_get_material_info);
                        if ($num_result_get_material_info > 0) {
                            while ($rowItemMaterialInfo = $result_get_material_info->fetch_assoc()) {
                                $material_item = array(
                                    'item_export_name' => $rowItemMaterialInfo['material_name'],
                                    'export_code' => $row['production_code'],
                                    'export_date' => $row['create_production'],
                                    'export_quantity' => $rowItemMaterialOrder['quantity_expected'],
                                    'quantity_actual' => $rowItemMaterialOrder['quantity_actual']
                                );
                                
                                array_push($order_item['order_item_material'], $material_item);
                            }
                        }
                    }
                }
                
                // Push to "data"
                array_push($order_arr['data'], $order_item);
                break;
            
            case 'import_material':
                $order_material = array(
                    'import_code' => $row['storage_import_code'],
                    'import_note' => $row['storage_import_note'] != null ? $row['storage_import_note'] : "",
                    'import_date' => $row['import_date'],
                    'id_supplier' => $row['id_supplier'],
                    'supplier_name' => $row['supplier_name'],
                    'supplier_code' => $row['supplier_code'],
                    'supplier_address' => $row['supplier_address'],
                    'supplier_email' => $row['supplier_email'],
                    'supplier_phone' => $row['supplier_phone'],
                    
                    'order_item_material' => array()
                );
                
                // check material order
                $sql_check_material_order = "SELECT * FROM tbl_import_supplier_material WHERE id_import = '" . $row['id'] . "' ";
                
                $result_check_material_order = mysqli_query($conn, $sql_check_material_order);
                $num_result_check_material_order = mysqli_num_rows($result_check_material_order);
                
                if ($num_result_check_material_order > 0) {
                    while ($rowItemMaterialOrder = $result_check_material_order->fetch_assoc()) {
                        
                        $sql_get_material_info = "SELECT
                                                    tbl_material_material.id as id,
                                                    tbl_material_material.id_supplier as id_supplier,
                                                    tbl_material_material.material_name as material_name,
                                                    tbl_material_material.material_code as material_code,
                                                    tbl_material_material.material_spec as material_spec,
                                                    tbl_material_material.id_unit as id_unit
                            
                                                 FROM tbl_material_material
                                                 WHERE tbl_material_material.id = '" . $rowItemMaterialOrder['id_material'] . "'
                        ";
                        $result_get_material_info = mysqli_query($conn, $sql_get_material_info);
                        $num_result_get_material_info = mysqli_num_rows($result_get_material_info);
                        if ($num_result_get_material_info > 0) {
                            while ($rowItemMaterialInfo = $result_get_material_info->fetch_assoc()) {
                                $material_item = array(
                                    'import_item_name' => $rowItemMaterialInfo['material_name'],
                                    'import_item_code' => $row['storage_import_code'],
                                    'import_date' => $row['import_date'],
                                    'import_quantity' => $rowItemMaterialOrder['import_quantity']
                                );
                                
                                array_push($order_material['order_item_material'], $material_item);
                                
                            }
                        }
                    }
                }
                
                // Push to "data"
                array_push($order_arr['data'], $order_material);
                
                break;
        }
    }
}

if ($order_type == 'import_material'){
    
    // check NKNVL từ sản suất của item material
    $sql_check_import_material = " SELECT
                        tbl_production_production.id as id,
                        tbl_production_production.production_code as production_code,
                        tbl_production_production.production_expected_date as production_expected_date,
                        tbl_production_production.production_actual_date as production_actual_date,
                        tbl_production_production.production_reason as production_reason,
                        tbl_production_production.production_status as production_status,
                        tbl_production_production.create_production as create_production,
        
                        tbl_production_production.id_machine as id_machine,
                        tbl_production_machine.machine_title as machine_title,
                        tbl_production_machine.machine_description as machine_description
        
                        FROM tbl_production_production
        
                        LEFT JOIN tbl_production_machine
                        ON tbl_production_machine.id = tbl_production_production.id_machine
        
                        WHERE tbl_production_production.production_status = 'COMP'
                    ";
    if (! empty($thoi_gian_bat_dau) && ! empty($thoi_gian_ket_thuc)) {
        if ($thoi_gian_bat_dau == $thoi_gian_ket_thuc) {
            $sql_check_import_material .= " AND DATE(tbl_production_production.create_production) = '" . $thoi_gian_bat_dau . "'
                               ";
        } else {
            $sql_check_import_material .= " AND (DATE(tbl_production_production.create_production) >= '" . $thoi_gian_bat_dau . "'
                                AND  DATE(tbl_production_production.create_production) <= '" . $thoi_gian_ket_thuc . "' )";
        }
    }
    
    $sql_check_import_material .= " ORDER BY tbl_production_production.create_production DESC ";
    
    $result_check_import_material = mysqli_query($conn, $sql_check_import_material);
    $num_check_import_material = mysqli_num_rows($result_check_import_material);
    
    if ($num_check_import_material > 0) {
        while ($row_check_import_material = $result_check_import_material->fetch_assoc()) {
            
            $order_material = array(
                'import_code' => $row_check_import_material['production_code'],
                'import_note' => "",
                'import_date' => $row_check_import_material['production_actual_date'],
                'id_supplier' => "",
                'supplier_name' => "",
                'supplier_code' => "",
                'supplier_address' => "",
                'supplier_email' => "",
                'supplier_phone' => "",
                
                'order_item_material' => array()
            );
            
            // check material order
            $sql_check_material_order = "SELECT * FROM tbl_production_material WHERE id_production = '" . $row_check_import_material['id'] . "' ";
            $result_check_material_order = mysqli_query($conn, $sql_check_material_order);
            $num_result_check_material_order = mysqli_num_rows($result_check_material_order);
            
            if ($num_result_check_material_order > 0) {
                while ($rowItemMaterialOrder = $result_check_material_order->fetch_assoc()) {
                    
                    $sql_get_material_info = "SELECT
                                                                    tbl_material_material.id as id,
                                                                    tbl_material_material.id_supplier as id_supplier,
                                                                    tbl_material_material.material_name as material_name,
                                                                    tbl_material_material.material_code as material_code,
                                                                    tbl_material_material.material_spec as material_spec,
                                                                    tbl_material_material.id_unit as id_unit
                        
                                         FROM tbl_material_material
                                         WHERE tbl_material_material.id = '" . $rowItemMaterialOrder['id_material'] . "'
                                    ";
                    $result_get_material_info = mysqli_query($conn, $sql_get_material_info);
                    $num_result_get_material_info = mysqli_num_rows($result_get_material_info);
                    if ($num_result_get_material_info > 0) {
                        while ($rowItemMaterialInfo = $result_get_material_info->fetch_assoc()) {
                            $material_item = array(
                                'import_item_name' => $rowItemMaterialInfo['material_name'],
                                'material_code' => $row_check_import_material['production_code'],
                                'import_date' => $row_check_import_material['production_actual_date'],
                                'import_quantity' => $rowItemMaterialOrder['quantity_actual'] != null ? $rowItemMaterialOrder['quantity_actual'] : ""
                            );
                            
                            array_push($order_material['order_item_material'], $material_item);
                            
                           
                        }
                    }
                }
            }
            
            // Push to "data"
            array_push($order_arr['data'], $order_material);
        }
    }
    
}
// Turn to JSON & output
echo json_encode($order_arr);

?>






