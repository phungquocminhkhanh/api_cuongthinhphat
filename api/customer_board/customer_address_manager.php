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

$id_customer = '';
if (isset($_REQUEST['id_customer']) &&  !empty($_REQUEST['id_customer'])) {
    $id_customer = $_REQUEST['id_customer'];
}else{
    returnError("Nhập id_customer!");
}

switch ($typeManager) {
    case 'list_customer_address':
        
        $sql = "SELECT * FROM tbl_customer_branch WHERE id_customer = '".$id_customer."'";
        
        $result = $conn->query($sql);
        $num = mysqli_num_rows($result);
        
        $arr_result = array();
        
        $arr_result['success'] = 'true';
        $arr_result['data'] = array();
        
        if ($num > 0) {
            while ($row = $result->fetch_assoc()) {
                $address_item = array(
                    'id' => $row['id'],
                    'branch_title' => $row['branch_title'],
                    'branch_phone' => $row['branch_phone'],
                    'branch_address' => $row['branch_address']
                );
                
                // Push to "data"
                array_push($arr_result['data'], $address_item);
            }
        }
        
        // Turn to JSON & output
        echo json_encode($arr_result);
        
        break;
        
    case 'create_customer_address':
        
        $branch_title = '';
        if (isset($_REQUEST['branch_title']) &&  !empty($_REQUEST['branch_title'])) {
            $branch_title = $_REQUEST['branch_title'];
            
            $sql_check_branch_title_exists = "SELECT
                *
            FROM tbl_customer_branch
                
            WHERE branch_title = '" . $branch_title . "'
           ";
            
            $result_check_branch_title_exists = mysqli_query($conn, $sql_check_branch_title_exists);
            $num_check_branch_title_exists = mysqli_num_rows($result_check_branch_title_exists);
            if ($num_check_branch_title_exists > 0) {
                returnError("Tên chi nhánh đã tồn tại!");
            }
            
        }else{
            returnError("Nhập tên chi nhánh!");
        }
        $branch_phone = '';
        if (isset($_REQUEST['branch_phone']) &&  !empty($_REQUEST['branch_phone'])) {
            $branch_phone = $_REQUEST['branch_phone'];
        }else{
            returnError("Nhập số điện thoại!");
        }
        $branch_address = '';
        if (isset($_REQUEST['branch_address']) &&  !empty($_REQUEST['branch_address'])) {
            $branch_address = $_REQUEST['branch_address'];
        }else{
            returnError("Nhập địa chỉ!");
        }
        
        $sql_create_customer_branch = "INSERT INTO tbl_customer_branch SET
              branch_title = '" . $branch_title . "'
              , branch_phone = '" . $branch_phone . "'
              , branch_address = '" . $branch_address . "'
              , id_customer = '" . $id_customer . "'
        ";
        
        if ($conn->query($sql_create_customer_branch)) {
            returnSuccess("Thêm địa chỉ thành công!");
        }else{
            returnError("Thêm địa chỉ không thành công!");
        }
        
        break;
        
    case 'update_customer_address':
        
        $id_address = '';
        if (isset($_REQUEST['id_address']) &&  !empty($_REQUEST['id_address'])) {
            $id_address = $_REQUEST['id_address'];
        }else{
            returnError("Nhập id_address!");
        }
        
        $check = 0;
        
        if (isset($_REQUEST['branch_title']) && ! empty($_REQUEST['branch_title'])) {
            
            $branch_title = $_REQUEST['branch_title'];
            
            $sql_check_branch_title_exists = "SELECT
                *
            FROM tbl_customer_branch
                
            WHERE branch_title = '" . $branch_title . "' AND id_customer != '".$id_customer."'
            ";
            
            $result_check_branch_title_exists = mysqli_query($conn, $sql_check_branch_title_exists);
            $num_check_branch_title_exists = mysqli_num_rows($result_check_branch_title_exists);
            if ($num_check_branch_title_exists > 0) {
                returnError("Tên chi nhánh đã tồn tại!");
            }
            
            $check ++;
            $query = "UPDATE tbl_customer_branch SET ";
            $query .= " branch_title  = '" . $branch_title . "' ";
            $query .= " WHERE id = '" . $id_address . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            }
        }
        
        if (isset($_REQUEST['branch_address']) && ! empty($_REQUEST['branch_address'])) {
            $check ++;
            $query = "UPDATE tbl_customer_branch SET ";
            $query .= " branch_address  = '" . $_REQUEST['branch_address'] . "' ";
            $query .= " WHERE id = '" . $id_address . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            }
        }
        if (isset($_REQUEST['branch_phone']) && ! empty($_REQUEST['branch_phone'])) {
            $check ++;
            $query = "UPDATE tbl_customer_branch SET ";
            $query .= " branch_phone  = '" . $_REQUEST['branch_phone'] . "' ";
            $query .= " WHERE id = '" . $id_address . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            }
        }
        
        if ($check == 0) {
            returnSuccess("Cập nhật thành công!");
        } else {
            returnError("Cập nhật không thành công");
        }
        
        break;
        
    case 'delete_customer_address':
        
        $id_address = '';
        if (isset($_REQUEST['id_address']) &&  !empty($_REQUEST['id_address'])) {
            $id_address = $_REQUEST['id_address'];
        }else{
            returnError("Nhập id_address!");
        }
        
        $sql_check_customer_branch_exists = "SELECT * FROM tbl_customer_branch WHERE id = '" . $id_address . "'";
        
        $result_check = mysqli_query($conn, $sql_check_customer_branch_exists);
        $num_result_check = mysqli_num_rows($result_check);
        
        if ($num_result_check > 0) {
            $sql_delete_customer_branch = "
                            DELETE FROM tbl_customer_branch
                            WHERE  id = '" . $id_address . "'
                          ";
            if ($conn->query($sql_delete_customer_branch)) {
                returnSuccess("Xóa địa chỉ thành công!");
            } else {
                returnError("Xóa địa chỉ không thành công!");
            }
        }
        
        break;
        
    case 'filter_':
        
        break;
        
    default:
        returnError("type_manager is not accept!");
        break;
}