<?php
$sql = "SELECT * FROM tbl_admin_type WHERE 1=1";

$result = $conn->query($sql);

// Get row count
$num = mysqli_num_rows($result);

// Check if any categories
if ($num > 0) {
    $result_arr = array();
    $result_arr['success'] = 'true';
    $result_arr['data'] = array();
    
    while ($row = $result->fetch_assoc()) {
        $item_level = array(
            'id' => $row['id'],
            'type_account' => $row['type_account'],
            'description' => $row['description']
        );
        
        // Push to "data"
        array_push($result_arr['data'], $item_level);
    }
    
    // Turn to JSON & output
    echo json_encode($result_arr);
} else {
    returnError("Không thể tải dữ liệu loại tài khoản!");
}