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
    case 'list_role':
        include_once 'get_role_permission.php';
        break;
        
    case 'create_role':
        
        break;
        
    case 'update_role':
        
        if (isset($_REQUEST['id_role']) && ! empty($_REQUEST['id_role'])) {
            $id_role = $_REQUEST['id_role'];
        } else {
            returnError("Nhập id_role");
        }
        
        $check = 0;
        
        if (isset($_REQUEST['permission'])) {
            if ($_REQUEST['permission'] == '') {
                unset($_REQUEST['permission']);
            } else {
                $check ++;
                $query = "UPDATE tbl_admin_permission SET ";
                $query .= "permission  = '" . $_REQUEST['permission'] . "' ";
                $query .= "WHERE id = '" . $id_role . "'";
                // Create post
                if ($conn->query($query)) {
                    $check --;
                } else {
                    returnError("Cập nhật tên module không thành công! ");
                }
            }
        }
        
        if (isset($_REQUEST['description'])) {
            if ($_REQUEST['description'] == '') {
                unset($_REQUEST['description']);
            } else {
                $check ++;
                $query = "UPDATE tbl_admin_permission SET ";
                $query .= "description  = '" . $_REQUEST['description'] . "' ";
                $query .= "WHERE id = '" . $id_role . "'";
                // Create post
                if ($conn->query($query)) {
                    $check --;
                } else {
                    returnError("Cập nhật mô tả module không thành công! ");
                }
            }
        }
        
        if ($check == 0) {
            
            //             $title = "Cáº­p nháº­t phÃ¢n quyá»�n!!!";
            //             $bodyMessage = 'Cáº­p nháº­t phÃ¢n quyá»�n';
            //             $action = "update_role_permission";
            
            //             $type_send = 'topic';
            
            //             $to = 'update_role_permission';
            
            //             //             $server_key = 'AAAANe6uSSo:APA91bHDyrTLbSRuMI4xUbsIQvbpQGZ9O0XIIzpdODSbitiztgK2zHvQa2X2JkFf21VrvLmNTYAmoptJWy4bUcK4YP94KJB0NrosT355gvopYRlCmvmN_uUwyrCab9sIyvVy-NONyGsh';
            //             pushNotification($title, $bodyMessage, $action, $to, $type_send);
            
            returnSuccess("Cập nhật module thành công!");
        } else {
            returnError("Cập nhật module không thành công!");
        }
        
        break;
        
    case 'check_role':
        
        if (isset($_REQUEST['id_user']) && ! empty($_REQUEST['id_user'])) {
            $id_user = $_REQUEST['id_user'];
        } else {
            returnError("Nhập id_user");
        }
        
        $sql = "SELECT tbl_admin_account.id as id,
                                     tbl_admin_account.account_username as username,
                                     tbl_admin_account.account_fullname as full_name,
                                     tbl_admin_account.account_phone as phone_number,
                                     tbl_admin_account.account_email as email,
                                     tbl_admin_account.account_password as password,
                                     tbl_admin_account.account_status as status_employee,
            
                                     tbl_admin_account.id_type as id_type,
                                     tbl_admin_type.type_account as type_account,
                                     tbl_admin_type.description as type_description
            
                                 FROM tbl_admin_account
                                 LEFT JOIN tbl_admin_type
                                 ON tbl_admin_type.id = tbl_admin_account.id_type
            
            WHERE tbl_admin_account.id = '" . $id_user . "'
           ";
        
        $result = mysqli_query($conn, $sql);
        $num_result = mysqli_num_rows($result);
        
        $arr_result = array();
        $arr_result['success'] = "true";
        $arr_result['data'] = array();
        
        if ($num_result > 0) {
            
            while ($row = $result->fetch_assoc()) {
                
                // if users account banned
                if ($row['status_employee'] == 'N') {
                    returnError("Tài khoản này đã bị khóa!");
                }
                
                $user_item = array(
                    'id' => $row['id'],
                    'username' => $row['username'],
                    'full_name' => $row['full_name'],
                    'email' => $row['email'],
                    'phone_number' => $row['phone_number'],
                    'status_employee' => $row['status_employee'],
                    'id_type' => $row['id_type'],
                    'type_account' => $row['type_account'],
                    'type_description' => $row['type_description'],
                    'login_type' => 'employee',
                    'role_permission' => getRolePermission($row['id'],$conn)
                );
                
                array_push($arr_result['data'], $user_item);
                
                
            }
        }
        echo json_encode($arr_result);
        
        break;
        
    case 'delete_role':
        
        if (isset($_REQUEST['id_role']) && ! empty($_REQUEST['id_role'])) {
            $id_role = $_REQUEST['id_role'];
        } else {
            returnError("Nhập id_role");
        }
        
        $sql_check_role_exists = "SELECT * FROM tbl_admin_permission WHERE id = '" . $id_role. "'
        ";
        
        $result = $conn->query($sql_check_role_exists);
        
        $num = mysqli_num_rows($result);
        
        if ($num > 0) {
            
            $query = "DELETE FROM tbl_admin_permission ";
            $query .= "WHERE id = '" . $id_role. "'";
            // Create post
            if ($conn->query($query)) {
                returnSuccess("Xóa module thành công!");
            } else {
                returnError("Xóa module không thành công!");
            }
            
            
        }else{
            returnError("Module không tồn tại!");
        }
        
        break;
        
    case 'filter_role':
        
        break;
        
    default:
        returnError("type_manager is not accept!");
        break;
}