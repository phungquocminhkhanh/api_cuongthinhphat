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
    case 'list_slide':
        include_once './viewlist_board/get_company_slide.php';
        break;
    
    case 'create_slide':
        
       
        if (isset($_REQUEST['id_admin']) && ! empty($_REQUEST['id_admin'])) {
            $id_admin = $_REQUEST['id_admin'];
           
        } else {
            returnError("Nhập tên id_admin!");
        }
        if (isset($_REQUEST['slide_title']) && ! empty($_REQUEST['slide_title'])) {
            $slide_title = $_REQUEST['slide_title'];
            
            // check_order_slide
            $sql_check_order_slide = "SELECT *
                FROM tbl_company_slide
                WHERE slide_title = '" . $slide_title . "'
             ";
            $result_check_order_slide = mysqli_query($conn, $sql_check_order_slide);
            $num_result_check_order_slide = mysqli_num_rows($result_check_order_slide);
            if ($num_result_check_order_slide > 0) {
                returnError("Đã tồn tại tên slide!");
            }
        } else {
            returnError("Nhập tên slide!");
        }
        
        $img_photo_slide = '';
        
        if (isset($_FILES['slide_img'])) {
            $img_photo_slide = saveImage($_FILES['slide_img'], 'images/company_slide/');
            if ($img_photo_slide == "error_size_img") {
                returnError("slide_img > 5MB !");
            }
            
            if ($img_photo_slide == "error_type_img") {
                returnError("slide_img error type");
            }
        } else {
            returnError("Chọn hình ảnh slide!");
        }
        
        $sql_create_slide = "
            INSERT INTO tbl_company_slide SET
             id_admin     = '" . $id_admin . "',
             slide_title            = '" . $slide_title . "',
             slide_img     = '" . $img_photo_slide . "'

        ";
        
        if ($conn->query($sql_create_slide)) {
            
            returnSuccess("Tạo slide thành công!");
        } else {
            returnError("Tạo slide không thành công!");
        }
        
        break;
    
    case 'update_slide':
        
        $id_slide = '';
        if (isset($_REQUEST['id_slide']) && ! empty($_REQUEST['id_slide'])) {
            $id_slide = $_REQUEST['id_slide'];
        } else {
            returnError("Nhập id_slide!");
        }
        
        $check = 0;
        
        if (isset($_REQUEST['slide_title']) && ! empty($_REQUEST['slide_title'])) {
            $slide_title = $_REQUEST['slide_title'];
            // check_order_slide
            $sql_check_order_slide = "SELECT *
                FROM tbl_company_slide
                WHERE slide_title = '" . $slide_title . "' AND id != '" . $id_slide . "'
             ";
            $result_check_order_slide = mysqli_query($conn, $sql_check_order_slide);
            $num_result_check_order_slide = mysqli_num_rows($result_check_order_slide);
            if ($num_result_check_order_slide > 0) {
                returnError("Đã tồn tại tên slide!");
            }
            
            $check ++;
            $query = "UPDATE tbl_company_slide SET ";
            $query .= " slide_title  = '" . $slide_title . "' ";
            $query .= " WHERE id = '" . $id_slide . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            } else {
                returnError("Cập nhật tên slide không thành công!");
            }
        }
        
        $img_photo_slide = '';
        if (isset($_FILES['slide_img']) && is_uploaded_file($_FILES['slide_img']['tmp_name'])) {
            
            $img_photo_slide = saveImage($_FILES['slide_img'], 'images/company_slide/');
            if ($img_photo_slide == "error_size_img") {
                returnError("slide_img > 5MB !");
            }
            
            if ($img_photo_slide == "error_type_img") {
                returnError("slide_img error type");
            }
            
            $sql_tmp = "SELECT * FROM tbl_company_slide WHERE id = '" . $id_slide . "' ";
            
            $result_tmp = mysqli_query($conn, $sql_tmp);
            while ($row_tmp = $result_tmp->fetch_assoc()) {
                $slide_img = $row_tmp['slide_img'];
                
                if (! empty($slide_img) && file_exists('../' . $slide_img)) {
                    unlink('../' . $slide_img);
                }
            }
            
            $check ++;
            $query = "UPDATE tbl_company_slide SET ";
            $query .= " slide_img = '" . $img_photo_slide . "' ";
            $query .= " WHERE id = '" . $id_slide . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            } else {
                returnError("Cập nhật hình ảnh slide không thành công!");
            }
        }

        if (isset($_REQUEST['slide_order']) && ! empty($_REQUEST['slide_order'])) {
            $slide_order = $_REQUEST['slide_order'];
            
            
            $check ++;
            $query = "UPDATE tbl_company_slide SET ";
            $query .= " slide_order  = '" . $slide_order . "' ";
            $query .= " WHERE id = '" . $id_slide . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            } else {
                returnError("Cập nhật thứ tự slide  không thành công!");
            }
        }
        if ($check > 0) {
            returnError("Cập nhật slide thành công!");
        } else {
            returnSuccess("Cập nhật slide thành công!");
        }
        
        break;
    
    case 'delete_slide':
        
        $id_slide = '';
        if (isset($_REQUEST['id_slide']) && ! empty($_REQUEST['id_slide'])) {
            $id_slide = $_REQUEST['id_slide'];
        } else {
            returnError("Nhập id_slide!");
        }
        
        $sql_check_slide_exists = "SELECT * FROM tbl_company_slide WHERE id = '" . $id_slide . "'";
        
        $result_check = mysqli_query($conn, $sql_check_slide_exists);
        $num_result_check = mysqli_num_rows($result_check);
        
        if ($num_result_check > 0) {
            
            while ($rowItem = $result_check->fetch_assoc()) {
                $image_slide = $rowItem['slide_img'];
                
                if (file_exists('../' . $image_slide)) {
                    unlink('../' . $image_slide);
                }
            }
            
            $sql_delete_slide = "
                            DELETE FROM tbl_company_slide
                            WHERE  id = '" . $id_slide . "'
                          ";
            if ($conn->query($sql_delete_slide)) {
                returnSuccess("Xóa slide thành công!");
            } else {
                returnError("Xóa slide không thành công!");
            }
        } else {
            returnError("Không tìm thấy slide!");
        }
        
        break;
    
    default:
        returnError("type_manager is not accept!");
        break;
}