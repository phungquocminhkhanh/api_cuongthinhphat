<?php
// query
$sql = "SELECT
            *
          FROM tbl_material_supplier
          WHERE 1=1
         ";

$result = $conn->query($sql);
$num = mysqli_num_rows($result);

$arr_result['success'] = 'true';
$arr_result['data'] = array();

if ($num > 0) {
    while ($row = $result->fetch_assoc()) {
        $supplier_item = array(
            'id' => $row['id'],
            'supplier_name' => $row['supplier_name'],
            'supplier_address' => $row['supplier_address'],
            'supplier_email' => $row['supplier_email'],
            'supplier_phone' => $row['supplier_phone'],
            'supplier_code' => $row['supplier_code']
            
        );
        
        // Push to "data"
        array_push($arr_result['data'], $supplier_item);
    }
}

// Turn to JSON & output
echo json_encode($arr_result);
