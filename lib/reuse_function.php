<?php

// FORMAT TO VND PRICE
function vnd_format($num1)
{
    $result = number_format($num1, 0, ',', '.') . ' Ä�';
    return $result;
}

// SHORTEN STRING
function get_words($str, $wordCount)
{
    return implode('', array_slice(preg_split('/([\s,\.;\?\!]+)/', $str, $wordCount * 2 + 1, PREG_SPLIT_DELIM_CAPTURE), 0, $wordCount * 2 - 1));
}

// stripUnicode
function stripUnicode($str)
{
    if (! $str)
        return false;
    $unicode = array(
        'a' => 'Ã¡|Ã |áº£|Ã£|áº¡|Äƒ|áº¯|áº·|áº±|áº³|áºµ|Ã¢|áº¥|áº§|áº©|áº«|áº­',
        'd' => 'Ä‘',
        'e' => 'Ã©|Ã¨|áº»|áº½|áº¹|Ãª|áº¿|á»�|á»ƒ|á»…|á»‡',
        'i' => 'Ã­|Ã¬|á»‰|Ä©|á»‹',
        'o' => 'Ã³|Ã²|á»�|Ãµ|á»�|Ã´|á»‘|á»“|á»•|á»—|á»™|Æ¡|á»›|á»�|á»Ÿ|á»¡|á»£',
        'u' => 'Ãº|Ã¹|á»§|Å©|á»¥|Æ°|á»©|á»«|á»­|á»¯|á»±',
        'y' => 'Ã½|á»³|á»·|á»¹|á»µ'
    );
    foreach ($unicode as $nonUnicode => $uni)
        $str = preg_replace("/($uni)/i", $nonUnicode, $str);
    return $str;
}

// Normalize ckeditor text
function stripCKeditor($string)
{
    return strip_tags(html_entity_decode($string, ENT_COMPAT, 'UTF-8'));
}

// turn \r\n to \n because mobile use \n as linebreak
function changeLineBreak($string)
{
    return preg_replace("/\r\n/", "\n", $string);
}

function getRandomString($length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';
    for ($i = 0; $i < $length; $i ++) {
        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }
    return $string;
}

function returnError($string)
{
    echo json_encode(array(
        'success' => 'false',
        'message' => $string
    ));
    exit();
}

function returnSuccess($string)
{
    echo json_encode(array(
        'success' => 'true',
        'message' => $string
    ));
    exit();
}

/*
 * Description: Distance calculation from the latitude/longitude of 2 points
 */
function distanceCalculation($point1_lat, $point1_long, $point2_lat, $point2_long, $unit = 'km', $decimals = 2)
{
    // Calculate the distance in degrees
    $degrees = rad2deg(acos((sin(deg2rad($point1_lat)) * sin(deg2rad($point2_lat))) + (cos(deg2rad($point1_lat)) * cos(deg2rad($point2_lat)) * cos(deg2rad($point1_long - $point2_long)))));
    
    // Convert the distance in degrees to the chosen unit (kilometres, miles or nautical miles)
    switch ($unit) {
        case 'km':
            $distance = $degrees * 111.13384; // 1 degree = 111.13384 km, based on the average diameter of the Earth (12,735 km)
            break;
        case 'mi':
            $distance = $degrees * 69.05482; // 1 degree = 69.05482 miles, based on the average diameter of the Earth (7,913.1 miles)
            break;
        case 'nmi':
            $distance = $degrees * 59.97662; // 1 degree = 59.97662 nautic miles, based on the average diameter of the Earth (6,876.3 nautical miles)
    }
    return round($distance, $decimals);
}

// Create random string
function generateRandomString($length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i ++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// tinh khoang cach 2 diem
function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2)
{
    $theta = $longitude1 - $longitude2;
    $miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
    $miles = acos($miles);
    $miles = rad2deg($miles);
    $miles = $miles * 60 * 1.1515;
    $feet = $miles * 5280;
    $yards = $feet / 3;
    $kilometers = $miles * 1.609344;
    $meters = $kilometers * 1000;
    return compact('miles', 'feet', 'yards', 'kilometers', 'meters');
}

function getRolePermission($idUser = '', $conn)
{
    $sql = "SELECT * FROM tbl_admin_permission";
    
    if (! empty($idUser)) {
        $sql = " SELECT 
            tbl_admin_permission.id,
            tbl_admin_permission.permission,
            tbl_admin_permission.description

            FROM tbl_admin_authorize
            LEFT JOIN tbl_admin_permission
            ON tbl_admin_permission.id = tbl_admin_authorize.grant_permission

            WHERE tbl_admin_authorize.id_admin = '" . $idUser . "'
			
			ORDER BY tbl_admin_authorize.grant_permission ASC
        ";
    }
    
    $result = mysqli_query($conn, $sql);
    // mysqli_close($conn);
    // Get row count
    $num = mysqli_num_rows($result);
    $arr_result = array();
    // Check if any item
    if ($num > 0) {
        
        while ($row = $result->fetch_assoc()) {
            
            $role_item = array(
                'id' => $row['id'],
                'permission' => $row['permission'],
                'description' => $row['description']
            
            );
            // Push to "data"
            array_push($arr_result, $role_item);
        }
    }
    
    return $arr_result;
}

