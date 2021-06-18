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
    case 'list_product_feature':
        include_once './viewlist_board/get_company_product_feature.php';
        break;
        
    case 'create_product_feature':
        
        $feature_product = '';
        if (isset($_REQUEST['feature_product']) && ! empty($_REQUEST['feature_product'])) {
            $feature_product =  mysqli_real_escape_string($conn, $_REQUEST['feature_product']);
        } else {
            returnError("Nhập tên sản phẩm tiêu biểu!");
        }
        
        $feature_description = '';
        if (isset($_REQUEST['feature_description']) && ! empty($_REQUEST['feature_description'])) {
            $feature_description = mysqli_real_escape_string($conn, $_REQUEST['feature_description']);
        } else {
            returnError("Nhập mô tả!");
        }
        $feature_stars = '';
        if (isset($_REQUEST['feature_stars']) && ! empty($_REQUEST['feature_stars'])) {
            $feature_stars = $_REQUEST['feature_stars'];
        } else {
            returnError("Nhập đánh giá!");
        }
        
        $feature_img = '';
        
        if (isset($_FILES['feature_img'])) {
            $feature_img = saveImage($_FILES['feature_img'], 'images/company_feature/');
            if ($feature_img == "error_size_img") {
                returnError("feature_img > 5MB !");
            }
            
            if ($feature_img == "error_type_img") {
                returnError("feature_img error type");
            }
        }
        $sql_create_product_feature = "
            INSERT INTO tbl_company_feature SET
             feature_product            = '" . $feature_product . "',
             feature_description            = '" . $feature_description . "',
             feature_stars            = '" . $feature_stars . "',
             feature_img     = '" . $feature_img . "'
        ";
        
        if ($conn->query($sql_create_product_feature)) {
            
            returnSuccess("Tạo sản phẩm tiêu biểu thành công!");
        } else {
            returnError("Tạo sản phẩm tiêu biểu không thành công!");
        }
        break;
        
    case 'update_product_feature':
        
        $id_product_feature = '';
        if (isset($_REQUEST['id_product_feature']) && ! empty($_REQUEST['id_product_feature'])) {
            $id_product_feature = $_REQUEST['id_product_feature'];
        } else {
            returnError("Nhập id_product_feature!");
        }
        
        $check = 0;
        
        if (isset($_REQUEST['feature_product']) && ! empty($_REQUEST['feature_product'])) {
            $check ++;
            $query = "UPDATE tbl_company_feature SET ";
            $query .= " feature_product  = '" . mysqli_real_escape_string($conn, $_REQUEST['feature_product']) . "' ";
            $query .= " WHERE id = '" . $id_product_feature . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            } else {
                returnError("Cập nhật tên sản phẩm không thành công!");
            }
        }
        if (isset($_REQUEST['feature_description']) && ! empty($_REQUEST['feature_description'])) {
            $check ++;
            $query = "UPDATE tbl_company_feature SET ";
            $query .= " feature_description  = '" . mysqli_real_escape_string($conn, $_REQUEST['feature_description']) . "' ";
            $query .= " WHERE id = '" . $id_product_feature . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            } else {
                returnError("Cập nhật mô tả không thành công!");
            }
        }
        if (isset($_REQUEST['feature_stars']) && ! empty($_REQUEST['feature_stars'])) {
            $check ++;
            $query = "UPDATE tbl_company_feature SET ";
            $query .= " feature_stars  = '" . $_REQUEST['feature_stars'] . "' ";
            $query .= " WHERE id = '" . $id_product_feature . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            } else {
                returnError("Cập nhật đánh giá không thành công!");
            }
        }
        
        $feature_img = '';
        if (isset($_FILES['feature_img']) && is_uploaded_file($_FILES['feature_img']['tmp_name'])) {
            
            $feature_img = saveImage($_FILES['feature_img'], 'images/company_feature/');
            if ($feature_img == "error_size_img") {
                returnError("image_upload > 5MB !");
            }
            
            if ($feature_img == "error_type_img") {
                returnError("image_upload error type");
            }
            
            $sql_tmp = "SELECT * FROM tbl_company_feature WHERE id = '" . $id_product_feature . "' ";
            
            $result_tmp = mysqli_query($conn, $sql_tmp);
            while ($row_tmp = $result_tmp->fetch_assoc()) {
                $old_img = $row_tmp['feature_img'];
                
                // unlink old file avatar if exist and it not the place holder file
                if (! empty($news_img) && file_exists('../' . $old_img)) {
                    unlink('../' . $old_img);
                }
                // end handle unlink old file avatar if exist and it not the place holder file
            }
            
            $check ++;
            $query = "UPDATE tbl_company_feature SET ";
            $query .= " feature_img = '" . $feature_img . "' ";
            $query .= " WHERE id = '" . $id_product_feature . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            } else {
                returnError("Cập nhật hình ảnh sản phẩm không thành công!");
            }
        }
        
        if ($check > 0) {
            returnError("Cập nhật sản phẩm tiêu biểu không thành công!");
        } else {
            returnSuccess("Cập nhật sản phẩm tiêu biểu thành công!");
        }
        
        break;
        
        
        
    case 'delete_product_feature':
        $id_product_feature = '';
        if (isset($_REQUEST['id_product_feature']) && ! empty($_REQUEST['id_product_feature'])) {
            $id_product_feature = $_REQUEST['id_product_feature'];
        } else {
            returnError("Nhập id_product_feature!");
        }
        
        $sql_check_notification_exists = "SELECT * FROM tbl_company_feature WHERE id = '" . $id_product_feature . "'";
        
        $result_check = mysqli_query($conn, $sql_check_notification_exists);
        $num_result_check = mysqli_num_rows($result_check);
        
        if ($num_result_check > 0) {
            
            while ($rowItem = $result_check->fetch_assoc()) {
                $feature_img = $rowItem['feature_img'];
                
                if (file_exists('../' . $feature_img)) {
                    unlink('../' . $feature_img);
                }
            }
            
            $sql_delete_news = "
                            DELETE FROM tbl_company_feature
                            WHERE  id = '" . $id_product_feature . "'
                          ";
            if ($conn->query($sql_delete_news)) {
                returnSuccess("Xóa sản phẩm tiêu biểu thành công!");
            } else {
                returnError("Xóa sản phẩm tiêu biểu không thành công!");
            }
        } else {
            returnError("Không tìm thấy sản phẩm tiêu biểu!");
        }
        
        
        break;
        
        
    default:
        returnError("type_manager is not accept!");
        break;
}