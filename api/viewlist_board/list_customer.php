<?php
$filter = '';
if (isset($_REQUEST['filter'])) {
    if ($_REQUEST['filter'] == '') {
        unset($_REQUEST['filter']);
    } else {
        $filter = $_REQUEST['filter'];
    }
}

$customer_arr = array();
// get total customer
$sql="SELECT count(tbl_customer_customer.id) as customer_total  FROM tbl_customer_customer WHERE 1=1 ";

if (! empty($filter)) {
    $sql .= " AND (tbl_customer_customer.customer_name LIKE '%" . $filter . "%'
                                    OR tbl_customer_customer.customer_phone LIKE '%" . $filter . "%')
        ";
}
if (isset($_REQUEST['id_customer']) && ! empty($_REQUEST['id_customer'])) {
    $id_customer=$_REQUEST['id_customer'];

    $sql .= " AND id='" . $id_customer. "'";
}
$result = mysqli_query($conn,$sql);
while($row = $result->fetch_assoc())
{
    $customer_arr['total']= $row['customer_total'];
}

$limit=20;
$page=1;
if ( isset($_REQUEST['limit']) && $_REQUEST['limit']!='' ){
    $limit=$_REQUEST['limit'];
}
if ( isset($_REQUEST['page']) && $_REQUEST['page']!='' ){
    $page=$_REQUEST['page'];
}


$customer_arr['total_page']= strval(ceil($customer_arr['total']/$limit));

$customer_arr['limit']=strval($limit);
$start=($page-1)*$limit;

// query
$sql = "SELECT *

        FROM tbl_customer_customer
        WHERE 1=1 ";

if (! empty($filter)) {
    $sql .= " AND (tbl_customer_customer.customer_name LIKE '%" . $filter . "%'
                                    OR tbl_customer_customer.customer_phone LIKE '%" . $filter . "%')
        ";
}
if (! empty($id_customer)) {
    
    $sql .= " AND tbl_customer_customer.id = '" . $id_customer . "' ";
}    
$sql .= " ORDER BY tbl_customer_customer.id DESC LIMIT $start,$limit ";
$result = mysqli_query($conn,$sql);

// Get row count
$num = mysqli_num_rows($result);

// Check if any categories

$customer_arr['success'] = 'true';
$customer_arr['page']=  $page;
$customer_arr['data'] = array();

if($num > 0) {
    // Cat array
    while($rowItemCustomer = $result->fetch_assoc())
    {   
        
        $c_item = array(
            'id' => $rowItemCustomer['id'],
            'customer_phone' => $rowItemCustomer['customer_phone'],
            'customer_name' => $rowItemCustomer['customer_name'],
            'customer_register' => $rowItemCustomer['customer_register'],
            'customer_address' => $rowItemCustomer['customer_address']!=null?$rowItemCustomer['customer_address']:"",
            'customer_status' => $rowItemCustomer['customer_status'],
            'customer_code' => $rowItemCustomer['customer_code'] != null ? $rowItemCustomer['customer_code'] : "",
            'customer_enterprise' => $rowItemCustomer['customer_enterprise'] != null ? $rowItemCustomer['customer_enterprise'] : "",
            'id_admin' => $rowItemCustomer['id_admin'] != null ? $rowItemCustomer['id_admin'] : "",
            'customer_email' => $rowItemCustomer['customer_email'] != null ? $rowItemCustomer['customer_email'] : "",
             'customer_company' => $rowItemCustomer['customer_company'] != null ? $rowItemCustomer['customer_company'] : "",
           
            'login_type' => 'customer'
        );
        //get dia chi gui
            if(isset($_REQUEST['customer_delivery']) && ! empty($_REQUEST['customer_delivery']))
            {
                $c_item['customer_delivery']=array();
                 $sql_customer_delivery="SELECT * 
                                    FROM tbl_customer_delivery
                                    WHERE id_customer= '" . $rowItemCustomer["id"] . "'";
                $result_customer_delivery=mysqli_query($conn,$sql_customer_delivery);
              
                while($delivery = $result_customer_delivery->fetch_assoc())
                {
                    $tam_delivery = array(
                            'id_delivery' => $delivery['id'],
                            'id_customer' => $delivery['id_customer'],
                            'delivery_company' => $delivery['delivery_company'],
                            'delivery_deputy_person' => $delivery['delivery_deputy_person'],
                            'delivery_deputy_phone' => $delivery['delivery_deputy_phone'],
                            'delivery_address' => $delivery['delivery_address'],
                            'delivery_default' => $delivery['delivery_default']

                        );
                    
                     array_push($c_item['customer_delivery'], $tam_delivery);
                }  
            }
           
        // get dia chi nhan
            if(isset($_REQUEST['customer_shipping']) && ! empty($_REQUEST['customer_shipping']))
            {
                $c_item['customer_shipping']=array();
                 $sql_customer_shipping="SELECT * 
                                    FROM tbl_customer_shipping
                                    WHERE id_customer= '" .$rowItemCustomer["id"] . "'";
                $result_customer_shipping=mysqli_query($conn,$sql_customer_shipping);
              
                while($shipping = $result_customer_shipping->fetch_assoc())
                {
                    $tam_shipping = array(
                            'id_shipping' => $shipping['id'],
                            'id_customer' => $shipping['id_customer'],
                            'shipping_reminiscent_name' => $shipping['shipping_reminiscent_name'],
                            'shipping_contact_person' => $shipping['shipping_contact_person'],
                            'shipping_contact_phone' => $shipping['shipping_contact_phone'],
                            'shipping_address' => $shipping['shipping_address'],
                            'shipping_default' => $shipping['shipping_default']

                        );
                    
                     array_push($c_item['customer_shipping'], $tam_shipping);
                }  
            }
         // get dia chi nhan
            if(isset($_REQUEST['customer_order']) && ! empty($_REQUEST['customer_order']))
            {
                $c_item['customer_shipping']=array();
                 $sql_customer_shipping="SELECT * 
                                    FROM tbl_customer_shipping
                                    WHERE id_customer= '" .$rowItemCustomer["id"] . "'";
                $result_customer_shipping=mysqli_query($conn,$sql_customer_shipping);
              
                while($shipping = $result_customer_shipping->fetch_assoc())
                {
                    $tam_shipping = array(
                            'id_shipping' => $shipping['id'],
                            'id_customer' => $shipping['id_customer'],
                            'shipping_reminiscent_name' => $shipping['shipping_reminiscent_name'],
                            'shipping_contact_person' => $shipping['shipping_contact_person'],
                            'shipping_contact_phone' => $shipping['shipping_contact_phone'],
                            'shipping_address' => $shipping['shipping_address'],
                            'shipping_default' => $shipping['shipping_default']

                        );
                    
                     array_push($c_item['customer_shipping'], $tam_shipping);
                }  
            }   
        // Push to "data"
        array_push($customer_arr['data'], $c_item);
        
    }
}
// Turn to JSON & output
echo json_encode($customer_arr);

mysqli_close($conn);

?>