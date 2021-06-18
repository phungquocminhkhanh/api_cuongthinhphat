<?php
$id_customer = '';
if (isset($_REQUEST['id_customer']) && ! empty($_REQUEST['id_customer'])) {
    $id_customer = $_REQUEST['id_customer'];
} else {
    returnError("Nhập id_customer!");
}

$full_name = '';
if (isset($_REQUEST['customer_name']) && ! empty($_REQUEST['customer_name'])) {
    $full_name = $_REQUEST['customer_name'];
}
$customer_code = '';
if (isset($_REQUEST['customer_code']) && ! empty($_REQUEST['customer_code'])) {
    $customer_code = $_REQUEST['customer_code'];
}
$email = '';
if (isset($_REQUEST['email']) && ! empty($_REQUEST['email'])) {
    $email = $_REQUEST['email'];
}
$sex = '';
if (isset($_REQUEST['sex']) && ! empty($_REQUEST['sex'])) {
    $sex = $_REQUEST['sex'];
}
$birthday = '';
if (isset($_REQUEST['birthday']) && ! empty($_REQUEST['birthday'])) {
    $birthday = $_REQUEST['birthday'];
}

if (isset($_REQUEST['password'])) {
    if ($_REQUEST['password'] == '') {
        unset($_REQUEST['password']);
    }
}
$user_change_password = 0;
if (isset($_REQUEST['old_password'])) {
    if ($_REQUEST['old_password'] == '') {
        unset($_REQUEST['old_password']);
    } else {
        $user_change_password = 1;
        $sql = 'SELECT * FROM tbl_customer_customer WHERE id = ' . $id_customer . ' ';
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_array($result)) {
            if ($row['customer_password'] != md5($_REQUEST['old_password'])) {
                echo json_encode(array(
                    'success' => 'false',
                    'message' => 'Mật khẩu cũ không chính xác!'
                ));
                exit();
            }
        }
    }
}

$check = 0;

if (isset($customer_code) && ! empty($customer_code)) {
    
    // check employee_code exists
    $sql_check_customer_code = "SELECT * FROM tbl_customer_customer WHERE customer_code = '" . $customer_code . "' AND id != '".$id_customer."'
            ";
    $result_check_customer_code = $conn->query($sql_check_customer_code);
    $num_result_check_customer_code = mysqli_num_rows($result_check_customer_code);
    if ($num_result_check_customer_code > 0) {
        returnError("Mã khách hàng đã tồn tại!");
    }
    
    $check ++;
    $query = "UPDATE tbl_customer_customer SET ";
    $query .= " customer_code  = '" . $customer_code . "' ";
    $query .= " WHERE id = '" . $id_customer . "'";
    // Create post
    if ($conn->query($query)) {
        $check --;
    } else {
        returnError("Cập nhật mã khách hàng không thành công!");
    }
}

if (isset($full_name) && ! empty($full_name)) {
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

if (isset($sex) && ! empty($sex)) {
    $check ++;
    $query = "UPDATE tbl_customer_customer SET ";
    $query .= " customer_sex  = '" . $sex . "' ";
    $query .= " WHERE id = '" . $id_customer . "'";
    // Create post
    if ($conn->query($query)) {
        $check --;
    } else {
        returnError("Cập nhật giới tính không thành công!");
    }
}

if (isset($birthday) && ! empty($birthday)) {
    $check ++;
    $query = "UPDATE tbl_customer_customer SET ";
    $query .= " customer_birthday  = '" . $birthday . "' ";
    $query .= " WHERE id = '" . $id_customer . "'";
    // Create post
    if ($conn->query($query)) {
        $check --;
    } else {
        returnError("Cập nhật ngày sinh không thành công!");
    }
}

if (isset($email) && ! empty($email)) {
    $check ++;
    $query = "UPDATE tbl_customer_customer SET ";
    $query .= " customer_email  = '" . $email . "' ";
    $query .= " WHERE id = '" . $id_customer . "'";
    // Create post
    if ($conn->query($query)) {
        $check --;
    } else {
        returnError("Cập nhật email không thành công!");
    }
}

if (isset($_REQUEST['password']) && ! empty($_REQUEST['password']) && $user_change_password == 1) {
    $check ++;
    $query = "UPDATE tbl_customer_customer SET ";
    $query .= "customer_password  = '" . md5(mysqli_real_escape_string($conn, $_REQUEST['password'])) . "' ";
    $query .= "WHERE id = '" . $id_customer . "'";
    // check execute query
    if ($conn->query($query)) {
        $check --;
    } else {
        returnError("Cập nhật customer_password không thành công!");
    }
}

if ($check == 0) {
    // get all user new info
    $result_arr = array();
    
    $sql_get_customer = "SELECT
                   *
            FROM  tbl_customer_customer
            WHERE id = '" . $id_customer . "'
           ";
    $result_get_customer = mysqli_query($conn, $sql_get_customer);
    
    $num_row_result_get_customer = mysqli_num_rows($result_get_customer);
    
    while ($rowItemCustomer = $result_get_customer->fetch_assoc()) {
        
        $user_item = array(
            'id' => $rowItemCustomer['id'],
            'customer_phone' => $rowItemCustomer['customer_phone'],
            'customer_name' => $rowItemCustomer['customer_name'],
            'customer_sex' => $rowItemCustomer['customer_sex'],
            'customer_birthday' => $rowItemCustomer['customer_birthday'],
            'customer_email' => $rowItemCustomer['customer_email'],
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