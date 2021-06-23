<?php

if (isset($_REQUEST['type_address']) &&  !empty($_REQUEST['type_address'])) {
    $type_address = $_REQUEST['type_address'];
}else{
    returnError('Nhập type_address');
}
if (isset($_REQUEST['type_manager']) &&  !empty($_REQUEST['type_manager'])) {
    $type_manager = $_REQUEST['type_manager'];
}else{
    returnError("Nhập type_manager!");
}
if($type_address=='shipping')
{
    switch ($type_manager) {
        case 'list_customer_address':

            $sql = "SELECT * 
                    FROM tbl_customer_shipping
                    WHERE 1=1 ";

            if(isset($_REQUEST['id_customer']) && !(empty($_REQUEST['id_customer']))){
                $id_customer = $_REQUEST['id_customer'];
                $sql .="AND (`tbl_customer_shipping`.`id_customer` = '{$id_customer}') ";
            }

            if (isset($_REQUEST['id_address']) &&  !empty($_REQUEST['id_address'])) {
                $id_address = $_REQUEST['id_address'];
                $sql .="AND (`tbl_customer_shipping`.`id` = '$id_address') ";
            }
          
            $result = $conn->query($sql);
            $num = mysqli_num_rows($result);
            
            $arr_result = array();
            
            $arr_result['success'] = 'true';
            $arr_result['data'] = array();
            
            if ($num > 0) {
                while ($row = $result->fetch_assoc()) {
                    $address_item = array(
                        'id_shipping' => $row['id'],
                        'id_customer' => $row['id_customer'],
                        'shipping_reminiscent_name' => $row['shipping_reminiscent_name'],
                        'shipping_contact_person' => $row['shipping_contact_person'],
                        'shipping_contact_phone' => $row['shipping_contact_phone'],
                        'shipping_address' => $row['shipping_address'],
                        'shipping_default' => $row['shipping_default']

                    );
                    
                    // Push to "data"
                    array_push($arr_result['data'], $address_item);
                }
            }
            
            // Turn to JSON & output
            echo json_encode($arr_result);
            exit();
            
            break;
            
        case 'create_customer_address':
            if (isset($_REQUEST['id_customer']) &&  !empty($_REQUEST['id_customer'])) {
                $id_customer = $_REQUEST['id_customer'];
            }else{
                returnError('Nhập id_customer');
            }
            
            if (isset($_REQUEST['reminiscent_company_name']) &&  !empty($_REQUEST['reminiscent_company_name'])) {
                $shipping_reminiscent_name = $_REQUEST['reminiscent_company_name'];  
            }else{
                returnError("Nhập reminiscent_company_name");
            }

            if (isset($_REQUEST['contact_person']) &&  !empty($_REQUEST['contact_person'])) {
                $shipping_contact_person = $_REQUEST['contact_person'];
            }else{
                returnError("Nhập contact_person");
            }
         
            if (isset($_REQUEST['address_phone']) &&  !empty($_REQUEST['address_phone'])) {
                $shipping_contact_phone = $_REQUEST['address_phone'];
            }else{
                returnError("Nhập address_phone");
            }
            
            if (isset($_REQUEST['address_address']) &&  !empty($_REQUEST['address_address'])) {
                $shipping_address = $_REQUEST['address_address'];
            }else{
                returnError("Nhập address_address");
            }

            $sql_create_customer_branch = "INSERT INTO tbl_customer_shipping SET
                    shipping_reminiscent_name = '" . $shipping_reminiscent_name . "'
                  , shipping_contact_person = '" . $shipping_contact_person . "'
                  , shipping_contact_phone = '" . $shipping_contact_phone . "'
                  , shipping_address = '" . $shipping_address . "'
                  , id_customer  = '" . $id_customer  . "'
                  
            ";
            $check =0;
            if (isset($_REQUEST['address_default']) &&  !empty($_REQUEST['address_default'])) {
                $shipping_default = $_REQUEST['address_default'];
                $sql_create_customer_branch .=",shipping_default  = '" . $shipping_default  . "'";
                if($_REQUEST['address_default']=='Y')
                {
                    $query = "UPDATE tbl_customer_shipping SET ";
                    $query .= " shipping_default  = 'N' ";
                    $query .= " WHERE shipping_default = 'Y' 
                    AND id_customer = $id_customer
                    ";
                    // Create post
                    $check++;
                    if ($conn->query($query)) {
                        $check --;
                    }
                }
               
            }
            
            if ($conn->query($sql_create_customer_branch)) {
                returnSuccess("Thêm địa chỉ gửi đi thành công!");
            }else{
                returnError("Thêm địa chỉ gửi đi không thành công!");
            }
            
            break;
            
        case 'update_customer_address':
            
            $id_address = '';
            if (isset($_REQUEST['id_address']) &&  !empty($_REQUEST['id_address'])) {
                $id_address = $_REQUEST['id_address'];
            }else{
                returnError("Nhập id_address!");
            }
            
            if (isset($_REQUEST['id_customer']) &&  !empty($_REQUEST['id_customer'])) {
                $id_customer = $_REQUEST['id_customer'];
            }else{
                returnError("Nhập id_customer!");
            }

            $check = 0;
            
            if (isset($_REQUEST['reminiscent_company_name']) && ! empty($_REQUEST['reminiscent_company_name'])) {
                
                $shipping_reminiscent_name = $_REQUEST['reminiscent_company_name'];
                
                $check ++;
                $query = "UPDATE tbl_customer_shipping SET ";
                $query .= " shipping_reminiscent_name  = '" . $shipping_reminiscent_name . "' ";
                $query .= " WHERE id = '" . $id_address . "'";
                // Create post
                if ($conn->query($query)) {
                    $check --;
                }
            }
            
            if (isset($_REQUEST['contact_person']) && ! empty($_REQUEST['contact_person'])) {
                $check ++;
                $query = "UPDATE tbl_customer_shipping SET ";
                $query .= " shipping_contact_person  = '" . $_REQUEST['contact_person'] . "' ";
                $query .= " WHERE id = '" . $id_address . "'";
                // Create post
                if ($conn->query($query)) {
                    $check --;
                }
            }
            if (isset($_REQUEST['address_phone']) && ! empty($_REQUEST['address_phone'])) {
                $check ++;
                $query = "UPDATE tbl_customer_shipping SET ";
                $query .= " shipping_contact_phone  = '" . $_REQUEST['address_phone'] . "' ";
                $query .= " WHERE id = '" . $id_address . "'";
                // Create post
                if ($conn->query($query)) {
                    $check --;
                }
            }
            if (isset($_REQUEST['address_address']) && ! empty($_REQUEST['address_address'])) {
                $check ++;
                $query = "UPDATE tbl_customer_shipping SET ";
                $query .= " shipping_address  = '" . $_REQUEST['address_address'] . "' ";
                $query .= " WHERE id = '" . $id_address . "'";
                // Create post
                if ($conn->query($query)) {
                    $check --;
                }
            }

            if (isset($_REQUEST['address_default']) && ! empty($_REQUEST['address_default'])) {
                $check ++;
                if($_REQUEST['address_default']=='Y')
                {
                    $query1 = "UPDATE tbl_customer_shipping SET ";
                    $query1 .= " shipping_default  = 'N' ";
                    $query1 .= " WHERE shipping_default = 'Y' 
                    AND id_customer = $id_customer
                    ";
                    // Create post
                    if ($conn->query($query1)) {

                    }
                }
                $query = "UPDATE tbl_customer_shipping SET ";
                $query .= " shipping_default  = '" . $_REQUEST['address_default'] . "' ";
                $query .= " WHERE id = '" . $id_address . "'";
                // Create post
                if ($conn->query($query)) {
                    $check --;
                }
            }
            
            if ($check == 0) {
                returnSuccess("Cập nhật thành công!");
            } else {
                returnError("Cập nhật không thành công");
            }
            
            break;
            
        case 'delete_customer_address':
            
            $id_address = '';
            if (isset($_REQUEST['id_address']) &&  !empty($_REQUEST['id_address'])) {
                $id_address = $_REQUEST['id_address'];
            }else{
                returnError("Nhập id_address!");
            }
            
            $sql_check_customer_branch_exists = "SELECT * FROM tbl_customer_shipping WHERE id = '" . $id_address . "'";
            
            $result_check = mysqli_query($conn, $sql_check_customer_branch_exists);
            $num_result_check = mysqli_num_rows($result_check);
            
            if ($num_result_check > 0) {
                $sql_delete_customer_branch = "
                                DELETE FROM tbl_customer_shipping
                                WHERE  id = '" . $id_address . "'
                              ";
                if ($conn->query($sql_delete_customer_branch)) {
                    returnSuccess("Xóa địa chỉ thành công!");
                } else {
                    returnError("Xóa địa chỉ không thành công!");
                }
            }else{
                returnError('Không tìm thấy id_address');
            }
            
            break;
        
        default:
            returnError("type_manager is not accept!");
            break;
    }
}
elseif($type_address=='delivery')
{
    switch ($type_manager) {
        case 'list_customer_address':

            $sql = "SELECT * 
                    FROM tbl_customer_delivery
                    WHERE 1=1 ";

            if(isset($_REQUEST['id_customer']) && !(empty($_REQUEST['id_customer']))){
                $id_customer = $_REQUEST['id_customer'];
                $sql .="AND (`tbl_customer_delivery`.`id_customer` = '{$id_customer}') ";
            }

            if (isset($_REQUEST['id_address']) &&  !empty($_REQUEST['id_address'])) {
                $id_address = $_REQUEST['id_address'];
                $sql .="AND (`tbl_customer_delivery`.`id` = '$id_address') ";
            }
          
            $result = $conn->query($sql);
            $num = mysqli_num_rows($result);
            
            $arr_result = array();
            
            $arr_result['success'] = 'true';
            $arr_result['data'] = array();
            
            if ($num > 0) {
                while ($row = $result->fetch_assoc()) {
                    $address_item = array(
                        'id_delivery' => $row['id'],
                        'id_customer' => $row['id_customer'],
                        'delivery_company' => $row['delivery_company'],
                        'delivery_deputy_person' => $row['delivery_deputy_person'],
                        'delivery_deputy_phone' => $row['delivery_deputy_phone'],
                        'delivery_address' => $row['delivery_address'],
                        'delivery_default' => $row['delivery_default']

                    );
                    
                    // Push to "data"
                    array_push($arr_result['data'], $address_item);
                }
            }
            
            // Turn to JSON & output
            echo json_encode($arr_result);
            exit();
            break;
            
        case 'create_customer_address':
            
            if (isset($_REQUEST['id_customer']) &&  !empty($_REQUEST['id_customer'])) {
                $id_customer = $_REQUEST['id_customer'];  
            }else{
                returnError("Nhập reminiscent_company_name");
            }
            if (isset($_REQUEST['reminiscent_company_name']) &&  !empty($_REQUEST['reminiscent_company_name'])) {
                $delivery_company = $_REQUEST['reminiscent_company_name'];  
            }else{
                returnError("Nhập reminiscent_company_name");
            }

            if (isset($_REQUEST['contact_person']) &&  !empty($_REQUEST['contact_person'])) {
                $delivery_deputy_person = $_REQUEST['contact_person'];
            }else{
                returnError("Nhập contact_person");
            }
         
            if (isset($_REQUEST['address_phone']) &&  !empty($_REQUEST['address_phone'])) {
                $delivery_deputy_phone = $_REQUEST['address_phone'];
            }else{
                returnError("Nhập address_phone");
            }
            
            if (isset($_REQUEST['address_address']) &&  !empty($_REQUEST['address_address'])) {
                $delivery_address = $_REQUEST['address_address'];
            }else{
                returnError("Nhập address_address");
            }

            $sql_create_customer_branch = "INSERT INTO tbl_customer_delivery SET
                    delivery_company = '" . $delivery_company . "'
                  , delivery_deputy_person = '" . $delivery_deputy_person . "'
                  , delivery_deputy_phone = '" . $delivery_deputy_phone . "'
                  , delivery_address = '" . $delivery_address . "'
                  , id_customer  = '" . $id_customer  . "'
                  
            ";
            $check =0;
            if (isset($_REQUEST['address_default']) &&  !empty($_REQUEST['address_default'])) {
                $delivery_default = $_REQUEST['address_default'];
                $sql_create_customer_branch .=",delivery_default  = '" . $delivery_default  . "'";

                if($_REQUEST['address_default']=='Y')
                {
                    $query = "UPDATE tbl_customer_delivery SET ";
                    $query .= " delivery_default  = 'N' ";
                    $query .= " WHERE delivery_default = 'Y' 
                    AND id_customer = $id_customer
                    ";
                    // Create post
                    $check++;
                    if ($conn->query($query)) {
                        $check --;
                    }
                }
               
            }
            
            if ($conn->query($sql_create_customer_branch)) {
                returnSuccess("Thêm địa chỉ nhận  thành công!");
            }else{
                returnError("Thêm địa chỉ nhận không thành công!");
            }
            
            break;
            
        case 'update_customer_address':
            
            $id_address = '';
            if (isset($_REQUEST['id_address']) &&  !empty($_REQUEST['id_address'])) {
                $id_address = $_REQUEST['id_address'];
            }else{
                returnError("Nhập id_address!");
            }
            
            if (isset($_REQUEST['id_customer']) &&  !empty($_REQUEST['id_customer'])) {
                $id_customer = $_REQUEST['id_customer'];
            }else{
                returnError("Nhập id_customer!");
            }

            $check = 0;
            
            if (isset($_REQUEST['reminiscent_company_name']) && ! empty($_REQUEST['reminiscent_company_name'])) {
                
                $delivery_company = $_REQUEST['reminiscent_company_name'];
                
                $check ++;
                $query = "UPDATE tbl_customer_delivery SET ";
                $query .= " delivery_company  = '" . $delivery_company . "' ";
                $query .= " WHERE id = '" . $id_address . "'";
                // Create post
                if ($conn->query($query)) {
                    $check --;
                }
            }
            
            if (isset($_REQUEST['contact_person']) && ! empty($_REQUEST['contact_person'])) {
                $check ++;
                $query = "UPDATE tbl_customer_delivery SET ";
                $query .= " delivery_deputy_person  = '" . $_REQUEST['contact_person'] . "' ";
                $query .= " WHERE id = '" . $id_address . "'";
                // Create post
                if ($conn->query($query)) {
                    $check --;
                }
            }
            if (isset($_REQUEST['address_phone']) && ! empty($_REQUEST['address_phone'])) {
                $check ++;
                $query = "UPDATE tbl_customer_delivery SET ";
                $query .= " delivery_deputy_phone  = '" . $_REQUEST['address_phone'] . "' ";
                $query .= " WHERE id = '" . $id_address . "'";
                // Create post
                if ($conn->query($query)) {
                    $check --;
                }
            }
            if (isset($_REQUEST['address_address']) && ! empty($_REQUEST['address_address'])) {
                $check ++;
                $query = "UPDATE tbl_customer_delivery SET ";
                $query .= " delivery_address  = '" . $_REQUEST['address_address'] . "' ";
                $query .= " WHERE id = '" . $id_address . "'";
                // Create post
                if ($conn->query($query)) {
                    $check --;
                }
            }

            if (isset($_REQUEST['address_default']) && ! empty($_REQUEST['address_default'])) {
                $check ++;
                if($_REQUEST['address_default']=='Y')
                {
                    $query1 = "UPDATE tbl_customer_delivery SET ";
                    $query1 .= " delivery_default  = 'N' ";
                    $query1 .= " WHERE delivery_default = 'Y' 
                    AND id_customer = $id_customer
                    ";
                    // Create post
                    if ($conn->query($query1)) {

                    }
                }
                $query = "UPDATE tbl_customer_delivery SET ";
                $query .= " delivery_default  = '" . $_REQUEST['address_default'] . "' ";
                $query .= " WHERE id = '" . $id_address . "'";
                // Create post
                if ($conn->query($query)) {
                    $check --;
                }
            }
            
            if ($check == 0) {
                returnSuccess("Cập nhật thành công!");
            } else {
                returnError("Cập nhật không thành công");
            }
            
            break;
            
        case 'delete_customer_address':
            
            $id_address = '';
            if (isset($_REQUEST['id_address']) &&  !empty($_REQUEST['id_address'])) {
                $id_address = $_REQUEST['id_address'];
            }else{
                returnError("Nhập id_address!");
            }
            
            $sql_check_customer_branch_exists = "SELECT * FROM tbl_customer_delivery WHERE id = '" . $id_address . "'";
            
            $result_check = mysqli_query($conn, $sql_check_customer_branch_exists);
            $num_result_check = mysqli_num_rows($result_check);
            
            if ($num_result_check > 0) {
                $sql_delete_customer_branch = "
                                DELETE FROM tbl_customer_delivery
                                WHERE  id = '" . $id_address . "'
                              ";
                if ($conn->query($sql_delete_customer_branch)) {
                    returnSuccess("Xóa địa chỉ thành công!");
                } else {
                    returnError("Xóa địa chỉ không thành công!");
                }
            }else{
                returnError('Không tìm thấy id_address');
            }
            
            break;
        
        default:
            returnError("type_manager is not accept!");
            break;
    }
}else{
    returnError('Không tồn tại type_address');
}


    