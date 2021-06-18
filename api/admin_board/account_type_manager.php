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
    case 'list_account_type':
        include_once './viewlist_board/list_account_type.php';
        break;
        
    case 'create_account_type':
        $type_account = '';
        if (isset($_REQUEST['type_account']) && ! empty($_REQUEST['type_account'])) {
            $type_account = $_REQUEST['type_account'];
        } else {
            returnError("Nhập cấp độ quản trị!");
        }
        
        $description = '';
        if (isset($_REQUEST['description']) && ! empty($_REQUEST['description'])) {
            $description = $_REQUEST['description'];
        }else{
            returnError("Nhập mô tả!");
        }
        
        $sql_create_account_type = "INSERT INTO tbl_admin_type SET
                type_account = '" . $type_account . "',
                description = '" . $description . "'
        ";
        
        if ($conn->query($sql_create_account_type)) {
            returnSuccess("Thêm loại tài khoản thành công!");
        } else {
            returnError("Thêm loại tài khoản không thành công!");
        }
        
        break;
        
    case 'update_account_type':
        
        $id_type = '';
        if (isset($_REQUEST['id_type']) && ! empty($_REQUEST['id_type'])) {
            $id_type = $_REQUEST['id_type'];
        } else {
            returnError("Nhập id_type!");
        }
        
        $check = 0;
        
        if (isset($_REQUEST['type_account']) && ! empty($_REQUEST['type_account'])) {
            $check ++;
            $query = "UPDATE tbl_admin_type SET ";
            $query .= " type_account  = '" . $_REQUEST['type_account'] . "' ";
            $query .= " WHERE id = '" . $id_type . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            }
        }
        if (isset($_REQUEST['description']) && ! empty($_REQUEST['description'])) {
            $check ++;
            $query = "UPDATE tbl_admin_type SET ";
            $query .= " description  = '" . $_REQUEST['description'] . "' ";
            $query .= " WHERE id = '" . $id_type . "'";
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
        
    case 'delete_account_type':
        $id_type = '';
        if (isset($_REQUEST['id_type']) && ! empty($_REQUEST['id_type'])) {
            $id_type = $_REQUEST['id_type'];
        } else {
            returnError("Nhập id_type!");
        }
        
        $sql_check_level_exists = "SELECT * FROM tbl_admin_type WHERE id = '" . $id_type . "'";
        
        $result_check = mysqli_query($conn, $sql_check_level_exists);
        $num_result_check = mysqli_num_rows($result_check);
        
        if ($num_result_check > 0) {
            
            $sql_check_employee = "
                            SELECT *
                            FROM  tbl_admin_account
                            WHERE  id_type = '" . $id_type . "'
                          ";
            
            $result_check_employee = mysqli_query($conn, $sql_check_employee);
            $num_result_check_employee = mysqli_num_rows($result_check_employee);
            if ($num_result_check_employee >0 ){
                returnError("Không thể xóa loại tài khoản khi đã có nhân viên!");
            }
            
            
            $sql_delete_level = "
                            DELETE FROM tbl_admin_type
                            WHERE  id = '" . $id_type . "'
                          ";
            if ($conn->query($sql_delete_level)) {
                returnSuccess("Xóa loại tài khoản thành công!");
            } else {
                returnError("Xóa loại tài khoản không thành công!");
            }
        } else {
            returnError("Không tìm thấy loại tài khoản!");
        }
        
        break;
        
    case 'filter_':
        
        break;
        
    default:
        returnError("type_manager is not accept!");
        break;
}