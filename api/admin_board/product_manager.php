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
    case 'list_product':
        include_once './viewlist_board/get_customer_product.php';
        break;
        
    case 'create_product':

        $id_admin = '';
        if (isset($_REQUEST['id_admin']) && ! empty($_REQUEST['id_admin'])) {
            $id_admin = $_REQUEST['id_admin'];
        } else {
            returnError("Nhập id_admin!");
        }

        $id_customer = '';
        if (isset($_REQUEST['id_customer']) && ! empty($_REQUEST['id_customer'])) {
            $id_customer = $_REQUEST['id_customer'];
        } else {
            returnError("Nhập khách hàng!");
        }
        $id_category = 1;
        $img_photo_product = '';
        
        if (isset($_FILES['product_img'])) {
            $img_photo_product = saveImage($_FILES['product_img'], 'images/product_product/');
            if ($img_photo_product == "error_size_img") {
                returnError("product_img > 5MB !");
            }
            
            if ($img_photo_product == "error_type_img") {
                returnError("product_img error type");
            }
        } else {
            returnError("Chọn hình ảnh sản phẩm!");
        }
        
        
        $id_unit = '';
        if (isset($_REQUEST['id_unit']) && ! empty($_REQUEST['id_unit'])) {
            $id_unit = $_REQUEST['id_unit'];
        } else {
            returnError("Nhập đơn vị!");
        }
        $product_code = '';
        if (isset($_REQUEST['product_code']) && ! empty($_REQUEST['product_code'])) {
            $product_code = $_REQUEST['product_code'];
            $sql_check_product_code_exists = "SELECT * FROM tbl_product_product WHERE product_code = '" . $product_code . "'
            ";
            $result_check = $conn->query($sql_check_product_code_exists);
            $num_result_check = mysqli_num_rows($result_check);
            if ($num_result_check > 0) {
                returnError("Mã sản phẩm đã tồn tại!");
            }
        } else {
            returnError("Nhập mã sản phẩm!");
        }
        $product_name = '';
        if (isset($_REQUEST['product_name']) && ! empty($_REQUEST['product_name'])) {
            $product_name = $_REQUEST['product_name'];
        } else {
            returnError("Nhập tên sản phẩm!");
        }
        $product_description = '';
        if (isset($_REQUEST['product_description']) && ! empty($_REQUEST['product_description'])) {
            $product_description = $_REQUEST['product_description'];
        } else {
            returnError("Nhập mô tả sản phẩm!");
        }

        $id_packet = '';
        if (isset($_REQUEST['id_packet']) && ! empty($_REQUEST['id_packet'])) {
            $id_packet = $_REQUEST['id_packet'];
        } else {
            returnError("Nhập đóng gói!");
        }

        $product_unit_packet = '';
        if (isset($_REQUEST['product_unit_packet']) && ! empty($_REQUEST['product_unit_packet'])) {
            $product_unit_packet = $_REQUEST['product_unit_packet'];
        } else {
            returnError("Nhập quy cách!");
        }

        $safety_stock = '';
        if (isset($_REQUEST['safety_stock']) && ! empty($_REQUEST['safety_stock'])) {
            $safety_stock = $_REQUEST['safety_stock'];
        } else {
            returnError("Nhập an toàn kho sản phẩm!");
        }
        
        $sql_create_product = "INSERT INTO tbl_product_product SET
                id_admin = '" . $id_admin . "',
                id_customer = '" . $id_customer . "',
                id_category = '" . $id_category . "',
                id_unit = '" . $id_unit . "',
                id_packet = '" . $id_unit . "',
                product_unit_packet = '" . $product_unit_packet . "',
                product_name = '" . $product_name . "',
                product_code = '" . $product_code . "',
                product_description = '" . $product_description . "',
                product_img = '" . $img_photo_product . "',
                safety_stock = '" . $safety_stock . "'
        ";
        
        if ($conn->query($sql_create_product)) {
            returnSuccess("Thêm sản phẩm thành công!");
        } else {
            returnError("Thêm sản phẩm không thành công!");
        }
        
        break;
        
    case 'update_product':
        $query = "UPDATE tbl_product_product SET ";
        $id_product = '';
        if (isset($_REQUEST['id_product']) && ! empty($_REQUEST['id_product'])) {
            $id_product = $_REQUEST['id_product'];
        } else {
            returnError("Nhập id_product!");
        }
        
        $check = 0;
        
        if (isset($_REQUEST['id_unit']) && ! empty($_REQUEST['id_unit'])) {
            
            $id_unit = $_REQUEST['id_unit'];
            $query .= " id_unit  = '" . $id_unit . "', ";
           
        }
        if (isset($_REQUEST['id_packet']) && ! empty($_REQUEST['id_packet'])) {
            
            $id_packet = $_REQUEST['id_packet'];
            $query .= " id_packet  = '" . $id_packet . "', ";
           
        }
        if (isset($_REQUEST['product_unit_packet']) && ! empty($_REQUEST['product_unit_packet'])) {
            
            $product_unit_packet = $_REQUEST['product_unit_packet'];
            $query .= " product_unit_packet  = '" . $product_unit_packet . "', ";

        }
        if (isset($_REQUEST['product_name']) && ! empty($_REQUEST['product_name'])) {
            
            $product_name = $_REQUEST['product_name'];         
            $query .= " product_name  = '$product_name', ";

        }
        if (isset($_REQUEST['id_category']) && ! empty($_REQUEST['id_category'])) {
            
            $id_category = $_REQUEST['id_category'];
            $query .= " id_category  = '" . $id_category . "', ";
        }
        if (isset($_REQUEST['product_name']) && ! empty($_REQUEST['product_name'])) {
            
            $product_name = $_REQUEST['product_name'];
            $query .= " product_name  = '" . $product_name . "', ";

        }
        if (isset($_REQUEST['product_description']) && ! empty($_REQUEST['product_description'])) {
            
            $product_description = $_REQUEST['product_description'];
            $query .= " product_description  = '" . $product_description . "', ";
           
        }
        if (isset($_REQUEST['safety_stock']) && ! empty($_REQUEST['safety_stock'])) {
            
            $safety_stock = $_REQUEST['safety_stock'];
            $query .= " safety_stock  = '" . $safety_stock . "', ";
        }
        
        $img_photo_product = '';
        if (isset($_FILES['product_img']) && is_uploaded_file($_FILES['product_img']['tmp_name'])) {
            
            $img_photo_product = saveImage($_FILES['product_img'], 'images/product_product/');
            if ($img_photo_product == "error_size_img") {
                returnError("product_img > 5MB !");
            }
            
            if ($img_photo_product == "error_type_img") {
                returnError("product_img error type");
            }
            
            $sql_tmp = "SELECT * FROM tbl_product_product WHERE id = '" . $id_product . "' ";
            
            $result_tmp = mysqli_query($conn, $sql_tmp);
            while ($row_tmp = $result_tmp->fetch_assoc()) {
                $p_img = $row_tmp['product_img'];
                
                if (! empty($p_img) && file_exists('../' . $p_img)) {
                    unlink('../' . $p_img);
                }
            }
            $query .= " product_img = '" . $img_photo_product . "', ";
                 
        }
        $query .= "WHERE id = '" . $id_product . "'";
        $query_final=str_replace(", WHERE", "  WHERE", $query);// cắt bỏ dấu ',' cuối cùng
       
        if ($conn->query($query_final)) {
            returnSuccess("Cập nhật thành công!");
        } else {
            returnError("Cập nhật hình ảnh sản phẩm không thành công!");
        }
        
        break;
        
        
        
    case 'delete_product':
        
        $id_product = '';
        if (isset($_REQUEST['id_product']) && ! empty($_REQUEST['id_product'])) {
            $id_product = $_REQUEST['id_product'];
        } else {
            returnError("Nhập id_product!");
        }
        
        $sql_check_product_exists = "SELECT * FROM tbl_product_product WHERE id = '" . $id_product . "'";
        
        $result_check = mysqli_query($conn, $sql_check_product_exists);
        $num_result_check = mysqli_num_rows($result_check);
        
        if ($num_result_check > 0) {
            
            $check_production_product = "SELECT *
                            FROM tbl_production_product
                            WHERE  id_product = '" . $id_product . "'
                          ";
            
            $result_check_product = mysqli_query($conn, $check_production_product);
            $num_result_check_product = mysqli_num_rows($result_check_product);
            if ($num_result_check_product > 0) {
                returnError("Sản phẩm không thể xóa, đã có đơn hàng sản xuất!");
            }
            
            while ($rowItem = $result_check->fetch_assoc()) {
                $image_product = $rowItem['product_img'];
                
                if (file_exists('../' . $image_product)) {
                    unlink('../' . $image_product);
                }
            }
            
            $sql_delete_product = "
                            DELETE FROM tbl_product_product
                            WHERE  id = '" . $id_product . "'
                          ";
            if ($conn->query($sql_delete_product)) {
                returnSuccess("Xóa sản phẩm thành công!");
            } else {
                returnError("Xóa sản phẩm không thành công!");
            }
        }
        
        break;
        
    case 'filter_':
        
        break;
        
    default:
        returnError("type_manager is not accept!");
        break;
}