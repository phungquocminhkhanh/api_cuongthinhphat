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
    case 'list_supplier':
        $filter = '';
        if (isset($_REQUEST['filter'])) {
            if ($_REQUEST['filter'] == '') {
                unset($_REQUEST['filter']);
            } else {
                $filter = $_REQUEST['filter'];
            }
        }
        
        $result_arr = array();
        // get total customer
        $sql = "SELECT count(tbl_material_supplier.id) as supplier_total  FROM tbl_material_supplier WHERE 1=1 ";
        
        if (! empty($filter)) {
            $sql .= " AND supplier_name LIKE '%" . $filter . "%'  OR supplier_code = '" . $filter . "'";
        }
        
        $result = mysqli_query($conn, $sql);
        while ($row = $result->fetch_assoc()) {
            $result_arr['total'] = strval($row['supplier_total'] - 1);
        }
        
        $limit = 20;
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
        $sql = "SELECT
             *
             FROM tbl_material_supplier
         WHERE 1=1 ";
        
        if (! empty($filter)) {
            $sql .= " AND supplier_name LIKE '%" . $filter . "%' OR supplier_code = '" . $filter . "'";
        }
        
        $sql .= " LIMIT $start,$limit";
        
        $result = mysqli_query($conn, $sql);
        
        // Get row count
        $num = mysqli_num_rows($result);
        
        // Check if any categories
        
        $result_arr['success'] = 'true';
        $result_arr['page'] = $page;
        $result_arr['data'] = array();
        
        if ($num > 0) {
            // Cat array
            while ($row = $result->fetch_assoc()) {
                $supplier_item = array(
                    'id' => $row['id'],
                    'supplier_code' => $row['supplier_code'],
                    'supplier_name' => $row['supplier_name'],
                    'supplier_email' => $row['supplier_email'],
                    'supplier_phone' => $row['supplier_phone'],
                    'supplier_address' => $row['supplier_address']
                );
                
                // Push to "data"
                array_push($result_arr['data'], $supplier_item);
            }
        }
        // Turn to JSON & output
        echo json_encode($result_arr);
        
        break;
        
    case 'create_supplier':
        
        $supplier_code = '';
        if (isset($_REQUEST['supplier_code']) && ! empty($_REQUEST['supplier_code'])) {
            $supplier_code = $_REQUEST['supplier_code'];
            // check employee_code exists
            $sql_check_supplier_code = "SELECT * FROM tbl_material_supplier WHERE supplier_code = '" . $id_code . "'";
            $result_check = $conn->query($sql_check_supplier_code);
            $num_result_check = mysqli_num_rows($result_check);
            if ($num_result_check > 0) {
                returnError("Mã nhà cung cấp đã tồn tại!");
            }
        } else {
                        returnError("Nhập mã nhà cung cấp!");
//             $id_code = "NCC".time();
        }
        
        $name = '';
        if (isset($_REQUEST['supplier_name']) && ! empty($_REQUEST['supplier_name'])) {
            $name = $_REQUEST['supplier_name'];
        } else {
            returnError("Nhập tên nhà cung cấp!");
        }
        
        $phone_number = '';
        if (isset($_REQUEST['supplier_phone']) && ! empty($_REQUEST['supplier_phone'])) {
            $phone_number = $_REQUEST['supplier_phone'];
        } else {
            returnError("Nhập số điện thoại nhà cung cấp!");
        }
        
        $email = '';
        if (isset($_REQUEST['supplier_email']) && ! empty($_REQUEST['supplier_email'])) {
            $email = $_REQUEST['supplier_email'];
        }else {
            returnError("Nhập email nhà cung cấp!");
        }
        
        $address = '';
        if (isset($_REQUEST['supplier_address']) && ! empty($_REQUEST['supplier_address'])) {
            $address = $_REQUEST['supplier_address'];
        }else{
            returnError("Nhập địa chỉ nhà cung cấp!");
        }
        
        $sql_create_supplier = "INSERT INTO tbl_material_supplier SET
                supplier_code = '" . $supplier_code . "',
                supplier_name = '" . $name . "',
                supplier_email = '" . $email . "',
                supplier_phone = '" . $phone_number . "',
                supplier_address = '" . $address . "'
        ";
        
        if ($conn->query($sql_create_supplier)) {
            returnSuccess("Thêm nhà cung cấp thành công!");
        } else {
            returnError("Thêm nhà cung cấp không thành công!");
        }
        
        break;
        
    case 'update_supplier':
        
        $id_supplier = '';
        if (isset($_REQUEST['id_supplier']) && ! empty($_REQUEST['id_supplier'])) {
            $id_supplier = $_REQUEST['id_supplier'];
        } else {
            returnError("Nhập id_supplier!");
        }
        
        $check = 0;
        
        if (isset($_REQUEST['supplier_code']) && ! empty($_REQUEST['supplier_code'])) {
            
            $id_code = $_REQUEST['supplier_code'];
            
            // check employee_code exists
            $sql_check_employee_code = "SELECT * FROM tbl_product_manufacturer WHERE supplier_code = '" . $id_code . "' AND id != '".$id_supplier."' 
            ";
            $result_check = $conn->query($sql_check_employee_code);
            $num_result_check = mysqli_num_rows($result_check);
            if ($num_result_check > 0) {
                returnError("Mã nhà cung cấp đã tồn tại!");
            }
            
            $check ++;
            $query = "UPDATE tbl_material_supplier SET ";
            $query .= " supplier_code  = '" . $id_code . "' ";
            $query .= " WHERE id = '" . $id_supplier . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            }
        }
        
        if (isset($_REQUEST['supplier_phone']) && ! empty($_REQUEST['supplier_phone'])) {
            
            $phone_number = $_REQUEST['supplier_phone'];
            
            $check ++;
            $query = "UPDATE tbl_material_supplier SET ";
            $query .= " supplier_phone  = '" . $phone_number . "' ";
            $query .= " WHERE id = '" . $id_supplier . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            }
        }
        
        if (isset($_REQUEST['supplier_name']) && ! empty($_REQUEST['supplier_name'])) {
            $check ++;
            $query = "UPDATE tbl_material_supplier SET ";
            $query .= " supplier_name  = '" . $_REQUEST['supplier_name'] . "' ";
            $query .= " WHERE id = '" . $id_supplier . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            }
        }
        if (isset($_REQUEST['supplier_email']) && ! empty($_REQUEST['supplier_email'])) {
            $check ++;
            $query = "UPDATE tbl_material_supplier SET ";
            $query .= " supplier_email  = '" . $_REQUEST['supplier_email'] . "' ";
            $query .= " WHERE id = '" . $id_supplier . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            }
        }
        if (isset($_REQUEST['supplier_address']) && ! empty($_REQUEST['supplier_address'])) {
            $check ++;
            $query = "UPDATE tbl_material_supplier SET ";
            $query .= " supplier_address  = '" . $_REQUEST['supplier_address'] . "' ";
            $query .= " WHERE id = '" . $id_supplier . "'";
            // Create post
            if ($conn->query($query)) {
                $check --;
            }
        }
        if ($check == 0) {
            returnSuccess("Cập nhật thành công!");
        } else {
            returnError("Cập nhật không thành công!");
        }
        
        break;
        
    case 'delete_supplier':
        $id_supplier = '';
        if (isset($_REQUEST['id_supplier']) && ! empty($_REQUEST['id_supplier'])) {
            $id_supplier = $_REQUEST['id_supplier'];
        } else {
            returnError("Nhập id_supplier!");
        }
        
        $sql_check_supplier_exists = "SELECT * FROM tbl_material_supplier WHERE id = '" . $id_supplier . "'";
        
        $result_check = mysqli_query($conn, $sql_check_supplier_exists);
        $num_result_check = mysqli_num_rows($result_check);
        
        if ($num_result_check > 0) {
            
            $check_supplier_material = "SELECT *
                            FROM tbl_material_material
                            WHERE  id_supplier = '" . $id_supplier . "'
                          ";
            
            $result_check_material = mysqli_query($conn, $check_supplier_material);
            $num_result_check_material = mysqli_num_rows($result_check_material);
            if ($num_result_check_material > 0) {
                returnError("Nhà cung cấp không thể xóa, đã có vật liệu.");
            }
            
            $sql_delete_supplier = "
                            DELETE FROM tbl_material_supplier
                            WHERE  id = '" . $id_supplier . "'
                          ";
            if ($conn->query($sql_delete_supplier)) {
                returnSuccess("Xóa nhà cung cấp thành công!");
            } else {
                returnError("Xóa nhà cung cấp không thành công!");
            }
        } else {
            returnError("Không tìm thấy nhân viên!");
        }
        
        break;
        
    case 'filter_':
        
        break;
        
    default:
        returnError("type_manager is not accept!");
        break;
}