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
    case 'create_customer':
        include_once './customer_board/register.php';
        break;
        
    case 'update_customer':
        include_once './customer_board/update_personal.php';
        break;
    case 'customer_filter':
    case 'list_customer':
        
        include_once './viewlist_board/list_customer.php';
        
        break;
        
    case 'delete_customer':
        
        if (isset($_REQUEST['id_customer'])) {
            if ($_REQUEST['id_customer'] == '') {
                unset($_REQUEST['id_customer']);
                returnError("Nhập id_customer");
            }
        }
        
        if (! isset($_REQUEST['id_customer'])) {
            returnError("Nhập id_customer");
        }
        
        $id_customer = $_REQUEST['id_customer'];
        
        $sql_check_customer_exists = "SELECT * FROM tbl_customer_customer WHERE id = '" . $id_customer . "'";
        
        $result_check = mysqli_query($conn, $sql_check_customer_exists);
        $num_result_check = mysqli_num_rows($result_check);
        
        if ($num_result_check > 0) {
            
            $sql_check_customer_order = "
                            SELECT *
                            FROM tbl_product_product
                            WHERE  id_customer = '" . $id_customer . "'
                          ";
            
            $result_check_customer_order = mysqli_query($conn, $sql_check_customer_order);
            $num_result_check_customer_order = mysqli_num_rows($result_check_customer_order);
            if ($num_result_check_customer_order >0 ){
                returnError("Không thể xóa tài khoản khi đã có sản phẩm!");
            }
            
            $sql_delete_customer = "
                            DELETE FROM tbl_customer_customer
                            WHERE  id = '" . $id_customer . "'
                          ";
            if ($conn->query($sql_delete_customer)) {
                returnSuccess("Xóa khách hàng thành công!");
            } else {
                returnError("Xóa khách hàng không thành công!");
            }
        } else {
            returnError("Không tìm thấy khách hàng!");
        }
        
        break;
        
    case 'resset_password_customer':
        
        if (isset($_REQUEST['id_customer'])) {
            if ($_REQUEST['id_customer'] == '') {
                unset($_REQUEST['id_customer']);
                returnError("Nháº­p id_customer");
            }
        } else {
            returnError("Nhập id_customer");
        }
        
        if (isset($_REQUEST['password_reset'])) {
            if ($_REQUEST['password_reset'] == '') {
                unset($_REQUEST['password_reset']);
                returnError("Nhập password_reset");
            }
        } else {
            returnError("Nhập password_reset");
        }
        
        $id_customer = $_REQUEST['id_customer'];
        $password_reset = $_REQUEST['password_reset'];
        
        $sql_check_customer_exists = "SELECT * FROM tbl_customer_customer WHERE id = '" . $id_customer . "'";
        
        $result_check = mysqli_query($conn, $sql_check_customer_exists);
        $num_result_check = mysqli_num_rows($result_check);
        
        if ($num_result_check > 0) {
            
            $query = "UPDATE tbl_customer_customer SET ";
            $query .= "customer_password  = '" . md5(mysqli_real_escape_string($conn, $password_reset)) . "' ";
            $query .= "WHERE id = '" . $id_customer . "'";
            // check execute query
            if ($conn->query($query)) {
                returnSuccess("Cập nhật thành công!");
            } else {
                returnError("Cập nhật không thành công!");
            }
        } else {
            returnError("Không tìm thấy khách hàng!");
        }
        exit();
        break;
        
        
    default:
        returnError("type_manager is not accept!");
        break;
}

