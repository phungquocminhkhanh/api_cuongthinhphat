<?php
if (isset($_REQUEST['customer_phone'])) {
    if ($_REQUEST['customer_phone'] == '') {
        unset($_REQUEST['customer_phone']);
    }
}

if (isset($_REQUEST['new_password'])) {
    if ($_REQUEST['new_password'] == '') {
        unset($_REQUEST['new_password']);
    }
}

if (! isset($_REQUEST['customer_phone'])) {
    echo json_encode(array(
        'success' => 'false',
        'message' => 'Nhập số điện thoại đăng ký!'
    ));
    exit();
}

if (! isset($_REQUEST['new_password'])) {
    echo json_encode(array(
        'success' => 'false',
        'message' => 'Nhập mật khẩu mới !'
    ));
    exit();
}

if (isset($_REQUEST['new_password'])) {
    $query = "UPDATE tbl_customer_customer SET ";
    $query .= "customer_password = '" . md5(mysqli_real_escape_string($conn, $_REQUEST['new_password'])) . "' ";
    $query .= "WHERE customer_phone = '" . mysqli_real_escape_string($conn, $_REQUEST['customer_phone']) . "'";
    // check execute query
    if ($conn->query($query)) {
        // get all user new info
        
        $sql = "SELECT * FROM tbl_customer_customer WHERE customer_phone = '" . $_REQUEST['customer_phone'] . "'
        ";
        $result = $conn->query($sql);
        $user_arr = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $user_item = array(
                    'id' => $row['id'],
                    'customer_phone' => $row['customer_phone'],
                    'customer_name' => $row['customer_name'],
                    'login_type' => 'customer'
                );
                
                // end get all user new info
                
                $user_arr['success'] = 'true';
                $user_arr['data'] = array(
                    $user_item
                );
                echo json_encode($user_arr);
            }
            
        } else {
            returnError("Không tìm thấy thông tin khách hàng!");
        }
    } else {
        returnError("Cập nhật quên mật khẩu không thành công!");
    }
}

?>