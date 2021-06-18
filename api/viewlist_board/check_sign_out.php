<?php

$id_user = '';
if (isset($_REQUEST['id_user']) && ! empty($_REQUEST['id_user'])) {
    $id_user = $_REQUEST['id_user'];
}else{
    returnError("Nhập id_user!");
}

$type_login = '';
if (isset($_REQUEST['type_login']) && ! empty($_REQUEST['type_login'])) {
    $type_login = $_REQUEST['type_login'];
}else{
    returnError("Nhập type_login!");
}

$sql = '';
switch ($type_login){
    case "employee":
        $sql = "SELECT * FROM tbl_admin_account WHERE id = '".$id_user."'";
        break;
        
    case "customer":
        $sql = "SELECT * FROM tbl_customer_customer WHERE id = '".$id_user."'";
        break;
}

$result = mysqli_query($conn, $sql);

$num_row = mysqli_num_rows($result);

$result_arr = array();
$result_arr['success'] = 'true';

if ($num_row > 0) {
    while ($row = $result->fetch_assoc()) {
        $user_item = array(
            'id' => $row['id'],
            'force_sign_out' =>  $row['force_sign_out']
        );
        
        $result_arr['data'] = array(
            $user_item
        );
    }
}else{
    $result_arr['message'] = 'Không tìm thấy user!';
}

echo json_encode($result_arr);

?>






