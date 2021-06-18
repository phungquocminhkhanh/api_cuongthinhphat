<?php
$item_type = '';
if (isset($_REQUEST['item_type']) && ! empty($_REQUEST['item_type'])) {
    $item_type = $_REQUEST['item_type'];
} else {
    returnError("Nhập item_type!");
}

$value_actual = '';
if (isset($_REQUEST['value_actual']) && ! empty($_REQUEST['value_actual'])) {
    $value_actual = $_REQUEST['value_actual'];
} else {
    returnError("Nhập value_actual!");
}

$id_item = '';
if (isset($_REQUEST['id_production']) && ! empty($_REQUEST['id_production'])) {
    $id_item = $_REQUEST['id_production'];
} else {
    returnError("Nhập id_production!");
}

$sql = "";
switch ($item_type) {
    case 'product':
        $sql = "UPDATE tbl_production_product SET quantity_actual = '" . $value_actual . "' WHERE id = '" . $id_item . "'";
        break;
    
    case 'material':
        $sql = "UPDATE tbl_production_material SET quantity_actual = '" . $value_actual . "' WHERE id = '" . $id_item . "'";
        break;
}

if (! empty($sql)) {
    if ($conn->query($sql)) {
        returnSuccess("Cập nhật lệnh sản xuất thành công!");
    } else {
        returnError("Cập nhật lệnh sản xuất không thành công!");
    }
}