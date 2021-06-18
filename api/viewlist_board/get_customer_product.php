<?php
$typeManager = '';
if (isset($_REQUEST['type_manager'])) {
    if ($_REQUEST['type_manager'] == '') {
        unset($_REQUEST['type_manager']);
    } else {
        $typeManager = $_REQUEST['type_manager'];
    }
}

$id_customer = '';
if (isset($_REQUEST['id_customer']) && ! empty($_REQUEST['id_customer'])) {
    $id_customer = $_REQUEST['id_customer'];
} else {
    if (empty($typeManager))
        returnError("Nhập id_customer!");
}

// query
$sql = "SELECT
            tbl_product_product.id as id,
            tbl_product_product.product_code as product_code,
            tbl_product_product.product_name as product_name,
            tbl_product_product.product_description as product_description,
            tbl_product_product.product_img as product_img,
            tbl_product_product.safety_stock as safety_stock,

            tbl_product_unit.id as id_unit,
            tbl_product_unit.unit_title as unit_title,
            tbl_product_unit.unit as unit
          FROM tbl_product_product
    
          LEFT JOIN tbl_product_unit 
          ON tbl_product_unit.id = tbl_product_product.id_unit

          WHERE 1=1
         ";

if(!empty($id_customer)){
    $sql .= " AND id_customer = '" . $id_customer . "'";
}

$result = $conn->query($sql);
$num = mysqli_num_rows($result);

$arr_result['success'] = 'true';
$arr_result['data'] = array();

if ($num > 0) {
    while ($row = $result->fetch_assoc()) {
        $hotline_item = array(
            'id' => $row['id'],
            'product_code' => $row['product_code'],
            'product_name' => $row['product_name'],
            'product_description' => $row['product_description'],
            'safety_stock' => $row['safety_stock'],
            'id_unit' => $row['id_unit'],
            'unit_title' => $row['unit_title'],
            'unit' => $row['unit'],
            'product_img' => $row['product_img']
        
        );
        
        // Push to "data"
        array_push($arr_result['data'], $hotline_item);
    }
}

// Turn to JSON & output
echo json_encode($arr_result);
