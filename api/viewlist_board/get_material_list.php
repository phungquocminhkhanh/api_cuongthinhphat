<?php
$typeManager = '';
if (isset($_REQUEST['type_manager'])) {
    if ($_REQUEST['type_manager'] == '') {
        unset($_REQUEST['type_manager']);
    }else{
        $typeManager = $_REQUEST['type_manager'];
    }
}

$id_supplier = '';
if (isset($_REQUEST['id_supplier']) && ! empty($_REQUEST['id_supplier'])) {
    $id_supplier = $_REQUEST['id_supplier'];
} else {
    if (empty($typeManager))
        returnError("Nhập id_supplier!");
}
// query
$sql = "SELECT
            tbl_material_material.id as id,
            tbl_material_material.material_name as material_name,
            tbl_material_material.material_code as material_code,
            tbl_material_material.material_spec as material_spec,
            tbl_material_material.safety_stock as safety_stock,
            tbl_material_material.id_unit as id_unit,
            tbl_material_material.id_supplier as id_supplier,

            tbl_product_unit.id as id_unit,
            tbl_product_unit.unit_title as unit_title,
            tbl_product_unit.unit as unit,

            tbl_material_supplier.supplier_name as supplier_name

          FROM tbl_material_material

          LEFT JOIN tbl_material_supplier
          ON tbl_material_material.id_supplier = tbl_material_supplier.id

          LEFT JOIN tbl_product_unit 
          ON tbl_product_unit.id = tbl_material_material.id_unit

          WHERE 1=1
         ";

if (! empty($id_supplier)) {
    $sql .= " AND id_supplier = '" . $id_supplier . "'";
}

$result = $conn->query($sql);
$num = mysqli_num_rows($result);

$arr_result['success'] = 'true';
$arr_result['data'] = array();

if ($num > 0) {
    while ($row = $result->fetch_assoc()) {
        $material_item = array(
            'id' => $row['id'],
            'material_name' => $row['material_name'],
            'material_code' => $row['material_code'],
            'material_spec' => $row['material_spec'],
            'safety_stock' => $row['safety_stock'],
            'id_supplier' => $row['id_supplier'],
            'supplier_name' => $row['supplier_name'],
            'unit' => $row['unit'],
            'unit_title' => $row['unit_title'],
            'id_unit' => $row['id_unit']
        
        );
        
        // Push to "data"
        array_push($arr_result['data'], $material_item);
    }
}

// Turn to JSON & output
echo json_encode($arr_result);
