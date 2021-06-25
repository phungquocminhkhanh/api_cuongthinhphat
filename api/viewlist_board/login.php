<?php
if (isset($_REQUEST['username'])) {
    if ($_REQUEST['username'] == '') {
        unset($_REQUEST['username']);
    }
}
if (! isset($_REQUEST['username'])) {
    returnError("Nhập username!");
}

if (isset($_REQUEST['password'])) {
    if ($_REQUEST['password'] == '') {
        unset($_REQUEST['password']);
    }
}

if (! isset($_REQUEST['password'])) {
    returnError("Nhập password!");
}

$username = $_REQUEST['username'];
$password = $_REQUEST['password'];
$result_arr = array();

// check login employee
$sql_check_employee_exists = "SELECT tbl_admin_account.id as id,
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
    
                                WHERE BINARY  tbl_admin_account.account_username = '" . $username . "'";

$result_check_employee_exists = mysqli_query($conn, $sql_check_employee_exists);
$num_result_check_employee_exists = mysqli_num_rows($result_check_employee_exists);

if ($num_result_check_employee_exists > 0) {
    while ($rowItemEmployee = $result_check_employee_exists->fetch_assoc()) {
        if ($rowItemEmployee['password'] == md5($password)) {
            
            // if users account banned
            if ($rowItemEmployee['status_employee'] == 'N') {
                returnError("Tài khoản này đã bị khóa!");
            }
            
            $query = "UPDATE tbl_admin_account SET ";
            $query .= " force_sign_out  = '0' WHERE id = '".$rowItemEmployee['id']."'";
            $conn->query($query);
            
            $employee_item = array(
                'id' => $rowItemEmployee['id'],
                'username' => $rowItemEmployee['username'],
                'full_name' => $rowItemEmployee['full_name'],
                'email' => $rowItemEmployee['email'],
                'phone_number' => $rowItemEmployee['phone_number'],
                'status_employee' => $rowItemEmployee['status_employee'],
                'id_type' => $rowItemEmployee['id_type'],
                'type_account' => $rowItemEmployee['type_account'],
                'type_description' => $rowItemEmployee['type_description'],
                'login_type' => 'employee',
                'role_permission' => getRolePermission($rowItemEmployee['id'],$conn)
            );
            
            $result_arr['success'] = 'true';
            $result_arr['data'] = array(
                $employee_item
            );
            
            echo json_encode($result_arr);
            
            exit();
        } else {
            returnError("Sai mật khẩu!");
        }
    }
} else {
    // login tbl_account_customer
    //id	phone_active	full_name	email	address	password	phone_number	sex	birthday	nationality
    
    $sql = "SELECT
                    *
        
            FROM  tbl_customer_customer
            WHERE customer_phone = '" . $username . "'
           ";
    $result = mysqli_query($conn, $sql);
    
    $num_row = mysqli_num_rows($result);
    
    if ($num_row > 0) {
        
        while ($row = $result->fetch_assoc()) {
            if ($row['customer_password'] == md5($password)) {
                
                // if users account banned
                //                 if ($row['status'] == 'N') {
                //                     returnError("Tài khoản này đã bị khóa!");
                //                 }
                
                $query = "UPDATE tbl_customer_customer SET ";
                $query .= " force_sign_out  = '0' WHERE id = '".$row['id']."'";
                $conn->query($query);
                
                $user_item = array(
                    'id' => $row['id'],
                    'customer_phone' => $row['customer_phone'],
                    'customer_code' => $row['customer_code'],
                    'customer_enterprise' => $row['customer_enterprise'],
                    'customer_company' => $row['customer_company'],
                    'customer_name' => $row['customer_name'],
                    'customer_phone' => $row['customer_phone'],
                    'customer_email' => $row['customer_email'],
                    'customer_address' => $row['customer_address']!=null?$row['customer_address']:"",
                    'login_type' => 'customer'
                );
                
                $result_arr['success'] = 'true';
                $result_arr['data'] = array(
                    $user_item
                );
                
                echo json_encode($result_arr);
                
                exit();
            } else {
                returnError("Sai mật khẩu!");
            }
        }
    } else {
        returnError("Tài khoản đăng nhập không tồn tại!");
    }
}

