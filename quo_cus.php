<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$head =  $_POST['txt'];
/// *** ///
$arr_cus = array();
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($head)) {
    $txtSQL = "SELECT cus_id,cus_name FROM customer WHERE cus_id = '$head'";
    $result_cus = $db->query($txtSQL);
    while($row = mysqli_fetch_assoc($result_cus)){
        $cus_id   = $row['cus_id'];
        $cus_name = $row['cus_name'];
        /// เก็บตัวแปรไว้ใน array ///
        $arr_cus[] = array(
            "cus_id"   => $cus_id,
            "cus_name" => $cus_name
        );
    }
}
/// แปลงค่า array อยู่ในรูป json ///
echo json_encode($arr_cus);
?>
