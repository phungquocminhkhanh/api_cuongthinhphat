<?php
// query
$sql = "SELECT
            *
          FROM tbl_company_feature
          WHERE 1=1
         ";

$result = $conn->query($sql);
$num = mysqli_num_rows($result);

$arr_result['success'] = 'true';
$arr_result['data'] = array();

if ($num > 0) {
    while ($row = $result->fetch_assoc()) {
        $hotline_item = array(
            'id' => $row['id'],
            'feature_product' => $row['feature_product'],
            'feature_description' => $row['feature_description'],
            'feature_stars' => $row['feature_stars'],
            'feature_img' => $row['feature_img']
            
        );
        
        // Push to "data"
        array_push($arr_result['data'], $hotline_item);
    }
}

// Turn to JSON & output
echo json_encode($arr_result);
