<?php
$typeManager = '';
if (isset($_REQUEST['type_manager'])) {
    if ($_REQUEST['type_manager'] == '') {
        unset($_REQUEST['type_manager']);
    } else {
        $typeManager = $_REQUEST['type_manager'];
    }
}

$order_status = '';
if (isset($_REQUEST['order_status']) && ! empty($_REQUEST['order_status'])) {
    $order_status = $_REQUEST['order_status'];
}
$id_customer = '';
if (isset($_REQUEST['id_customer']) && ! empty($_REQUEST['id_customer'])) {
    $id_customer = $_REQUEST['id_customer'];
}
if (empty($typeManager) && empty($id_customer)) {
    returnError("Nhập id_customer!");
}
$order_id = '';
if (isset($_REQUEST['order_id']) && ! empty($_REQUEST['order_id'])) {
    $order_id = $_REQUEST['order_id'];
}
$order_code = '';
if (isset($_REQUEST['order_code']) && ! empty($_REQUEST['order_code'])) {
    $order_code = $_REQUEST['order_code'];
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

$order_arr = array();

$sql_count_order = "SELECT count(tbl_order_order.id) as order_total  

                    FROM tbl_order_order
                    LEFT JOIN tbl_customer_customer
                        ON tbl_customer_customer.id = tbl_order_order.id_customer
                     WHERE 1=1 ";

if (! empty($order_status)) {
    if ($order_status == 'three_some') {
        $sql_count_order .= " AND ( tbl_order_order.order_status = '1'
                        OR tbl_order_order.order_status = '2'
                        OR tbl_order_order.order_status = '3'
                        OR tbl_order_order.order_status = '4'
        )";
    } else{
        $sql_count_order .= " AND tbl_order_order.order_status = '" . $order_status . "' ";
    }
    
}

if (! empty($id_customer)) {
    
    $sql_count_order .= " AND tbl_order_order.id_customer = '" . $id_customer . "' ";
}

if (! empty($order_id)) {
    
    $sql_count_order .= " AND tbl_order_order.id = '" . $order_id . "' ";
}
if (! empty($order_code)) {
    
    $sql_count_order .= " AND tbl_order_order.order_code = '" . $order_code . "' ";
}

if (! empty($filter)) {
    $sql_count_order .= " AND tbl_order_order.order_code = '" . $filter . "' ";
}

// if (! empty($thoi_gian_bat_dau)) {
// $sql_count_order .= " AND tbl_order_order.order_date_create >= '" . $thoi_gian_bat_dau . "'
// AND tbl_order_order.order_date_create <= '" . $thoi_gian_ket_thuc . "' ";
// }
if (! empty($thoi_gian_bat_dau) && !empty($thoi_gian_ket_thuc)) {
    if ($thoi_gian_bat_dau == $thoi_gian_ket_thuc) {
        $sql_count_order .= " AND DATE(tbl_order_order.order_date_create) = '" . $thoi_gian_bat_dau . "'
                               ";
    } else {
        $sql_count_order .= " AND (DATE(tbl_order_order.order_date_create) >= '" . $thoi_gian_bat_dau . "'
                                AND  DATE(tbl_order_order.order_date_create) <= '" . $thoi_gian_ket_thuc . "' )";
    }
}

$result = mysqli_query($conn, $sql_count_order);
while ($row = $result->fetch_assoc()) {
    $order_arr['total'] = $row['order_total'];
}

$limit = 20;
$page = 1;
if (isset($_REQUEST['limit']) && $_REQUEST['limit'] != '') {
    $limit = $_REQUEST['limit'];
}
if (isset($_REQUEST['page']) && $_REQUEST['page'] != '') {
    $page = $_REQUEST['page'];
}

$order_arr['total_page'] = strval(ceil($order_arr['total'] / $limit));

$order_arr['limit'] = strval($limit);

$order_arr['page'] = strval($page);

$start = ($page - 1) * $limit;

// query
$sql = "SELECT
            tbl_order_order.id as id,
            tbl_order_order.order_code as order_code,
            tbl_order_order.id_customer as id_customer,
            tbl_order_order.order_status as order_status,
            tbl_order_order.order_date_delivery as order_date_delivery,
            tbl_order_order.order_record_delivery as order_record_delivery,
            tbl_order_order.order_record_shipping as order_record_shipping,
            tbl_order_order.order_note as order_note,
            tbl_order_order.order_total_cost as order_total_cost,
            tbl_order_order.order_record_cancel_note as order_record_cancel_note,

            tbl_customer_customer.customer_name as customer_name,
            tbl_customer_customer.customer_code as customer_code,
            tbl_customer_customer.customer_email as customer_email,
            tbl_customer_customer.customer_phone as customer_phone,

            tbl_order_order.order_date_create as order_date_create
    
          FROM  tbl_order_order
          LEFT JOIN tbl_customer_customer
          ON tbl_customer_customer.id = tbl_order_order.id_customer
    
          WHERE 1=1
          
         ";

