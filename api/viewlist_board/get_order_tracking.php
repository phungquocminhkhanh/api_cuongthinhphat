<?php
$order_id = '';
if (isset($_REQUEST['order_id']) && ! empty($_REQUEST['order_id'])) {
    $order_id = $_REQUEST['order_id'];
}else{
    returnError("Nhập order_id!");
}

$sql = "SELECT * FROM tbl_order_process WHERE id_order = '".$order_id."' ORDER BY order_status ASC";

$result = $conn->query($sql);
$num = mysqli_num_rows($result);

$arr_result['success'] = 'true';
$arr_result['data'] = array();

if ($num > 0) {
    while ($row = $result->fetch_assoc()) {
        $tracking_item = array(
            'id' => $row['id'],
            'order_status' => $row['order_status'],
            'process_date' => $row['process_date']
            
        );
        
        // Push to "data"
        array_push($arr_result['data'], $tracking_item);
    }
}

// Turn to JSON & output
echo json_encode($arr_result);