function saveImage($file, $target_save = '')
{
    $link_image = '';
    if (isset($file) && is_uploaded_file($file['tmp_name'])) {
        // check file size (1048576: 1MB) 5242880
        
        if ($file['size'] >= 5242880) {
            // returnError("only accept file size < 5MB!");
            
            return "error_size_img";
        }
        
        // check file type
        $allowedTypes = array(
            IMAGETYPE_PNG,
            IMAGETYPE_JPEG,
            IMAGETYPE_GIF
        );
        $detectedType = exif_imagetype($file['tmp_name']);
        $error = ! in_array($detectedType, $allowedTypes);
        
        if ($error) {
            // returnError("only accept PNG, JPEG, GIF !");
            return "error_type_img";
        }
        
        $target_dir = $target_save;
        $target_dir_4_upload = '../' . $target_save;
        $final_name = basename($file["name"]);
        
        $path = $file['name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $final_name = generateRandomString(60) . '.' . $ext;
        
        // end handle way to rename
        
        while (file_exists($target_dir_4_upload . $final_name)) {
            // doi ten file
            $final_name = generateRandomString(60) . '.' . $ext;
        }
        
        // upload file toi folder icon
        $target_file_upload = $target_dir_4_upload . $final_name;
        $target_file = $target_dir . $final_name;
        
        move_uploaded_file($file["tmp_name"], $target_file_upload);
        
        $link_image = $target_file;
    }
    
    return $link_image;
}

function getUserAddress($lat, $lng)
{
    $apiKey = 'AIzaSyDdO5M8_PPHAxaFyW9Hh6HhSIXeCtbPgGo';
    
    $geo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($lat) . ',' . trim($lng) . '&sensor=false&key=' . $apiKey);
    
    // We convert the JSON to an array
    $geo = json_decode($geo, true);
    
    // If everything is cool
    if ($geo['status'] = 'OK') {
        return $geo['results'][0]['formatted_address'];
    }
    
    return "";
}

function get_lat_long($address)
{
    $array = array();
    $apiKey = 'AIzaSyDdO5M8_PPHAxaFyW9Hh6HhSIXeCtbPgGo';
    
    $geo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&sensor=false&key=' . $apiKey);
    
    // We convert the JSON to an array
    $geo = json_decode($geo, true);
    
    // If everything is cool
    if ($geo['status'] = 'OK') {
        $latitude = $geo['results'][0]['geometry']['location']['lat'];
        $longitude = $geo['results'][0]['geometry']['location']['lng'];
        $array = array(
            'lat' => $latitude,
            'lng' => $longitude
        );
    }
    
    return $array;
}

function countResultSearchCustomer($conn, $first_name, $last_name)
{
    $resultCount = 0;
    
    $sql = "SELECT * FROM tbl_customer_board WHERE last_name = '" . $last_name . "'";
    
    if (! empty($first_name)) {
        $sql .= " AND first_name = '" . $first_name . "'";
    }
    
    $result_query = mysqli_query($conn, $sql);
    
    $resultCount = mysqli_num_rows($result_query);
    
    return $resultCount;
}

function checkArrCustomerInputSearch($conn, $data, $offset)
{
    $last_name = "";
    $first_name = "";
    
    if ($offset > 0) {
        // get first_name
        for ($i = count($data) - $offset; $i < count($data); $i ++) {
            if ($i == (count($data) - 1)) {
                $first_name .= $data[$i];
            } else {
                $first_name .= $data[$i] . " ";
            }
        }
    }
    
    for ($i = 0; $i < count($data) - $offset; $i ++) {
        if ($i == (count($data) - $offset - 1)) {
            $last_name .= $data[$i];
        } else {
            $last_name .= $data[$i] . " ";
        }
    }
    
    $checkDataSearchLastName = countResultSearchCustomer($conn, $first_name, $last_name);
    if ($checkDataSearchLastName > 0) {
        $resutlSql = " AND last_name = '" . $last_name . "'";
        
        if (! empty($first_name)) {
            $resutlSql .= " AND first_name = '" . $first_name . "'";
        }
        return $resutlSql;
    } else {
        return "";
    }
}

function pushNotification($title, $message, $action, $to, $type_send = 'topic', $server_key = 'AAAAR0TTTdw:APA91bGSCuXgt9LxZcETqJmUS4kB2i5V5cZCq-OspochhOpVmEf3VB46ZMmT8urCLPNGuH0rzdYJntoezw0qvRg_BSoUrIV5Gubx-r31iGCKGqsAJquYzxg1cdsU5TuUHraKl-hrDI6r')
{
    $message_data = array(
        'title' => $title,
        'body' => $message,
        "click_action" => $action,
        "badge" => "1"
    );
    $headers = array(
        'Authorization: key=' . $server_key,
        'Content-Type: application/json'
    );
    
    $data = array();
    
    if (! empty($type_send) && $type_send == 'single') {
        require_once 'notification.php';
        $notification = new Notification();
        
        $notification->setTitle($title);
        $notification->setMessage($message);
        $notification->setAction($action);
        
        $requestData = $notification->getNotificatin();
        
        $data['to'] = $to;
        $data['data'] = $requestData;
    } else {
        $data['to'] = "/topics/" . $to;
        $data['notification'] = $message_data;
    }
    
    $data = json_encode($data);
    
    // print_r($data);
    // exit
    
    $url = 'https://fcm.googleapis.com/fcm/send';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($ch);
    curl_close($ch);
}

?>