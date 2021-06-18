<?php
$typeManager = '';
if (isset($_REQUEST['type_manager'])) {
    if ($_REQUEST['type_manager'] == '') {
        unset($_REQUEST['type_manager']);
    } else {
        $typeManager = $_REQUEST['type_manager'];
    }
}
$delivery_status = '';
if (isset($_REQUEST['delivery_status']) && ! empty($_REQUEST['delivery_status'])) {
    $delivery_status = $_REQUEST['delivery_status'];
} else {
    if (empty($typeManager)) {
        returnError("Nhập delivery_status!");
    }
}

$date_start = '';
$date_end = '';
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
if (isset($_REQUEST['date_begin']) && ! empty($_REQUEST['date_begin'])) {
    $date_start = $_REQUEST['date_begin'];
}
if (isset($_REQUEST['date_end']) && ! empty($_REQUEST['date_end'])) {
    $date_end = $_REQUEST['date_end'];
}

$sql = " SELECT
            tbl_export_storage.id as id,
            tbl_export_storage.storage_export_code as storage_export_code,
            tbl_export_storage.id_order as id_order,
            tbl_export_storage.delivery_time as delivery_time,
            tbl_export_storage.delivery_note as delivery_note,
            tbl_export_storage.delivery_status as delivery_status,

            tbl_order_order.id  as customer_order_id,
            tbl_order_order.order_code  as customer_order_code,
            tbl_order_order.order_date_delivery  as customer_order_date_delivery,
            tbl_order_order.order_branch_addr_delivery  as customer_order_branch_addr_delivery,
            tbl_order_order.order_branch_phone_delivery  as customer_order_branch_phone_delivery,
            tbl_order_order.order_branch_title_delivery  as customer_order_branch_title_delivery,
            tbl_order_order.order_customer_note  as customer_order_customer_note,
            tbl_order_order.order_status  as customer_order_status,
            tbl_order_order.order_total_cost  as customer_order_total_cost,
            tbl_order_order.order_date_create  as customer_order_date_create,
            tbl_order_order.cancel_comment  as customer_order_cancel_comment,

            tbl_customer_customer.customer_name  as customer_name,
            tbl_customer_customer.customer_phone  as customer_phone
    
            FROM tbl_export_storage
    
            LEFT JOIN tbl_order_order
            ON tbl_order_order.id = tbl_export_storage.id_order


            LEFT JOIN tbl_customer_customer
            ON tbl_customer_customer.id = tbl_order_order.id_customer
    
            WHERE 1=1
";

if (!empty($delivery_status)){
    $sql .= " AND tbl_export_storage.delivery_status = '" . $delivery_status . "'"; 
}

if (! empty($date_start) && ! empty($date_end)) {
    $sql .= " AND (DATE(tbl_export_storage.delivery_time) >= '" . $date_start . "'
                                AND  DATE(tbl_export_storage.delivery_time) <= '" . $date_end . "') ";
}

$sql .= " ORDER BY tbl_export_storage.delivery_time DESC ";

$result = mysqli_query($conn, $sql);
$num = mysqli_num_rows($result);

$order_arr['success'] = 'true';
$order_arr['data'] = array();

if ($num > 0) {
    while ($row = $result->fetch_assoc()) {
        $order_item = array(
            'id_order_export' => $row['id'],
            'storage_export_code' => $row['storage_export_code'],
            'delivery_time' => $row['delivery_time'],
            'delivery_note' => $row['delivery_note'],
            'delivery_status' => $row['delivery_status'],
            'customer_name' => $row['customer_name'],
            'customer_phone' => $row['customer_phone'],
            'customer_order_id' => $row['customer_order_id'],
            'customer_order_code' => $row['customer_order_code'],
            'customer_order_date_delivery' => $row['customer_order_date_delivery'],
            'customer_order_branch_addr_delivery' => $row['customer_order_branch_addr_delivery'],
            'customer_order_branch_phone_delivery' => $row['customer_order_branch_phone_delivery'],
            'customer_order_branch_title_delivery' => $row['customer_order_branch_title_delivery'],
            'customer_order_customer_note' => $row['customer_order_customer_note'],
            'customer_order_status' => $row['customer_order_status'],
            'customer_order_total_cost' => $row['customer_order_total_cost'] != null ? $row['customer_order_total_cost'] : "",
            'customer_order_date_create' => $row['customer_order_date_create'],
            'customer_order_cancel_comment' => $row['customer_order_cancel_comment'] != null ? $row['customer_order_cancel_comment'] : "",
            
            'order_item_product' => array()
        );
        
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
                                                tbl_product_product.id_unit as id_unit,
  
                                                tbl_product_unit.unit_title  as specification_unit_title,
                                                tbl_product_unit.unit  as specification_unit_unit
                                                
                     FROM tbl_product_product

                     LEFT JOIN tbl_export_storage_detail
                     ON tbl_product_product.id = tbl_export_storage_detail.id_product

                     LEFT JOIN tbl_product_unit
                     ON tbl_product_unit.id = tbl_export_storage_detail.specification_unit

                     WHERE tbl_product_product.id = '" . $rowItemProductOrder['id_product'] . "' AND tbl_export_storage_detail.id_export = '".$row['id']."'
                ";
                $result_get_product_info = mysqli_query($conn, $sql_get_product_info);
                $num_result_get_product_info = mysqli_num_rows($result_get_product_info);
                if ($num_result_get_product_info > 0) {
                    while ($rowItemProductInfo = $result_get_product_info->fetch_assoc()) {
                        $product_item = array(
                            'id' => $rowItemProductInfo['id'],
                            'id_category' => $rowItemProductInfo['id_category'],
                            'product_name' => $rowItemProductInfo['product_name'],
                            'product_code' => $rowItemProductInfo['product_code'],
                            'product_description' => changeLineBreak(stripCKeditor($rowItemProductInfo['product_description'])),
                            'product_img' => $rowItemProductInfo['product_img'],
                            'export_quantity' => $rowItemProductOrder['detail_quantity'],
                            'id_unit' => $rowItemProductInfo['id_unit'],
                            'specification_unit_title' => $rowItemProductInfo['specification_unit_title'],
                            'specification_unit_unit' => $rowItemProductInfo['specification_unit_unit']
                        );
                        
                        array_push($order_item['order_item_product'], $product_item);
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
