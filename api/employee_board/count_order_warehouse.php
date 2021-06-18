<?php
$order_arr = array();

$count_order_export = 0;
$count_order_import = 0;
$count_order_export_nvl = 0;

$sql_count_order_export = "SELECT count(tbl_export_storage.id) as order_count  
                    FROM tbl_export_storage
                     WHERE tbl_export_storage.delivery_status = 'SET'
                   ";

$result_count_order_export = mysqli_query($conn, $sql_count_order_export);
while ($row_count_order_export = $result_count_order_export->fetch_assoc()) {
    $count_order_export = $row_count_order_export['order_count'];
}

$sql_count_order_import = "SELECT count(tbl_production_production.id) as order_count
                    FROM tbl_production_production
                     WHERE tbl_production_production.production_status = 'IMP'
                   ";

$result_count_order_import = mysqli_query($conn, $sql_count_order_import);
while ($row_count_order_import = $result_count_order_import->fetch_assoc()) {
    $count_order_import = $row_count_order_import['order_count'];
}

$sql_count_order_export_nvl = "SELECT count(tbl_production_production.id) as order_count
                    FROM tbl_production_production
                     WHERE tbl_production_production.production_status = 'SET'
                   ";

$result_count_order_export_nvl = mysqli_query($conn, $sql_count_order_export_nvl);
while ($row_count_order_export_nvl = $result_count_order_export_nvl->fetch_assoc()) {
    $count_order_export_nvl = $row_count_order_export_nvl['order_count'];
}

$order_arr['success'] = 'true';
$order_arr['data'] = array();

$arr_export = array(
    'count_order_export'=> (String) $count_order_export,
    'count_order_export_nvl'=> (String) $count_order_export_nvl,
    'count_order_import'=> (String) $count_order_import
);

array_push($order_arr['data'], $arr_export);

// Turn to JSON & output
echo json_encode($order_arr);