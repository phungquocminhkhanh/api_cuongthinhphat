<?php

if(isset($_REQUEST['customer_phone']))
{
    if($_REQUEST['customer_phone']==''){
        unset($_REQUEST['customer_phone']);
    }
}

if(!isset($_REQUEST['customer_phone']))
{
    echo json_encode(
        array('success'   => 'false','message' => 'Chưa nhập số điện thoại!!!')
        );
    exit();
}

// check if customer_phone exist in table_user
$sql_tmp = 'SELECT * FROM tbl_customer_customer WHERE customer_phone = '.$_REQUEST['customer_phone'].' ';
$rs = mysqli_query($conn,$sql_tmp);
if(mysqli_num_rows($rs)>0){
    echo json_encode(
        array('success'   => 'false','message' => 'Số điện thoại đăng ký đã tồn tại!')
        );
    exit();
}
// end check if customer_phone exist in table_user


echo json_encode(
    array('success'   => 'true','message' => 'Số điện thoại đăng ký chưa tồn tại!')
    );
exit();



?>






