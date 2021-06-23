<?php

// query
$sql = "SELECT
            tbl_product_product.id as id_product,
            tbl_product_product.product_code as product_code,
            tbl_product_product.product_name as product_name,
            tbl_product_product.product_img as product_img,
            tbl_product_product.product_description as product_description,
            tbl_product_category.category_title as category_title,
            tbl_product_unit.unit_title as unit_title,
            tbl_product_product.product_unit_packet as product_unit_packet


            FROM tbl_product_product
            LEFT JOIN tbl_product_category ON tbl_product_category.id = tbl_product_product.id_category
            LEFT JOIN tbl_product_unit ON tbl_product_unit.id = tbl_product_product.id_unit ";


if (isset($_REQUEST['id_customer']) && ! empty($_REQUEST['id_customer'])) {
    $id_customer = $_REQUEST['id_customer'];

    $sql .="LEFT JOIN tbl_customer_customer ON tbl_customer_customer.id = tbl_product_product.id_customer
         ";
} 

$sql .=" WHERE 1=1 ";

if(!empty($id_customer)){
    $sql .= " AND id_customer = '" . $id_customer . "'";
}

if (isset($_REQUEST['id_product']) && ! empty($_REQUEST['id_product'])) {
    $id_product = $_REQUEST['id_product'];
    $sql .=" AND tbl_product_product.id = $id_product ";
}   
$result = $conn->query($sql);
$num = mysqli_num_rows($result);

$arr_result['success'] = 'true';
$arr_result['data'] = array();

if ($num > 0) {
    while ($row = $result->fetch_assoc()) {
        $hotline_item = array(
            'id_product' => $row['id_product'],
            'product_code' => $row['product_code'],
            'product_name' => $row['product_name'],
            'product_img' => $row['product_img'],
            'product_description'=> $row['product_description'],
            'category_title'=> $row['category_title'],
            'unit_title'=> $row['unit_title'],
            'product_unit_packet'=> $row['product_unit_packet'],

        );
        
        // Push to "data"
        array_push($arr_result['data'], $hotline_item);
    }
}

// Turn to JSON & output
echo json_encode($arr_result);
exit();