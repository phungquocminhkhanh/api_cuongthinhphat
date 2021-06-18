<?php
$id_product_production = '';
if (isset($_REQUEST['product_production']) && ! empty($_REQUEST['product_production'])) {
    $id_product_production = explode(',', $_REQUEST['product_production']);
}else{
    returnError("Chọn sản phẩm sản xuất!");
}
$quantity_product_production = '';
if (isset($_REQUEST['quantity_product_production']) && ! empty($_REQUEST['quantity_product_production'])) {
    $quantity_product_production = explode(',', $_REQUEST['quantity_product_production']);
}else{
    returnError("Chọn số lượng sản phẩm sản xuất!");
}

if (count($id_product_production) != count($quantity_product_production)) {
    returnError("array product_production and array quantity_product_production is not equal!");
}

$id_material_production = '';
if (isset($_REQUEST['material_production']) && ! empty($_REQUEST['material_production'])) {
    $id_material_production = explode(',', $_REQUEST['material_production']);
}else{
    returnError("Chọn nguyên vật liệu sản xuất!");
}
$quantity_material_production = '';
if (isset($_REQUEST['quantity_material_production']) && ! empty($_REQUEST['quantity_material_production'])) {
    $quantity_material_production = explode(',', $_REQUEST['quantity_material_production']);
}else{
    returnError("Chọn số lượng nguyên vật liệu sản xuất!");
}

if (count($id_material_production) != count($quantity_material_production)) {
    returnError("array material_production and array quantity_material_production is not equal!");
}

$id_machine = '';
if (isset($_REQUEST['id_machine']) && ! empty($_REQUEST['id_machine'])) {
    $id_machine = $_REQUEST['id_machine'];
} else {
    returnError("Chọn dây chuyền sản xuất!");
}
$production_expected_date = '';
if (isset($_REQUEST['production_expected_date']) && ! empty($_REQUEST['production_expected_date'])) {
    $production_expected_date = $_REQUEST['production_expected_date'];
} else {
    returnError("Chọn thời gian dự kiến!");
}

$production_code = 'LSX'.time();

$sql = ' INSERT INTO  tbl_production_production
           SET
           id_machine       ="' . $id_machine . '",
           production_code       ="' . $production_code . '",
           production_status       = "SET",
           production_expected_date      = "' . $production_expected_date . '" ';

if ($conn->query($sql)) {
    $id_production = mysqli_insert_id($conn);
    
    if (! empty($id_product_production) && count($id_product_production) > 0) {
        for ($i = 0; $i < count($id_product_production); $i ++) {
            if (! empty($id_product_production[$i]) && ! empty($quantity_product_production[$i])) {
                
                $sql_import_production_product = 'INSERT INTO tbl_production_product
                                         SET
                                         id_production = "' . $id_production . '",
                                         id_product = "' . $id_product_production[$i] . '",
                                         quantity_expected = "' . $quantity_product_production[$i] . '" ';
                mysqli_query($conn, $sql_import_production_product);
            }
        }
    
    }
    
    if (! empty($id_material_production) && count($id_material_production) > 0) {
        for ($i2 = 0; $i2 < count($id_material_production); $i2 ++) {
            if (! empty($id_material_production[$i2]) && ! empty($quantity_material_production[$i2])) {
                
                $sql_import_production_material = 'INSERT INTO tbl_production_material
                                         SET
                                         id_production = "' . $id_production . '",
                                         id_material = "' . $id_material_production[$i2] . '",
                                         quantity_expected = "' . $quantity_material_production[$i2] . '" ';
                mysqli_query($conn, $sql_import_production_material);
            }
        }
        
    }
    
    //push notity
    $title = "Thông báo sản xuất!!!";
    $bodyMessage = "Có lệnh sản xuất đang chờ xử lý";
    $action = "order_production_produt";
    
    $type_send = 'topic';
    $to = 'qlsx_order_production_product';
    
    pushNotification($title, $bodyMessage, $action, $to, $type_send);
    
    returnSuccess("Tạo lệnh sản xuất thành công!");
}else{
    returnError("Tạo lệnh sản xuất không thành công!");
}