if (! empty($order_status)) {
    if ($order_status == 'three_some') {
        $sql .= " AND ( tbl_order_order.order_status = '1'
                        OR tbl_order_order.order_status = '2'
                        OR tbl_order_order.order_status = '3'
                        OR tbl_order_order.order_status = '4'
        )";
    } else{
        $sql .= " AND tbl_order_order.order_status = '" . $order_status . "' ";
    }
}

if (! empty($id_customer)) {
    
    $sql .= " AND tbl_order_order.id_customer = '" . $id_customer . "' ";
}

if (! empty($order_id)) {
    
    $sql .= " AND tbl_order_order.id = '" . $order_id . "' ";
}

if (! empty($order_code)) {
    
    $sql .= " AND tbl_order_order.order_code = '" . $order_code . "' ";
}

if (! empty($filter)) {
    $sql .= " AND tbl_order_order.order_code = '" . $filter . "' ";
}

if (! empty($thoi_gian_bat_dau) && !empty($thoi_gian_ket_thuc)) {
    if ($thoi_gian_bat_dau == $thoi_gian_ket_thuc) {
        $sql .= " AND DATE(tbl_order_order.order_date_create) = '" . $thoi_gian_bat_dau . "'
                               ";
    } else {
        $sql .= " AND (DATE(tbl_order_order.order_date_create) >= '" . $thoi_gian_bat_dau . "'
                                AND  DATE(tbl_order_order.order_date_create) <= '" . $thoi_gian_ket_thuc . "' )";
    }
}

$sql .= " ORDER BY tbl_order_order.order_date_create DESC LIMIT $start,$limit ";

// echo $sql;
// exit;
$result = mysqli_query($conn, $sql);
if(empty($result)){
    $num=0;
}
else
{
    $num = mysqli_num_rows($result);
}
$order_arr['success'] = 'true';
$order_arr['data'] = array();

if ($num > 0) {
    while ($row = $result->fetch_assoc()) {
        
        $order_item = array(
            'id_order' => $row['id'],
            'order_code' => $row['order_code'],
            'id_customer' => $row['id_customer'],
            'order_status' => $row['order_status'],
            'order_date_delivery' => $row['order_date_delivery'],
            'order_record_delivery' => $row['order_record_delivery'],
            'order_record_shipping' => $row['order_record_shipping'],
            'order_total_cost' => $row['order_total_cost'] != null ? (string) $row['order_total_cost'] : "",
            'order_date_create' => $row['order_date_create'],
            'order_note' => $row['order_note']!= null ? $row['order_note'] : "",
            'order_record_cancel_note' => $row['order_record_cancel_note'] != null ? $row['order_record_cancel_note'] : "",
            
            'customer_code' => $row['customer_code'] != null ? $row['customer_code'] : "",
            'customer_phone' => $row['customer_phone'],
            'customer_name' => $row['customer_name'],
            'customer_email' => $row['customer_email'] != null ? $row['customer_email'] : "",
            
            'order_item_product' => array()
        );
        
        // check product order
        $sql_check_product_order = "SELECT * FROM tbl_order_detail WHERE id_order = '" . $row['id'] . "' ";
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
                                                tbl_product_product.id_packet as id_packet,
                                                tbl_product_product.product_unit_packet as product_unit_packet

                     FROM tbl_product_product 
                     LEFT JOIN tbl_product_unit 
                                                ON tbl_product_unit.id=tbl_product_product.id_unit
                     WHERE tbl_product_product.id = '" . $rowItemProductOrder['id_product'] . "'
                ";
                $result_get_product_info = mysqli_query($conn, $sql_get_product_info);
                $num_result_get_product_info = mysqli_num_rows($result_get_product_info);
                if ($num_result_get_product_info > 0) {
                


                    while ($rowItemProductInfo = $result_get_product_info->fetch_assoc()) {
                        //get unit
                        $sql_get_unit="SELECT unit_title
                                FROM `tbl_product_unit` 
                                WHERE id='" . $rowItemProductInfo['id_unit'] . "'
                            ";
                        $result_sql_get_unit=mysqli_query($conn, $sql_get_unit);
                        $unit=$result_sql_get_unit->fetch_assoc();
                        //get packet
                        $sql_get_packet="SELECT unit_title
                                FROM `tbl_product_unit` 
                                WHERE id='" . $rowItemProductInfo['id_packet'] . "'
                            ";
                        $result_sql_get_packet=mysqli_query($conn, $sql_get_packet);
                        $packet=$result_sql_get_packet->fetch_assoc();


                        $product_item = array(
                            'id' => $rowItemProductInfo['id'],
                            'id_category' => $rowItemProductInfo['id_category'],
                            'product_name' => $rowItemProductInfo['product_name'],
                            'product_code' => $rowItemProductInfo['product_code'],
                            'product_description' => changeLineBreak(stripCKeditor($rowItemProductInfo['product_description'])),
                            'product_img' => $rowItemProductInfo['product_img'],

                            'product_quantity_packet' => $rowItemProductOrder['quantity_packet'],

                            'product_unit_title' => $unit['unit_title'],
                            'product_packet_title' => $packet['unit_title'],
                            'product_unit_packet' => $rowItemProductInfo['product_unit_packet']
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

?>






