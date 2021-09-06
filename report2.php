<?php
require('config/connect.php');
$db = new DB();
$days =  $_POST['days'];
/// start date ///
$d1 = substr($days, 0, 10);
$d2 = substr($days, 10, 10);
$d3 = substr($days, 20, 20);
/// array ///
$arr_re = array();
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($days) && $d3=="1") {
    $txtSQL = "SELECT pro_id,pro_name,size_name,cnt1.cnt,unit_name FROM product
    INNER JOIN product_size ON (pro_size=size_id)
    INNER JOIN convent      ON (pro_id=con_product)
    INNER JOIN product_unit ON (con_unts=unit_id)
    INNER JOIN quotation_cost ON (pro_id=quoc_pro)
    LEFT JOIN (SELECT SUM(IFNULL(sol_amt,0)) as cnt,pro_quo FROM sales_order_list
    INNER JOIN sales_order ON (sol_id=so_id)
    WHERE so_date BETWEEN '$d1' AND '$d2'
    GROUP BY pro_quo) as cnt1 ON pro_id=pro_quo
    GROUP BY cnt DESC";
    $result_re2 = $db->query($txtSQL);
    while($row = mysqli_fetch_assoc($result_re2)){
        $pro_name  = $row['pro_name'];
        $cnt       = $row['cnt'];
        $size_name = $row['size_name'];
        $unit_name = $row['unit_name'];
        /// เก็บตัวแปรไว้ใน array ///
        $arr_re[] = array(
            "pro_name"   => $pro_name,
            "cnt"        => $cnt,
            "size_name"  => $size_name,
            "unit_name"  => $unit_name
        );
    }
    /// แปลงค่า array อยู่ในรูป json ///
    echo json_encode($arr_re);
}else if(!empty($days) && $d3=="2"){
    $txtSQL = "SELECT pro_id,pro_name,size_name,cnt1.cnt,unit_name FROM product
    INNER JOIN product_size ON (pro_size=size_id)
    INNER JOIN convent      ON (pro_id=con_product)
    INNER JOIN product_unit ON (con_unts=unit_id)
    INNER JOIN quotation_cost ON (pro_id=quoc_pro)
    LEFT JOIN (SELECT SUM(IFNULL(sol_amt,0)) as cnt,pro_quo FROM sales_order_list
    INNER JOIN sales_order ON (sol_id=so_id)
    WHERE so_date BETWEEN '$d1' AND '$d2'
    GROUP BY pro_quo) as cnt1 ON pro_id=pro_quo
    GROUP BY cnt ASC";
    $result_re2 = $db->query($txtSQL);
    while($row = mysqli_fetch_assoc($result_re2)){
        $pro_name  = $row['pro_name'];
        $size_name = $row['size_name'];
        $cnt       = $row['cnt'];
        $unit_name = $row['unit_name'];
        /// เก็บตัวแปรไว้ใน array ///
        $arr_re[] = array(
            "pro_name"   => $pro_name,
            "size_name"  => $size_name,
            "cnt"        => $cnt,
            "unit_name"  => $unit_name
        );
    }
    /// แปลงค่า array อยู่ในรูป json ///
    echo json_encode($arr_re);
}
?>
