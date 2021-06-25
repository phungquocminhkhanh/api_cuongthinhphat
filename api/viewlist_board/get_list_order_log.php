<?php
$sql_count_order = "SELECT count(tbl_order_process_log.id) as order_total 
                    FROM tbl_order_process_log
                    LEFT JOIN tbl_order_order
                        ON tbl_order_process_log.id_order = tbl_order_order.id
                     WHERE 1=1 ";
$result = $conn->query($sql_count_order);

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

$sql_count_order = "SELECT 
                        tbl_order_order.id as id,

                        tbl_order_process_log.order_status as order_status,
                        tbl_order_process_log.process_date as process_date,
                        tbl_customer_customer.customer_company as customer_company
                    FROM tbl_order_process_log
                    LEFT JOIN tbl_order_order
                        ON tbl_order_process_log.id_order = tbl_order_order.id
                    LEFT JOIN tbl_customer_customer 
                        ON tbl_customer_customer.id = tbl_order_order.id_customer
                    WHERE 1=1
                    ORDER BY tbl_order_process_log.id DESC
                     "
                    
                    ;

$result = $conn->query($sql_count_order);
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
            'order_status' => $row['order_status'],
            'process_date' => $row['process_date'],
            'customer_company' => $row['customer_company'],
            'order_item_product' => array()
        );
        
        //check product order
        $sql_check_product_order = "SELECT * FROM tbl_order_detail WHERE id_order = '" . $row['id'] . "' ";
        $result_check_product_order = mysqli_query($conn, $sql_check_product_order);
        $num_result_check_product_order = mysqli_num_rows($result_check_product_order);
        
        if ($num_result_check_product_order > 0) {
            while ($rowItemProductOrder = $result_check_product_order->fetch_assoc()) {
                
                $sql_get_product_info = "SELECT 
                                                tbl_product_product.id as id,
                                                tbl_product_product.id_packet as id_packet,
                                                tbl_product_product.product_name as product_name,product_name   

                                         FROM  tbl_product_product
                                         WHERE tbl_product_product.id = '" . $rowItemProductOrder['id_product'] . "'
                ";
                

                $result_get_product_info = mysqli_query($conn, $sql_get_product_info);
               
                $num_result_get_product_info = mysqli_num_rows($result_get_product_info);

                if ($num_result_get_product_info > 0) {
                    while ($rowItemProductInfo = $result_get_product_info->fetch_assoc()) {

                        $sql_get_packet="SELECT unit_title
                                    FROM `tbl_product_unit` 
                                    WHERE id='" . $rowItemProductInfo['id_packet'] . "'
                        ";
                        $result_sql_get_packet=mysqli_query($conn, $sql_get_packet);
                        $packet=$result_sql_get_packet->fetch_assoc();

                        $product_item = array(
                            'id' => $rowItemProductInfo['id'],
                            'product_name' => $rowItemProductInfo['product_name'],
                            'product_quantity' => $rowItemProductOrder['quantity_packet'],
                            'product_packet_title' => $packet['unit_title'],

                        );
                        
                        array_push($order_item['order_item_product'], $product_item);
                    }
                }
            }
        }
        
        // // Push to "data"
         array_push($order_arr['data'], $order_item);
    }
}
echo json_encode($order_arr);
?>