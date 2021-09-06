<?php
require('config/connect.php');
$db = new DB();
///
$cp =  $_POST['cost'];
$c_name = substr($cp,0,7);
$c_num  = substr($cp,7,8);
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($cp)) {
    $txtSQL = "SELECT cpl_price FROM proposed_cpl WHERE cpl_com = '$c_name' AND cpl_pro = '$c_num'";
    $result_cost = $db->query($txtSQL);
    $row = mysqli_fetch_array($result_cost);
    echo $row['cpl_price'];
    exit();
}
?>
