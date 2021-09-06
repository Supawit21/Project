<?php
require('config/connect.php');
$db = new DB();
$days =  $_POST['days'];
/// start date ///
$d1 = substr($days, 0, 10);
$d2 = substr($days, 10, 10);
/// array ///
$arr_profit = array();
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($days)) {
    $txtSQL = "SELECT pro_id,type_name,pro_name,size_name,lot_cost,sol_cost FROM product
    INNER JOIN product_size ON (pro_size=size_id)
    INNER JOIN product_type ON (size_type=type_id)
    INNER JOIN lot          ON (pro_id=lot_pro)
    INNER JOIN quotation_cost ON (pro_id=quoc_pro)
    INNER JOIN sales_order_list ON (quoc_pro=pro_quo)
    INNER JOIN sales_order ON (sol_id=so_id)
    WHERE so_date BETWEEN '$d1' AND '$d2'
    GROUP BY pro_id
    ORDER BY sol_cost DESC";
    $result_re4 = $db->query($txtSQL);
    while($row = mysqli_fetch_assoc($result_re4)){
        $type_name = $row['type_name'];
        $pro_name  = $row['pro_name'];
        $size_name = $row['size_name'];
        $lot_cost  = $row['lot_cost'];
        $sol_cost  = $row['sol_cost'];
        /// เก็บตัวแปรไว้ใน array ///
        $arr_profit[] = array(
            "type_name" => $type_name,
            "pro_name"  => $pro_name,
            "size_name" => $size_name,
            "lot_cost"  => $lot_cost,
            "sol_cost"  => $sol_cost
        );
    }
    /// แปลงค่า array อยู่ในรูป json ///
    echo json_encode($arr_profit);
}
?>