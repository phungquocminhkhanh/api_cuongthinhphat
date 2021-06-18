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
$sql = "SELECT tbl_customer_customer.id as id,
                tbl_customer_customer.customer_name as customer_name,
                tbl_customer_customer.customer_code as customer_code,
                tbl_customer_customer.customer_sex as customer_sex,
                tbl_customer_customer.customer_birthday as customer_birthday,
                tbl_customer_customer.customer_email as customer_email,
                tbl_customer_customer.customer_phone as customer_phone

        FROM tbl_customer_customer
        WHERE 1=1 ";

if (! empty($filter)) {
    $sql .= " AND (tbl_customer_customer.customer_name LIKE '%" . $filter . "%'
                                    OR tbl_customer_customer.customer_phone LIKE '%" . $filter . "%')
        ";
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
    while($row = $result->fetch_assoc())
    {
        $c_item = array(
            'id' => $row['id'],
            'customer_name' => $row['customer_name'],
            'customer_code' => $row['customer_code'],
            'customer_sex' => $row['customer_sex'],
            'customer_birthday' => $row['customer_birthday'],
            'customer_email' => $row['customer_email'],
            'customer_phone' => $row['customer_phone']
        );
        
        // Push to "data"
        array_push($customer_arr['data'], $c_item);
        
    }
}
// Turn to JSON & output
echo json_encode($customer_arr);

mysqli_close($conn);

?>