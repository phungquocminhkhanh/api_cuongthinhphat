<?php
if (isset($_REQUEST['type_manager'])) {
    if ($_REQUEST['type_manager'] == '') {
        unset($_REQUEST['type_manager']);
    }
}

if (! isset($_REQUEST['type_manager'])) {
    returnError("type_manager is missing!");
}

$typeManager = $_REQUEST['type_manager'];

switch ($typeManager) {
    case 'get_support_hotline':
        include_once './viewlist_board/get_support_hotline.php';
        break;
    
    case 'update_support_hotline':
        
        $id_hotline = '';
        if (isset($_REQUEST['id_hotline']) && ! empty($_REQUEST['id_hotline'])) {
            $id_hotline = $_REQUEST['id_hotline'];
        } else {
            returnError("Nhập id_hotline!");
        }
        
        if (isset($_REQUEST['num_account']) && ! empty($_REQUEST['num_account'])) {
            $num_account = $_REQUEST['num_account'];
            
            $query = "UPDATE tbl_company_support SET ";
            $query .= " num_account  = '" . $num_account . "' ";
            $query .= " WHERE id = '" . $id_hotline . "'";
            // Create post
            if ($conn->query($query)) {
                returnSuccess("Cập nhật hotline thành công!");
            } else {
                returnError("Cập nhật hotline không thành công!");
            }
        }
        
        break;
    
    default:
        returnError("type_manager is not accept!");
        break;
}