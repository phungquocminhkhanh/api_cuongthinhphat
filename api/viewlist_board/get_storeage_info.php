<?php
$info_type = '';
if (isset($_REQUEST['info_type']) && ! empty($_REQUEST['info_type'])) {
    $info_type = $_REQUEST['info_type'];
} else {
    returnError("Nhập info_type!");
}

$item_type = '';
if (isset($_REQUEST['item_type']) && ! empty($_REQUEST['item_type'])) {
    $item_type = $_REQUEST['item_type'];
} else {
    returnError("Nhập item_type!");
}

$result_arr = array();

$result_arr['success'] = 'true';
$result_arr['data'] = array();

switch ($info_type) {
    case 'stock':
        // tồn kho
        switch ($item_type) {
            case 'product':
                // thành phẩm
                $sql = " SELECT 
                            tbl_storage_product.id as id,
                            tbl_storage_product.id_product as id_product,
                            tbl_storage_product.storage_quantity as storage_quantity,

                            tbl_product_product.product_name as product_name,
                            tbl_product_product.safety_stock  as safety_stock,

                            tbl_product_unit.unit_title as unit_title,
                            tbl_product_unit.unit as unit

                        FROM tbl_storage_product

                        LEFT JOIN tbl_product_product
                        ON tbl_product_product.id = tbl_storage_product.id_product

                        LEFT JOIN tbl_product_unit
                        ON tbl_product_unit.id = tbl_product_product.id_unit
                ";
                
                $result = mysqli_query($conn, $sql);
                $num = mysqli_num_rows($result);
                
                if ($num > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $item_product = array(
                            'item_storeage_name' => $row['product_name'],
                            'storage_quantity' => $row['storage_quantity'],
                            'safety_stock' => $row['safety_stock'],
                            'unit_title' => $row['unit_title'],
                            'unit' => $row['unit']
                        );
                        
                        // Push to "data"
                        array_push($result_arr['data'], $item_product);
                    }
                }
                // Turn to JSON & output
                echo json_encode($result_arr);
                break;
            
            case 'material': // NVL
                
                $sql = " SELECT
                            tbl_storage_material.id as id,
                            tbl_storage_material.id_material as id_material,
                            tbl_storage_material.storage_quantity as storage_quantity,
                    
                            tbl_material_material.material_name as material_name,
                            tbl_material_material.safety_stock as safety_stock,
                    
                            tbl_product_unit.unit_title as unit_title,
                            tbl_product_unit.unit as unit
                    
                        FROM tbl_storage_material
                    
                        LEFT JOIN tbl_material_material
                        ON tbl_material_material.id = tbl_storage_material.id_material
                    
                        LEFT JOIN tbl_product_unit
                        ON tbl_product_unit.id = tbl_material_material.id_unit
                ";
                
                $result = mysqli_query($conn, $sql);
                $num = mysqli_num_rows($result);
                
                if ($num > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $item_material = array(
                            'item_storeage_name' => $row['material_name'],
                            'storage_quantity' => $row['storage_quantity'],
                            'safety_stock' => $row['safety_stock'],
                            'unit_title' => $row['unit_title'],
                            'unit' => $row['unit']
                        );
                        
                        // Push to "data"
                        array_push($result_arr['data'], $item_material);
                    }
                }
                // Turn to JSON & output
                echo json_encode($result_arr);
                break;
        }
        
        break;
    
    case 'export': // xuất kho
        switch ($item_type) {
            case 'product': // thành phẩm
                
                $sql = " SELECT
                        tbl_export_storage.id as id,
                        tbl_export_storage.storage_export_code as storage_export_code,
                        tbl_export_storage.id_order as id_order,
                        tbl_export_storage.delivery_time as delivery_time,
                        tbl_export_storage.delivery_status as delivery_status
                                
                        FROM tbl_export_storage
                                
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
                
                $order_arr = array();
                
                $result = mysqli_query($conn, $sql);
                $num = mysqli_num_rows($result);
                
                $order_arr['success'] = 'true';
                $order_arr['data'] = array();
                
                if ($num > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // check product order
                        $sql_check_product_order = "SELECT * FROM tbl_order_detail WHERE id_order = '" . $row['id_order'] . "' ";
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
                                        
                                        array_push($order_arr['data'], $product_item);
                                    }
                                }
                            }
                        }
                    }
                
                }
                
                // Turn to JSON & output
                echo json_encode($order_arr);
                
                break;
            
            case 'material': // NVL

                $sql = " SELECT
                        tbl_production_production.id as id,
                        tbl_production_production.production_code as production_code,
                        tbl_production_production.create_production as create_production
                                
                        FROM tbl_production_production
                                
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
                
                $order_arr = array();
                
                // echo $sql;
                // exit;
                $result = mysqli_query($conn, $sql);
                $num = mysqli_num_rows($result);
                
                $order_arr['success'] = 'true';
                $order_arr['data'] = array();
                
                if ($num > 0) {
                    while ($row = $result->fetch_assoc()) {
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
                                            'export_quantity' => $rowItemMaterialOrder['quantity_expected']
                                        );
                                        
                                        array_push($order_arr['data'], $material_item);
                                    }
                                }
                            }
                        }
                    }
                }

                // Turn to JSON & output
                echo json_encode($order_arr);
                break;
        }
        break;
    
    case 'import': // nhập kho
        switch ($item_type) {
            case 'product': // thành phẩm
                
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
                
                $order_arr = array();
                
                // echo $sql;
                // exit;
                $result = mysqli_query($conn, $sql);
                $num = mysqli_num_rows($result);
                
                $order_arr['success'] = 'true';
                $order_arr['data'] = array();
                
                if ($num > 0) {
                    while ($row = $result->fetch_assoc()) {
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
                                            'import_quantity' => $rowItemProductOrder['quantity_actual'] != null ? $rowItemProductOrder['quantity_actual'] : ""
                                        );
                                        
                                        array_push($order_arr['data'], $product_item);
                                    }
                                }
                            }
                        }
                    }
                }
                // Turn to JSON & output
                echo json_encode($order_arr);
                
                break;
            
            case 'material': // NVL
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
                
                $order_arr = array();
                
                // echo $sql;
                // exit;
                $result = mysqli_query($conn, $sql);
                $num = mysqli_num_rows($result);
                
                $order_arr['success'] = 'true';
                $order_arr['data'] = array();
                
                if ($num > 0) {
                    while ($row = $result->fetch_assoc()) {
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
                                        
                                        array_push($order_arr['data'], $material_item);
                                    }
                                }
                            }
                        }
                        
                    }
                }
                
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
                                        
                                        array_push($order_arr['data'], $material_item);
                                    }
                                }
                            }
                        }
                    }
                }
                
                // Turn to JSON & output
                echo json_encode($order_arr);
                break;
        }
        break;
}

