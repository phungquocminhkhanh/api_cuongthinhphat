<?php
$typeManager = '';
if (isset($_REQUEST['type_manager'])) {
    if ($_REQUEST['type_manager'] == '') {
        unset($_REQUEST['type_manager']);
    } else {
        $typeManager = $_REQUEST['type_manager'];
    }
}

$production_status = '';
if (isset($_REQUEST['production_status']) && ! empty($_REQUEST['production_status'])) {
    $production_status = $_REQUEST['production_status'];
} else {
    if (empty($typeManager)) {
        returnError("Nhập production_status!");
    }
}

$date_start = '';
$date_end = '';

if (isset($_REQUEST['date_begin']) && ! empty($_REQUEST['date_begin'])) {
    $date_start = $_REQUEST['date_begin'];
}
if (isset($_REQUEST['date_end']) && ! empty($_REQUEST['date_end'])) {
    $date_end = $_REQUEST['date_end'];
}

if (isset($_REQUEST['date_option'])) {
    if ($_REQUEST['date_option'] == '') {
        unset($_REQUEST['date_option']);
    } else {
        if (substr($_REQUEST['date_option'], 0, 1) == 'M') {
            $year = substr($_REQUEST['date_option'], - 4);
            $pos = strpos($_REQUEST['date_option'], '_');
            $month = substr($_REQUEST['date_option'], 1, $pos - 1);
            $title_filter = "Tháng " . $month . " năm " . $year;
            $date_start = $year . '-' . $month . '-1';
            $date_end = $year . '-' . $month . '-31';
        }
    }
}

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

            WHERE 1=1
";

if (! empty($production_status)) {
    if ($production_status == 'three_some') {
        $sql .= " AND ( tbl_production_production.production_status = 'FIS' 
                        OR tbl_production_production.production_status = 'IMP' 
                        OR tbl_production_production.production_status = 'COMP' 
        )";
    } else{
        $sql .= " AND tbl_production_production.production_status = '" . $production_status . "'";
    }
}

if (! empty($date_start) && ! empty($date_end)) {
    $sql .= " AND (DATE(tbl_production_production.create_production) >= '" . $date_start . "'
                                AND  DATE(tbl_production_production.create_production) <= '" . $date_end . "') ";
}

$sql .= " ORDER BY tbl_production_production.create_production DESC ";

$result = mysqli_query($conn, $sql);
$num = mysqli_num_rows($result);

$order_arr['success'] = 'true';
$order_arr['data'] = array();

if ($num > 0) {
    while ($row = $result->fetch_assoc()) {
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
                            'id' => $rowItemProductInfo['id'],
                            'id_production' => $rowItemProductOrder['id'],
                            'id_category' => $rowItemProductInfo['id_category'],
                            'product_name' => $rowItemProductInfo['product_name'],
                            'product_code' => $rowItemProductInfo['product_code'],
                            'product_description' => changeLineBreak(stripCKeditor($rowItemProductInfo['product_description'])),
                            'product_img' => $rowItemProductInfo['product_img'],
                            'quantity_expected' => $rowItemProductOrder['quantity_expected'],
                            'quantity_actual' => $rowItemProductOrder['quantity_actual'] != null ? $rowItemProductOrder['quantity_actual'] : "",
                            'id_unit' => $rowItemProductInfo['id_unit']
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
                            'id' => $rowItemMaterialInfo['id'],
                            'id_production' => $rowItemMaterialOrder['id'],
                            'id_supplier' => $rowItemMaterialInfo['id_supplier'],
                            'material_name' => $rowItemMaterialInfo['material_name'],
                            'material_code' => $rowItemMaterialInfo['material_code'],
                            'material_spec' => $rowItemMaterialInfo['material_spec'],
                            'quantity_expected' => $rowItemMaterialOrder['quantity_expected'],
                            'quantity_actual' => $rowItemMaterialOrder['quantity_actual'] != null ? $rowItemMaterialOrder['quantity_actual'] : "",
                            'id_unit' => $rowItemMaterialInfo['id_unit']
                        );
                        
                        array_push($order_item['order_item_material'], $material_item);
                    }
                }
            }
        }
        
        // Push to "data"
        array_push($order_arr['data'], $order_item);
    }
}

// Turn to JSON & output
echo json_encode($order_arr);















