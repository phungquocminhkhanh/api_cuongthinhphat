<?php
$id_supplier = '';
if (isset($_REQUEST['id_supplier']) && ! empty($_REQUEST['id_supplier'])) {
    $id_supplier = $_REQUEST['id_supplier'];
} else {
    returnError("Nhập id_supplier!");
}

$id_material = '';
if (isset($_REQUEST['id_material']) && ! empty($_REQUEST['id_material'])) {
    $id_material = explode(',', $_REQUEST['id_material']);
}else{
    returnError("Chọn nguyên vật liệu!");
}

$quantity_material = '';
if (isset($_REQUEST['quantity_material']) && ! empty($_REQUEST['quantity_material'])) {
    $quantity_material = explode(',', $_REQUEST['quantity_material']);
}else{
    returnError("Nhập số lượng nguyên vật liệu!");
}

if (count($id_material) != count($quantity_material)) {
    returnError("array id_material and array quantity_material is not equal!");
}

$storage_import_note = '';
if (isset($_REQUEST['storage_import_note']) && ! empty($_REQUEST['storage_import_note'])) {
    $storage_import_note = $_REQUEST['storage_import_note'];
}

$storage_import_code = "NKVL".time();
// insert into table
$sql = ' INSERT INTO  tbl_import_supplier
           SET
           id_supplier       ="' . $id_supplier . '",
           storage_import_code       ="' . $storage_import_code . '",
           storage_import_note      = "' . $storage_import_note . '" ';

if ($conn->query($sql)) {
    $id_import = mysqli_insert_id($conn);
    
    if (! empty($id_material) && count($id_material) > 0) {
        for ($i = 0; $i < count($id_material); $i ++) {
            if (! empty($id_material[$i]) && ! empty($quantity_material[$i])) {
                
                $sql_import_item_material = 'INSERT INTO tbl_import_supplier_material
                                         SET
                                         id_import = "' . $id_import . '",
                                         id_material = "' . $id_material[$i] . '",
                                         import_quantity = "' . $quantity_material[$i] . '" ';
                mysqli_query($conn, $sql_import_item_material);
                
                //check material storeage
                $sql_check_material_storeage = "SELECT * FROM tbl_storage_material WHERE id_material = '".$id_material[$i]."'";
                $result_check_material_storeage = mysqli_query($conn, $sql_check_material_storeage);
                $num_result_check_material_storeage = mysqli_num_rows($result_check_material_storeage);
                
                $old_value_storeage = '0';
                
                if ($num_result_check_material_storeage > 0) {
                    while ($rowItemMaterialStoreage = $result_check_material_storeage->fetch_assoc()) {
                        $old_value_storeage = $rowItemMaterialStoreage['storage_quantity'];
                    }
                    // update new value storeage
                    $new_value_storeage = $old_value_storeage + $quantity_material[$i];
                    
                    $sql_update_new_value_storeage = "UPDATE tbl_storage_material SET
                            storage_quantity = '" . $new_value_storeage . "'
                            WHERE id_material = '" . $id_material[$i] . "'
                    ";
                    mysqli_query($conn, $sql_update_new_value_storeage);
                }else{
                    //create new value product storeage
                    $new_value_storeage =  $quantity_material[$i];
                    
                    $sql_update_new_value_storeage = "INSERT INTO tbl_storage_material SET
                            storage_quantity = '" . $new_value_storeage . "',
                            id_material = '" . $id_material[$i] . "'
                    ";
                    mysqli_query($conn, $sql_update_new_value_storeage);
                }
            }
        }
    }
    returnSuccess("Nhập kho NVL thành công!");
}else{
    returnError("Nhập kho NVL không thành công!");
}
















