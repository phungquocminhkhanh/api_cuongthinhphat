<?php
// query
$sql = "SELECT
            *
          FROM tbl_production_machine
          WHERE 1=1
         ";

$result = $conn->query($sql);
$num = mysqli_num_rows($result);

$arr_result['success'] = 'true';
$arr_result['data'] = array();

if ($num > 0) {
    while ($row = $result->fetch_assoc()) {
        $machine_item = array(
            'id' => $row['id'],
            'machine_title' => $row['machine_title'],
            'machine_description' => $row['machine_description']
            
        );
        
        // Push to "data"
        array_push($arr_result['data'], $machine_item);
    }
}

// Turn to JSON & output
echo json_encode($arr_result);
