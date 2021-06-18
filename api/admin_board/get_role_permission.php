<?php
$arr_result = array();

$idUser = '';
if (isset($_REQUEST['id_user'])) {
    if ($_REQUEST['id_user'] == '') {
        unset($_REQUEST['id_user']);
    }else{
        $idUser = $_REQUEST['id_user'];
    }
}

$arr_result['data'] = getRolePermission($idUser,$conn);

if(count($arr_result['data']) >0){
    $arr_result['success'] = "true";
}else{
}
$arr_result['success'] = "true";

echo json_encode($arr_result);

