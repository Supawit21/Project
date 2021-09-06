<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$head =  $_POST['txt'];
/// *** ///
$arr_tra = array();
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($head)) {
    $txtSQL = "SELECT tran_id,tran_name FROM transport WHERE status_tran = 1 AND tran_id = '$head'";
    $result_tra = $db->query($txtSQL);
    while($row = mysqli_fetch_assoc($result_tra)){
        $tran_id   = $row['tran_id'];
        $tran_name = $row['tran_name'];
        /// เก็บตัวแปรไว้ใน array ///
        $arr_tra[] = array(
            "tran_id"   => $tran_id,
            "tran_name" => $tran_name
        );
    }
}
/// แปลงค่า array อยู่ในรูป json ///
echo json_encode($arr_tra);
?>

