<?php

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
//header("Access-Control-Allow-Methods: GET");
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once 'basic_auth.php';
include_once '../lib/connect.php';

include_once '../lib/reuse_function.php';

// check if data recived is from raw - if so, assign it to $_REQUEST
if (! isset($_REQUEST['detect'])) {
    // get raw json data
    $_REQUEST = json_decode(file_get_contents('php://input'), true);
    if (! isset($_REQUEST['detect'])) {
        echo json_encode(array(
            'message' => 'detect parameter not found !'
        ));
        exit();
    }
}
// handle detect value
$detect = $_REQUEST['detect'];

switch ($detect) {
    // Quang's links
    
    /**
     * viewlist_board*
     */
    case 'user_login':
        {
            include_once 'viewlist_board/login.php';
            break;
        }
    
    case 'check_sign_out':
        {
            include_once 'viewlist_board/check_sign_out.php';
            break;
        }
    case 'get_support_hotline':
        {
            include_once 'viewlist_board/get_support_hotline.php';
            break;
        }
    case 'get_company_intro':
        {
            include_once 'viewlist_board/get_company_intro.php';
            break;
        }
    case 'get_company_slide':
        {
            include_once 'viewlist_board/get_company_slide.php';
            break;
        }
    case 'get_company_product_feature':
        {
            include_once 'viewlist_board/get_company_product_feature.php';
            break;
        }
    case 'get_customer_product':
        {
            include_once 'viewlist_board/get_customer_product.php';
            break;
        }
    case 'list_order':
        {
            include_once 'viewlist_board/list_order.php';
            break;
        }
    case 'list_order_detail':
        {
            include_once 'viewlist_board/list_order_detail.php';
            break;
        }    
    case 'list_order_history':
        {
            include_once 'viewlist_board/list_order_history.php';
            break;
        }
    case 'get_storeage_info':
        {
            include_once 'viewlist_board/get_storeage_info.php';
            break;
        }
    case 'list_order_production':
        {
            include_once 'viewlist_board/list_order_production.php';
            break;
        }
    case 'list_order_export':
        {
            include_once 'viewlist_board/list_order_export.php';
            break;
        }
    case 'get_list_order_log':
        {
            include_once 'viewlist_board/get_list_order_log.php';
            break;
        }
    case 'get_order_tracking':
        {
            include_once 'viewlist_board/get_order_tracking.php';
            break;
        }
    case 'get_supplier_list':
        {
            include_once 'viewlist_board/get_supplier_list.php';
            break;
        }
    case 'get_material_list':
        {
            include_once 'viewlist_board/get_material_list.php';
            break;
        }
    case 'get_unit_list':
        {
            include_once 'viewlist_board/get_unit_list.php';
            break;
        }
    case 'get_supplier_list':
        {
            include_once 'viewlist_board/get_supplier_list.php';
            break;
        }
    case 'get_machine_list':
        {
            include_once 'viewlist_board/get_machine_list.php';
            break;
        }
    case 'get_company_intro':
        {
            include_once 'viewlist_board/get_company_intro.php';
            break;
        }
    case 'update_status_order':
        {
            include_once 'viewlist_board/update_status_order.php';
            break;
        }
    
    
    /**
     * Customer_board*
     */
    case 'customer_register':
        {
            include_once 'customer_board/register.php';
            break;
        }
    case 'customer_forgot_pw':
        {
            include_once 'customer_board/forgot_password.php';
            break;
        }
    case 'customer_change_info':
        {
            include_once 'customer_board/update_personal.php';
            break;
        }
    case 'customer_phone_exist':
        {
            include_once 'customer_board/check_phone_exist.php';
            break;
        }
    case 'customer_address_manager':
        {
            include_once 'customer_board/customer_address_manager.php';
            break;
        }
        
    case 'create_order':
        {
            include_once 'customer_board/create_order.php';
            break;
        }
    case 'cancel_order':
        {
            include_once 'customer_board/cancel_order.php';
            break;
        }
    
    /**
     * Employee_board*
     */
    case 'update_item_actual_manufacturing':
        {
            include_once 'employee_board/update_item_actual_manufacturing.php';
            break;
        }
    case 'update_employee_password':
        {
            include_once 'employee_board/update_employee_password.php';
            break;
        }
    case 'update_order_production_status':
        {
            include_once 'employee_board/update_order_production_status.php';
            break;
        }
    case 'update_order_export_status':
        {
            include_once 'employee_board/update_order_export_status.php';
            break;
        }
    case 'create_import_material':
        {
            include_once 'employee_board/create_import_material.php';
            break;
        }
    case 'count_order_warehouse':
        {
            include_once 'employee_board/count_order_warehouse.php';
            break;
        }
    
    /**
     * Admin_board*
     */
        
    case 'version_manager':
        {
            include_once 'admin_board/version_manager.php';
            break;
        }
        
    case 'account_manager':
        {
            include_once 'admin_board/account_manager.php';
            break;
        }
    case 'account_type_manager':
        {
            include_once 'admin_board/account_type_manager.php';
            break;
        }
        
    case 'customer_manager':
        {
            include_once 'admin_board/customer_manager.php';
            break;
        }
        
    case 'company_intro_manager':
        {
            include_once 'admin_board/company_intro_manager.php';
            break;
        }
    case 'company_support_contact_manager':
        {
            include_once 'admin_board/company_support_contact_manager.php';
            break;
        }
    case 'create_order_production':
        {
            include_once 'admin_board/create_order_production.php';
            break;
        }
    case 'create_order_export_storeage':
        {
            include_once 'admin_board/create_order_export_storeage.php';
            break;
        }
        
    case 'force_signout':
        {
            include_once 'admin_board/force_signout.php';
            break;
        }
        
    case 'get_role_permission':
        {
            include_once 'admin_board/get_role_permission.php';
            break;
        }
        
    case 'material_manager':
        {
            include_once 'admin_board/material_manager.php';
            break;
        }
        
    case 'product_manager':
        {
            include_once 'admin_board/product_manager.php';
            break;
        }
    case 'product_feature_manager':
        {
            include_once 'admin_board/product_feature_manager.php';
            break;
        }
    case 'product_machine_manager':
        {
            include_once 'admin_board/product_machine_manager.php';
            break;
        }
    case 'product_unit_manager':
        {
            include_once 'admin_board/product_unit_manager.php';
            break;
        }
    
    case 'role_permission_manager':
        {
            include_once 'admin_board/role_permission_manager.php';
            break;
        }
        
    case 'slide_manager':
        {
            include_once 'admin_board/slide_manager.php';
            break;
        }
    case 'supplier_manager':
        {
            include_once 'admin_board/supplier_manager.php';
            break;
        }
        
        
    // end Quang's links
    
    default:
        {
            echo json_encode(array(
                'success' => 'false',
                'message' => 'detect has been failed !'
            ));
            break;
        }
}

// edit file in new branch
?>