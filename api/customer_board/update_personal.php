<?php
$id_customer = '';
if (isset($_REQUEST['id_customer']) && ! empty($_REQUEST['id_customer'])) {
    $id_customer = $_REQUEST['id_customer'];
} else {
    returnError("Nhập id_customer!");
}



$check = 0;

if (isset($_REQUEST['customer_name']) && ! empty($_REQUEST['customer_name'])) {
    $full_name = addslashes($_REQUEST['customer_name']);
     $check ++;
    $query = "UPDATE tbl_customer_customer SET ";
    $query .= " customer_name  = '" . $full_name . "' ";
    $query .= " WHERE id = '" . $id_customer . "'";
    // Create post
    if ($conn->query($query)) {
        $check --;
    } else {
        returnError("Cập nhật tên đầy đủ không thành công!");
    }
}

if (isset($_REQUEST['customer_company']) && ! empty($_REQUEST['customer_company'])) {
    $customer_company = addslashes($_REQUEST['customer_company']);
     $check ++;
    $query = "UPDATE tbl_customer_customer SET ";
    $query .= " customer_company  = '" . $customer_company . "' ";
    $query .= " WHERE id = '" . $id_customer . "'";
    // Create post
    if ($conn->query($query)) {
        $check --;
    } else {
        returnError("Cập nhật tên công ty không thành công!");
    }
}

if (isset($_REQUEST['customer_email']) && ! empty($_REQUEST['customer_email'])) {
    $customer_email = addslashes($_REQUEST['customer_email']);
     $check ++;
    $query = "UPDATE tbl_customer_customer SET ";
    $query .= " customer_email  = '" . $customer_email . "' ";
    $query .= " WHERE id = '" . $id_customer . "'";
    // Create post
    if ($conn->query($query)) {
        $check --;
    } else {
        returnError("Cập nhật email không thành công!");
    }
}

if (isset($_REQUEST['customer_code']) && ! empty($_REQUEST['customer_code'])) {
    $customer_code = addslashes($_REQUEST['customer_code']);
     $check ++;
    $query = "UPDATE tbl_customer_customer SET ";
    $query .= " customer_code  = '" . $customer_code . "' ";
    $query .= " WHERE id = '" . $id_customer . "'";
    // Create post
    if ($conn->query($query)) {
        $check --;
    } else {
        returnError("Cập nhật email không thành công!");
    }
}

$user_change_password = 0;
if (isset($_REQUEST['old_password']) && ! empty($_REQUEST['old_password'])) {
    $old_password = md5($_REQUEST['old_password']);

    $sql = "SELECT * FROM tbl_customer_customer 
    WHERE id = '$id_customer'
    AND customer_password ='$old_password'
    ";

    $result = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($result);
    if ($num > 0) {
         $user_change_password = 1;
    }else{
        returnError('Mật khẩu cũ không chính xác!');
    }
       
}

if (isset($_REQUEST['password']) && ! empty($_REQUEST['password']) && $user_change_password == 1) {
    if(is_password($_REQUEST['password'])){
        $new_pass = md5($_REQUEST['password']);
        $check ++;
        $customer_password = md5($_REQUEST['password']);
        $query = "UPDATE tbl_customer_customer SET ";
        $query .= "customer_password  = '$customer_password' ";
        $query .= "WHERE id = '" . $id_customer . "'";
        // check execute query
        if ($conn->query($query)) {
            $check --;
        } else {
            returnError("Cập nhật mật khẩu không thành công!");
        }
    }else{
        returnError("Mật khẩu không đúng định dạng !");
    }
}


if (isset($_REQUEST['customer_name']) && ! empty($_REQUEST['customer_name'])) {
    $full_name = addslashes($_REQUEST['customer_name']);
     $check ++;
    $query = "UPDATE tbl_customer_customer SET ";
    $query .= " customer_name  = '" . $full_name . "' ";
    $query .= " WHERE id = '" . $id_customer . "'";
    // Create post
    if ($conn->query($query)) {
        $check --;
    } else {
        returnError("Cập nhật tên đầy đủ không thành công!");
    }
}

if ($check == 0) {
    // get all user new info
    $result_arr = array();
    
    $sql_get_customer = "SELECT *
            FROM  tbl_customer_customer
            WHERE id = '" . $id_customer . "'
           ";
    $result_get_customer = mysqli_query($conn, $sql_get_customer);
    
    $num_row_result_get_customer = mysqli_num_rows($result_get_customer);
    
    while ($rowItemCustomer = $result_get_customer->fetch_assoc()) {
        
        $user_item =  array(
            'id' => $rowItemCustomer['id'],
            'customer_phone' => $rowItemCustomer['customer_phone'],
            'customer_name' => $rowItemCustomer['customer_name'],
            'customer_register' => $rowItemCustomer['customer_register'],
            'customer_address' => $rowItemCustomer['customer_address'],
            'customer_status' => $rowItemCustomer['customer_status'],
            'customer_code' => $rowItemCustomer['customer_code'] != null ? $rowItemCustomer['customer_code'] : "",
            'customer_enterprise' => $rowItemCustomer['customer_enterprise'] != null ? $rowItemCustomer['customer_enterprise'] : "",
            'id_admin' => $rowItemCustomer['id_admin'] != null ? $rowItemCustomer['id_admin'] : "",
            'customer_email' => $rowItemCustomer['customer_email'] != null ? $rowItemCustomer['customer_email'] : "",
             'customer_company' => $rowItemCustomer['customer_company'] != null ? $rowItemCustomer['customer_company'] : "",
            'login_type' => 'customer'
        );
        
        $result_arr['success'] = 'true';
        $result_arr['data'] = array(
            $user_item
        );
        echo json_encode($result_arr);
        exit();
    }
} else {
    returnError("Cập nhật thông tin không thành công!");
}

?>