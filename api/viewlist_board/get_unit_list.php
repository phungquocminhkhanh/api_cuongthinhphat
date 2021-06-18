<?php
// query
$sql = "SELECT
            *
          FROM tbl_product_unit
          WHERE 1=1
         ";

$result = $conn->query($sql);
$num = mysqli_num_rows($result);

$arr_result['success'] = 'true';
$arr_result['data'] = array();

if ($num > 0) {
    while ($row = $result->fetch_assoc()) {
        $unit_item = array(
            'id' => $row['id'],
            'unit_title' => $row['unit_title'],
            'unit' => $row['unit']
            
        );
        
        // Push to "data"
        array_push($arr_result['data'], $unit_item);
    }
}

// Turn to JSON & output
echo json_encode($arr_result);
