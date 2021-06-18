<?php
if (isset($_REQUEST['type_manager'])) {
    if ($_REQUEST['type_manager'] == '') {
        unset($_REQUEST['type_manager']);
    }
}

if (! isset($_REQUEST['type_manager'])) {
    returnError("type_manager is missing!");
}

$typeManager = $_REQUEST['type_manager'];

switch ($typeManager) {
    case 'list_material':
        include_once './viewlist_board/get_material_list.php';
        break;
        
    case 'create_material':
        
        $id_supplier = '';
        if (isset($_REQUEST['id_supplier']) && ! empty($_REQUEST['id_supplier'])) {
            $id_supplier = $_REQUEST['id_supplier'];
        } else {
            returnError("Nhập nhà cung cấp!");
        }
        $id_unit = '';
        if (isset($_REQUEST['id_unit']) && ! empty($_REQUEST['id_unit'])) {
            $id_unit = $_REQUEST['id_unit'];
        } else {
            returnError("Nhập đơn vị!");
        }
        $material_name = '';
        if (isset($_REQUEST['material_name']) && ! empty($_REQUEST['material_name'])) {
            $material_name = $_REQUEST['material_name'];
        } else {
            returnError("Nhập tên nguyên vật liệu!");
        }
        $material_code = '';
        if (isset($_REQUEST['material_code']) && ! empty($_REQUEST['material_code'])) {
            $material_code = $_REQUEST['material_code'];
            
            $sql_check_material_code_exists = "SELECT * FROM tbl_material_material WHERE material_code = '" . $material_code . "'
            ";
            $result_check = $conn->query($sql_check_material_code_exists);
            $num_result_check = mysqli_num_rows($result_check);
            if ($num_result_check > 0) {
                returnError("Mã nguyên vật liệu đã tồn tại!");
            }
        } else {
            returnError("Nhập mã nguyên vật liệu!");
        }
        $material_spec = '';
        if (isset($_REQUEST['material_spec']) && ! empty($_REQUEST['material_spec'])) {
            $material_spec = $_REQUEST['material_spec'];
        } else {
            returnError("Nhập quy cách nguyên vật liệu!");
        }
        $safety_stock = '';
        if (isset($_REQUEST['safety_stock']) && ! empty($_REQUEST['safety_stock'])) {
            $safety_stock = $_REQUEST['safety_stock'];
        } else {
            returnError("Nhập an toàn kho!");
        }
        
        $sql_create_material = "INSERT INTO tbl_material_material SET
                id_supplier = '" . $id_supplier . "',
                id_unit = '" . $id_unit . "',
                material_name = '" . $material_name . "',
                material_code = '" . $material_code . "',
                material_spec = '" . $material_spec . "',
                safety_stock = '" . $safety_stock . "'
        ";
        
        if ($conn->query($sql_create_material)) {
            returnSuccess("Thêm nguyên vật liệu thành công!");
        } else {
            returnError("Thêm nguyên vật liệu không thành công!");
        }
        
        break;
        
    case 'update_material':
        
        $id_material = '';
        if (isset($_REQUEST['id_material']) && ! empty($_REQUEST['id_material'])) {
            $id_material = $_REQUEST['id_material'];
        } else {
            returnError("Nhập id_material!");
        }
        
        $check = 0;
        
        if (isset($_REQUEST['material_code']) && ! empty($_REQUEST['material_code'])) {
            
            $material_code = $_REQUEST['material_code'];
            
            // check employee_code exists
            $sql_check_material_code = "SELECT * FROM tbl_material_material WHERE material_code = '" . $material_code . "' AND id != '".$id_material."'
            ";
            $result_check = $conn->query($sql_check_material_code);
            $num_result_check = mysqli_num_rows($result_check);
            if ($num_result_check > 0) {
                returnError("Mã nguyên vật liệu đã tồn tại!");
            }
            
            $check ++;
            $query = "UPDATE tbl_material_material SET ";
            $query .= " material_code  = '" . $material_code . "' ";
            $query .= " WHERE id = '" . $id_material . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            }
        }
        if (isset($_REQUEST['id_supplier']) && ! empty($_REQUEST['id_supplier'])) {
            
            $id_supplier = $_REQUEST['id_supplier'];
            
            $check ++;
            $query = "UPDATE tbl_material_material SET ";
            $query .= " id_supplier  = '" . $id_supplier . "' ";
            $query .= " WHERE id = '" . $id_material . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            }
        }
        if (isset($_REQUEST['id_unit']) && ! empty($_REQUEST['id_unit'])) {
            
            $id_unit = $_REQUEST['id_unit'];
            
            $check ++;
            $query = "UPDATE tbl_material_material SET ";
            $query .= " id_unit  = '" . $id_unit . "' ";
            $query .= " WHERE id = '" . $id_material . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            }
        }
        if (isset($_REQUEST['material_name']) && ! empty($_REQUEST['material_name'])) {
            
            $material_name = $_REQUEST['material_name'];
            
            $check ++;
            $query = "UPDATE tbl_material_material SET ";
            $query .= " material_name  = '" . $material_name . "' ";
            $query .= " WHERE id = '" . $id_material . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            }
        }
        if (isset($_REQUEST['material_spec']) && ! empty($_REQUEST['material_spec'])) {
            
            $material_spec = $_REQUEST['material_spec'];
            
            $check ++;
            $query = "UPDATE tbl_material_material SET ";
            $query .= " material_spec  = '" . $material_spec . "' ";
            $query .= " WHERE id = '" . $id_material . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            }
        }
        if (isset($_REQUEST['safety_stock']) && ! empty($_REQUEST['safety_stock'])) {
            
            $safety_stock = $_REQUEST['safety_stock'];
            
            $check ++;
            $query = "UPDATE tbl_material_material SET ";
            $query .= " safety_stock  = '" . $safety_stock . "' ";
            $query .= " WHERE id = '" . $id_material . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            }
        }
        
        if ($check == 0) {
            returnSuccess("Cập nhật thành công!");
        } else {
            returnError("Cập nhật không thành công!");
        }
        
        break;
        
        
        
    case 'delete_material':
        $id_material = '';
        if (isset($_REQUEST['id_material']) && ! empty($_REQUEST['id_material'])) {
            $id_material = $_REQUEST['id_material'];
        } else {
            returnError("Nhập id_material!");
        }
        
        $sql_check_material_exists = "SELECT * FROM tbl_material_material WHERE id = '" . $id_material . "'";
        
        $result_check = mysqli_query($conn, $sql_check_material_exists);
        $num_result_check = mysqli_num_rows($result_check);
        
        if ($num_result_check > 0) {
            
            $check_production_material = "SELECT *
                            FROM tbl_production_material
                            WHERE  id_material = '" . $id_material . "'
                          ";
            
            $result_check_material = mysqli_query($conn, $check_production_material);
            $num_result_check_material = mysqli_num_rows($result_check_material);
            if ($num_result_check_material > 0) {
                returnError("Nguyên vật liệu không thể xóa, đã có đơn hàng sản xuất!");
            }
            
            $sql_delete_material = "
                            DELETE FROM tbl_material_material
                            WHERE  id = '" . $id_material . "'
                          ";
            if ($conn->query($sql_delete_material)) {
                returnSuccess("Xóa nguyên vật liệu thành công!");
            } else {
                returnError("Xóa nguyên vật liệu không thành công!");
            }
        }
        
        
        break;
        
    case 'filter_':
        
        break;
        
    default:
        returnError("type_manager is not accept!");
        break;
}