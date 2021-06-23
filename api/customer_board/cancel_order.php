<?php
$id_order = '';
if (isset($_REQUEST['id_order']) && ! empty($_REQUEST['id_order'])) {
    $id_order = $_REQUEST['id_order'];
} else {
    returnError("Nhập id_order!");
}
$sql="SELECT order_status FROM  tbl_order_order WHERE id='" . $id_order . "'";
$result = mysqli_query($conn, $sql);
$row = $result->fetch_assoc();

if($row["order_status"]=='1')
{
        $order_record_cancel_note = '';
    if (isset($_REQUEST['order_record_cancel_note']) && ! empty($_REQUEST['order_record_cancel_note'])) {
        $order_record_cancel_node = $_REQUEST['order_record_cancel_note'];
    }
    else {
        returnError("Nhập order_record_cancle_node!");
    }
    $query = "UPDATE tbl_order_order 
                    SET 
                        order_status='6',
                        order_record_cancel_note='$order_record_cancel_note' 
                    WHERE id='$id_order'
                        ";
    if ($conn->query($query)) {
        returnSuccess("Hủy đơn hàng thành công!");
    }
    else
    {
        returnError($query);
    }
}
else
{
    returnError("Chỉ hủy được đang hàng ở trạng thái chờ xác nhận");
}

?>