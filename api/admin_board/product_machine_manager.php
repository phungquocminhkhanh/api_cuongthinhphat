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
    case 'list_machine':
        include_once './viewlist_board/get_machine_list.php';
        break;
    
    case 'create_machine':
        
        $machine_title = '';
        if (isset($_REQUEST['machine_title'])) {
            if ($_REQUEST['machine_title'] == '') {
                unset($_REQUEST['machine_title']);
                returnError("Nhập tên dây chuyền!");
            } else {
                $machine_title = $_REQUEST['machine_title'];
            }
        }
        
        if (! isset($_REQUEST['machine_title'])) {
            returnError("Nhập tên dây chuyền");
        }
        
        $machine_description = '';
        
        if (isset($_REQUEST['machine_description'])) {
            if ($_REQUEST['machine_description'] == '') {
                unset($_REQUEST['machine_description']);
                returnError("Nhập mô tả!");
            } else {
                $machine_description = $_REQUEST['machine_description'];
            }
        }
        
        if (! isset($_REQUEST['machine_description'])) {
            returnError("Nhập mô tả");
        }
        
        $sql_check_machine_exists = "SELECT * FROM tbl_production_machine WHERE machine_title = '" . $machine_title . "'";
        
        $result_check = mysqli_query($conn, $sql_check_machine_exists);
        $num_result_check = mysqli_num_rows($result_check);
        
        if ($num_result_check > 0) {
            returnError("Dây chuyền đã tồn tại!");
        }
        
        $sql_create_machine = "
            INSERT INTO tbl_production_machine SET
             machine_title           = '" . $machine_title . "',
             machine_description           = '" . $machine_description . "'
                 
        ";
        
        if ($conn->query($sql_create_machine)) {
            returnSuccess("Tạo dây chuyền thành công!");
        } else {
            returnError("Tạo dây chuyền không thành công!");
        }
        
        break;
    
    case 'update_machine':
        
        if (isset($_REQUEST['id_machine'])) {
            if ($_REQUEST['id_machine'] == '') {
                unset($_REQUEST['id_machine']);
                returnError("Nhập id_machine");
            }
        }
        
        if (! isset($_REQUEST['id_machine'])) {
            returnError("Nhập id_machine");
        }
        
        $id_machine = $_REQUEST['id_machine'];
        
        $sql_check_machine_exists = "SELECT * FROM tbl_production_machine WHERE id = '" . $id_machine . "'";
        
        $result_check = mysqli_query($conn, $sql_check_machine_exists);
        $num_result_check = mysqli_num_rows($result_check);
        
        if ($num_result_check > 0) {
            
            $check = 0;
            
            if (isset($_REQUEST['machine_title']) && ! empty($_REQUEST['machine_title'])) {
                $machine_title = $_REQUEST['machine_title'];
                $sql_check_machine_title_exists = "SELECT * FROM tbl_production_machine WHERE machine_title = '" . $machine_title . "' AND id != '" . $id_machine . "'";
                
                $result_check_title = mysqli_query($conn, $sql_check_machine_title_exists);
                $num_result_check_title = mysqli_num_rows($result_check_title);
                
                if ($num_result_check_title > 0) {
                    returnError("Tên dây chuyền đã tồn tại!");
                }
                
                $check ++;
                $query = "UPDATE tbl_production_machine SET ";
                $query .= "machine_title  = '" . $machine_title . "' ";
                $query .= "WHERE id = '" . $id_machine . "'";
                // Create post
                if ($conn->query($query)) {
                    $check --;
                } else {
                    returnError("Cập nhật tên dây chuyền không thành công!");
                }
            }
            
            if (isset($_REQUEST['machine_description']) && ! empty($_REQUEST['machine_description'])) {
                
                $check ++;
                $query = "UPDATE tbl_production_machine SET ";
                $query .= "machine_description  = '" . $_REQUEST['machine_description'] . "' ";
                $query .= "WHERE id = '" . $id_machine . "'";
                // Create post
                if ($conn->query($query)) {
                    $check --;
                } else {
                    returnError("Cập nhật machine_description không thành công!");
                }
            }
            
            if ($check == 0) {
                returnSuccess("Cập nhật dây chuyền thành công!");
            } else {
                returnError("Cập nhật dây chuyền không thành công!");
            }
        } else {
            returnError("Không tìm thấy dây chuyền!");
        }
        
        break;
    
    case 'delete_machine':
        
        if (isset($_REQUEST['id_machine'])) {
            if ($_REQUEST['id_machine'] == '') {
                unset($_REQUEST['id_machine']);
                returnError("Nhập id_machine");
            }
        }
        
        if (! isset($_REQUEST['id_machine'])) {
            returnError("Nhập id_machine");
        }
        
        $id_machine = $_REQUEST['id_machine'];
        
        $sql_check_machine_exists = "SELECT * FROM tbl_production_machine WHERE id = '" . $id_machine . "'";
        
        $result_check = mysqli_query($conn, $sql_check_machine_exists);
        $num_result_check = mysqli_num_rows($result_check);
        
        if ($num_result_check > 0) {
            
            $sql_check_production = "SELECT * FROM tbl_production_production WHERE id_machine = '" . $id_machine . "'";
            $result_check_production = mysqli_query($conn, $sql_check_production);
            $num_result_check_production = mysqli_num_rows($result_check_production);
            
            if ($num_result_check_production > 0) {
                returnError("Dây chuyền đã tồn tại lệnh sản xuất, không thể xóa!");
            }
            
            $sql_delete_machine = "
                            DELETE FROM tbl_production_machine
                            WHERE  id = '" . $id_machine . "'
                          ";
            if ($conn->query($sql_delete_machine)) {
                returnSuccess("Xóa dây chuyền thành công!");
            } else {
                returnError("Xóa dây chuyền không thành công!");
            }
        } else {
            returnError("Không tìm thấy dây chuyền!");
        }
        break;
    
    default:
        returnError("type_manager is not accept!");
        break;
}

