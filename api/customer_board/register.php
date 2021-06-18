<?php
$customer_name = '';
if (isset($_REQUEST['customer_name']) && ! empty($_REQUEST['customer_name'])) {
    $customer_name = $_REQUEST['customer_name'];
} else {
    returnError("Nhập tên đầy đủ!");
}

$password = '';
if (isset($_REQUEST['password']) && ! empty($_REQUEST['password'])) {
    $password = md5($_REQUEST['password']);
} else {
    returnError("Nhập mật khẩu!");
}

$customer_phone = '';
if (isset($_REQUEST['customer_phone']) && ! empty($_REQUEST['customer_phone'])) {
    $customer_phone = $_REQUEST['customer_phone'];
} else {
    returnError("Nhập customer_phone!");
}

$customer_code = '';
if (isset($_REQUEST['customer_code']) && ! empty($_REQUEST['customer_code'])) {
    $customer_code = $_REQUEST['customer_code'];
}
$customer_sex = '';
if (isset($_REQUEST['sex']) && ! empty($_REQUEST['sex'])) {
    $customer_sex = $_REQUEST['sex'];
}
$customer_birthday = '';
if (isset($_REQUEST['birthday']) && ! empty($_REQUEST['birthday'])) {
    $customer_birthday = $_REQUEST['birthday'];
}
$customer_email = '';
if (isset($_REQUEST['email']) && ! empty($_REQUEST['email'])) {
    $customer_email = $_REQUEST['email'];
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
    SET customer_phone           = '" . $customer_phone . "',
        customer_name          = '" . $customer_name . "'
    ";

if (! empty($password)) {
    $sql .= " , customer_password = '" . $password . "'";
}
if (! empty($customer_code)) {
    $sql .= " , customer_code = '" . $customer_code . "'";
}
if (! empty($customer_sex)) {
    $sql .= " , customer_sex = '" . $customer_sex . "'";
}
if (! empty($customer_birthday)) {
    $sql .= " , customer_birthday = '" . $customer_birthday . "'";
}
if (! empty($customer_email)) {
    $sql .= " , customer_email = '" . $customer_email . "'";
}

// echo $sql;
// exit;

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
            'customer_code' => $rowItemCustomer['customer_code'] != null ? $rowItemCustomer['customer_code'] : "",
            'customer_sex' => $rowItemCustomer['customer_sex'] != null ? $rowItemCustomer['customer_sex'] : "",
            'customer_birthday' => $rowItemCustomer['customer_birthday'] != null ? $rowItemCustomer['customer_birthday'] : "",
            'customer_email' => $rowItemCustomer['customer_email'] != null ? $rowItemCustomer['customer_email'] : "",
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
    echo json_encode(array(
        'success' => 'false',
        'message' => 'Đăng ký không thành công!'
    ));
}

?>

