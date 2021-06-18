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
    case 'list_unit':
        $result_arr = array();
        $sql = "SELECT count(tbl_product_unit.id) as unit_total  FROM tbl_product_unit";
        $result = mysqli_query($conn, $sql);
        while ($row = $result->fetch_assoc()) {
            $result_arr['total'] = $row['unit_total'];
        }
        
        $limit = 50;
        $page = 1;
        if (isset($_REQUEST['limit']) && $_REQUEST['limit'] != '') {
            $limit = $_REQUEST['limit'];
        }
        if (isset($_REQUEST['page']) && $_REQUEST['page'] != '') {
            $page = $_REQUEST['page'];
        }
        
        $result_arr['total_page'] = strval(ceil($result_arr['total'] / $limit));
        
        $result_arr['limit'] = strval($limit);
        $start = ($page - 1) * $limit;
        
        // query
        $sql = "SELECT * FROM `tbl_product_unit` WHERE 1=1 LIMIT $start,$limit";
        $result = mysqli_query($conn, $sql);
        mysqli_close($conn);
        $num = mysqli_num_rows($result);
        
        $result_arr['success'] = 'true';
        $result_arr['page'] = $page;
        $result_arr['data'] = array();
        
        if ($num > 0) {
            
            while ($row = $result->fetch_assoc()) {
                $c_item = array(
                    'id' => $row['id'],
                    'unit_title' => $row['unit_title'],
                    'unit' => $row['unit']
                );
                // Push to "data"
                array_push($result_arr['data'], $c_item);
            }
        }
        // Turn to JSON & output
        echo json_encode($result_arr);
        exit();
        break;
        
    case 'create_unit':
        
        $unit_title = '';
        if (isset($_REQUEST['unit_title'])) {
            if ($_REQUEST['unit_title'] == '') {
                unset($_REQUEST['unit_title']);
                returnError("Nhập tên đầy đủ");
            }else{
                $unit_title = $_REQUEST['unit_title'];
            }
        }
        
        if (! isset($_REQUEST['unit_title'])) {
            returnError("Nhập tên đầy đủ");
        }
        
        $unit = '';
        
        if (isset($_REQUEST['unit'])) {
            if ($_REQUEST['unit'] == '') {
                unset($_REQUEST['unit']);
                returnError("Nhập tên viết tắt");
            } else {
                $unit = $_REQUEST['unit'];
            }
        }
        
        if (! isset($_REQUEST['unit'])) {
        returnError("Nhập tên viết tắt");
        }
        
        $sql_check_unit_exists = "SELECT * FROM tbl_product_unit WHERE unit = '" . $unit . "'";
        
        $result_check = mysqli_query($conn, $sql_check_unit_exists);
        $num_result_check = mysqli_num_rows($result_check);
        
        if ($num_result_check > 0) {
            returnError("Đơn vị đã tồn tại!");
        }
        
        $sql_create_unit = "
            INSERT INTO tbl_product_unit SET
             unit_title           = '" . $unit_title . "',
             unit           = '" . $unit . "'
                 
        ";
        
        if ($conn->query($sql_create_unit)) {
            returnSuccess("Tạo đơn vị thành công!");
        } else {
            returnError("Tạo đơn vị không thành công!");
        }
        
        break;
        
    case 'update_unit':
        
        if (isset($_REQUEST['id_unit'])) {
            if ($_REQUEST['id_unit'] == '') {
                unset($_REQUEST['id_unit']);
                returnError("Nhập id_unit");
            }
        }
        
        if (! isset($_REQUEST['id_unit'])) {
            returnError("Nhập id_unit");
        }
        
        $id_unit = $_REQUEST['id_unit'];
        
        $sql_check_unit_exists = "SELECT * FROM tbl_product_unit WHERE id = '" . $id_unit . "'";
        
        $result_check = mysqli_query($conn, $sql_check_unit_exists);
        $num_result_check = mysqli_num_rows($result_check);
        
        if ($num_result_check > 0) {
            
            $check = 0;
            
            if (isset($_REQUEST['unit_title']) && ! empty($_REQUEST['unit_title'])) {
                $check ++;
                $query = "UPDATE tbl_product_unit SET ";
                $query .= "unit_title  = '" . $_REQUEST['unit_title'] . "' ";
                $query .= "WHERE id = '" . $id_unit . "'";
                // Create post
                if ($conn->query($query)) {
                    $check --;
                } else {
                    returnError("Cập nhật unit_title không thành công!");
                }
            }
            
            if (isset($_REQUEST['unit']) && ! empty($_REQUEST['unit'])) {
                $unit = $_REQUEST['unit'];
                $sql_check_unit_exists = "SELECT * FROM tbl_product_unit WHERE unit = '" . $unit . "' AND id != '".$id_unit."'";
                
                $result_check = mysqli_query($conn, $sql_check_unit_exists);
                $num_result_check = mysqli_num_rows($result_check);
                
                if ($num_result_check > 0) {
                    returnError("Tên viết tắt đơn vị đã tồn tại!");
                }
                
                $check ++;
                $query = "UPDATE tbl_product_unit SET ";
                $query .= "unit  = '" . $_REQUEST['unit'] . "' ";
                $query .= "WHERE id = '" . $id_unit . "'";
                // Create post
                if ($conn->query($query)) {
                    $check --;
                } else {
                    returnError("Cập nhật unit không thành công!");
                }
            }
            
            if ($check == 0) {
                returnSuccess("Cập nhật đơn vị thành công!");
            } else {
                returnError("Cập nhật đơn vị không thành công!");
            }
        } else {
            returnError("Không tìm thấy đơn vị!");
        }
        
        break;
        
    case 'delete_unit':
        
        if (isset($_REQUEST['id_unit'])) {
            if ($_REQUEST['id_unit'] == '') {
                unset($_REQUEST['id_unit']);
                returnError("Nhập id_unit");
            }
        }
        
        if (! isset($_REQUEST['id_unit'])) {
            returnError("Nhập id_unit");
        }
        
        $id_unit = $_REQUEST['id_unit'];
        
        $sql_check_unit_exists = "SELECT * FROM tbl_product_unit WHERE id = '" . $id_unit . "'";
        
        $result_check = mysqli_query($conn, $sql_check_unit_exists);
        $num_result_check = mysqli_num_rows($result_check);
        
        if ($num_result_check > 0) {
            
            $sql_check_product = "SELECT * FROM tbl_product_product WHERE id_unit = '".$id_unit."'";
            $result_check_product = mysqli_query($conn, $sql_check_product);
            $num_result_check_product = mysqli_num_rows($result_check_product);
            
            if ($num_result_check_product > 0) {
                returnError("Đơn vị tính đã tồn tại sản phẩm, không thể xóa!");
            }
            
            $sql_delete_unit = "
                            DELETE FROM tbl_product_unit
                            WHERE  id = '" . $id_unit . "'
                          ";
            if ($conn->query($sql_delete_unit)) {
                returnSuccess("Xóa đơn vị thành công!");
            } else {
                returnError("Xóa đơn vị không thành công!");
            }
        } else {
            returnError("Không tìm thấy đơn vị!");
        }
        break;
        
    default:
        returnError("type_manager is not accept!");
        break;
}

