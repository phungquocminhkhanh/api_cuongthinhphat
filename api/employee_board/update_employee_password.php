<?php
$typeManager = '';
if (isset($_REQUEST['type_manager'])) {
    if ($_REQUEST['type_manager'] == '') {
        unset($_REQUEST['type_manager']);
    } else {
        $typeManager = $_REQUEST['type_manager'];
    }
}

$id_employee = '';
if (isset($_REQUEST['id_employee']) && ! empty($_REQUEST['id_employee'])) {
    $id_employee = $_REQUEST['id_employee'];
} else {
    returnError("Nhập id_employee!");
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
        $sql = 'SELECT * FROM tbl_admin_account WHERE id = ' . $id_employee . ' ';
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_array($result)) {
            if ($row['account_password'] != md5($_REQUEST['old_password'])) {
                echo json_encode(array(
                    'success' => 'false',
                    'message' => 'Mật khẩu cũ không chính xác!'
                ));
                exit();
            }
        }
    }
}

if (!empty($typeManager) && $typeManager == "employee_manager"){
    $user_change_password = 1;
}

if (isset($_REQUEST['password']) && ! empty($_REQUEST['password']) && $user_change_password == 1) {
    $query = "UPDATE tbl_admin_account SET ";
    $query .= "account_password  = '" . md5(mysqli_real_escape_string($conn, $_REQUEST['password'])) . "' ";
    $query .= "WHERE id = '" . $id_employee . "'";
    // check execute query
    if ($conn->query($query)) {
        returnSuccess("Cập nhật mật khẩu thành công!");
    } else {
        returnError("Cập nhật mật khẩu không thành công!");
    }
}

?>