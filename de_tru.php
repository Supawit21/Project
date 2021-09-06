<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$head =  $_POST['txt'];
/// *** ///
$arr_tru = array();
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($head)) {
    $txtSQL = "SELECT tru_id,tru_reg,tru_type FROM trucks WHERE empty_truck = 1 AND status_truck = 1 AND tru_id = '$head'";
    $result_tru = $db->query($txtSQL);
    while($row = mysqli_fetch_assoc($result_tru)){
        $tru_id   = $row['tru_id'];
        $tru_reg  = $row['tru_reg'];
        $tru_type = $row['tru_type'];
        /// เก็บตัวแปรไว้ใน array ///
        $arr_tru[] = array(
            "tru_id"   => $tru_id,
            "tru_reg"  => $tru_reg,
            "tru_type" => $tru_type
        );
    }
}
/// แปลงค่า array อยู่ในรูป json ///
echo json_encode($arr_tru);
?>

