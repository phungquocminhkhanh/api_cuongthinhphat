<?php

if (isset($_REQUEST['customer_name']) && ! empty($_REQUEST['customer_name'])) {
    $customer_name = $_REQUEST['customer_name'];
} else {
    returnError("Nhập tên đầy đủ!");
}

if (isset($_REQUEST['id_admin']) && ! empty($_REQUEST['id_admin'])) {
    $id_admin = $_REQUEST['id_admin'];
}

if (isset($_REQUEST['customer_password']) && ! empty($_REQUEST['customer_password'])) {
    $customer_password = md5($_REQUEST['customer_password']);
} else {
    returnError("Nhập mật khẩu!");
}

if (isset($_REQUEST['customer_phone']) && ! empty($_REQUEST['customer_phone'])) {
    $customer_phone = addslashes($_REQUEST['customer_phone']);
} else {
    returnError("Nhập customer_phone!");
}

if (isset($_REQUEST['customer_code']) && ! empty($_REQUEST['customer_code'])) {
    $customer_code = addslashes($_REQUEST['customer_code']);
}

if (isset($_REQUEST['customer_email']) && ! empty($_REQUEST['customer_email'])) {
    $customer_email = addslashes($_REQUEST['customer_email']);
}

if (isset($_REQUEST['customer_company']) && ! empty($_REQUEST['customer_company'])) {
    $customer_company = addslashes($_REQUEST['customer_company']);
}

if (isset($_REQUEST['customer_enterprise']) && ! empty($_REQUEST['customer_enterprise'])) {
    $customer_enterprise = $_REQUEST['customer_enterprise'];
}

if (isset($_REQUEST['customer_address']) && ! empty($_REQUEST['customer_address'])) {
    $customer_address = addslashes($_REQUEST['customer_address']);
}else {
    returnError("Nhập customer_address!");
}


// start check customer exists
$sql = "SELECT * FROM tbl_customer_customer WHERE customer_phone = '" . $customer_phone . "'  ";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    returnError("Số điện thoại đăng ký đã tồn tại!");
}
// end check customer_phone

// Create query insert into user

$sql = "
    INSERT INTO tbl_customer_customer
    SET customer_phone           = '$customer_phone'
        ,customer_name          = '$customer_name'
        ,customer_password          = '$customer_password'
        ,customer_address          = '$customer_address'
    ";

if (! empty($customer_company)) {
    $sql .= " , customer_company = '" . $customer_company . "'";
}
if (! empty($id_admin)) {
    $sql .= " , id_admin = '" . $id_admin . "'";
}
if (! empty($customer_enterprise)) {
    $sql .= " , customer_enterprise = '" . $customer_enterprise . "'";
}
if (! empty($customer_email)) {
    $sql .= " , customer_email = '" . $customer_email . "'";
}
if (! empty($customer_code)) {
    $sql .= " , customer_code = '" . $customer_code . "'";
}


// Return customer info just created
if ($conn->query($sql)) {
    $result_arr = array();
    
    $id_created = mysqli_insert_id($conn);
    
    $sql_get_customer = "SELECT
                   *
            FROM  tbl_customer_customer
            WHERE id = '" . $id_created . "'
           ";
    $result_get_customer = mysqli_query($conn, $sql_get_customer);
    
    $num_row_result_get_customer = mysqli_num_rows($result_get_customer);
    
    while ($rowItemCustomer = $result_get_customer->fetch_assoc()) {
        
        $user_item = array(
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
    returnError('Đăng ký không thành công!');
}

?>

