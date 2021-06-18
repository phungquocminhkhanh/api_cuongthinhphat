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
    case 'get_company_intro':
        include_once './viewlist_board/get_company_intro.php';
        break;
    
    case 'create_company_intro':
        
        $company_title = '';
        if (isset($_REQUEST['company_title']) && ! empty($_REQUEST['company_title'])) {
            $company_title = $_REQUEST['company_title'];
            
            // check_order_slide
            $sql_check_company_intro_exists = "SELECT *
                FROM tbl_company_introduce
                WHERE company_title = '" . $company_title . "'
             ";
            $result_check_company_intro_exists = mysqli_query($conn, $sql_check_company_intro_exists);
            $num_result_check_company_intro_exists = mysqli_num_rows($result_check_company_intro_exists);
            if ($num_result_check_company_intro_exists > 0) {
                returnError("Tiêu đề giới thiệu đã tồn tại!");
            }
        } else {
            returnError("Nhập tiêu đề giới thiệu!");
        }
        $company_content = '';
        if (isset($_REQUEST['company_content']) && ! empty($_REQUEST['company_content'])) {
            $company_content = $_REQUEST['company_content'];
            
        } else {
            returnError("Nhập mô tả!");
        }
        
        $sql_create_company_intro = "
            INSERT INTO tbl_company_introduce SET
             company_title            = '" . $company_title . "',
             company_content     = '" . $company_content . "'
        ";
        
        if ($conn->query($sql_create_company_intro)) {
            
            returnSuccess("Thêm thông tin giới thiệu thành công!");
        } else {
            returnError("Thêm thông tin giới thiệu không thành công!");
        }
        
        break;
    
    case 'update_company_intro':
        
        $id_intro = '';
        if (isset($_REQUEST['id_intro']) && ! empty($_REQUEST['id_intro'])) {
            $id_intro = $_REQUEST['id_intro'];
        } else {
            returnError("Nhập id_intro!");
        }
        
        $check = 0;
        
        if (isset($_REQUEST['company_title']) && ! empty($_REQUEST['company_title'])) {
            $company_title = $_REQUEST['company_title'];
            // check_order_slide
            $sql_check_company_intro_exists = "SELECT *
                FROM tbl_company_introduce
                WHERE company_title = '" . $company_title . "' AND id != '" . $id_intro . "'
             ";
            $result_check_company_intro = mysqli_query($conn, $sql_check_company_intro_exists);
            $num_result_check_company_intro = mysqli_num_rows($result_check_company_intro);
            if ($num_result_check_company_intro > 0) {
                returnError("Tiêu đề giới thiệu đã tồn tại!");
            }
            
            $check ++;
            $query = "UPDATE tbl_company_introduce SET ";
            $query .= " company_title  = '" . $company_title . "' ";
            $query .= " WHERE id = '" . $id_intro . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            } else {
                returnError("Cập nhật tiêu đề giới thiệu không thành công!");
            }
        }
        if (isset($_REQUEST['company_content']) && ! empty($_REQUEST['company_content'])) {
            $company_content = $_REQUEST['company_content'];
            
            $check ++;
            $query = "UPDATE tbl_company_introduce SET ";
            $query .= " company_content  = '" . $company_content . "' ";
            $query .= " WHERE id = '" . $id_intro . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            } else {
                returnError("Cập nhật mô tả không thành công!");
            }
        }
        
        
        if ($check > 0) {
            returnError("Cập nhật thông tin không thành công!");
        } else {
            returnSuccess("Cập nhật thông tin thành công!");
        }
        
        break;
    
    case 'delete_company_intro':
        
        $id_intro = '';
        if (isset($_REQUEST['id_intro']) && ! empty($_REQUEST['id_intro'])) {
            $id_intro = $_REQUEST['id_intro'];
        } else {
            returnError("Nhập id_intro!");
        }
        
        $sql_check_company_intro_exists = "SELECT *
                FROM tbl_company_introduce
                WHERE id = '" . $id_intro . "'
             ";
        
        $result_check = mysqli_query($conn, $sql_check_company_intro_exists);
        $num_result_check = mysqli_num_rows($result_check);
        
        if ($num_result_check > 0) {
            
            $sql_delete_company_intro = "
                            DELETE FROM tbl_company_introduce
                            WHERE  id = '" . $id_intro . "'
                          ";
            if ($conn->query($sql_delete_company_intro)) {
                returnSuccess("Bạn đã xóa bài viết giới thiệu này thành công!");
            } else {
                returnError("Xóa bài viết giới thiệu không thành công!");
            }
        } else {
            returnError("Không tìm thấy bài viết!");
        }
        
        break;
    
    default:
        returnError("type_manager is not accept!");
        break;
}