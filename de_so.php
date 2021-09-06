<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$head =  $_POST['txt'];
/// *** ///
$arr_so = array();
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($head)) {
    $txtSQL = "SELECT pro_name,size_name,sol_amt,unit_name,qc1.quoc_id,qc1.quoc_order,qc1.quoc_pro,IFNULL(cnt.count,0) as sd_cnt FROM sales_order_list
    INNER JOIN quotation_cost as qc1 ON (order_quo=qc1.quoc_order)
    INNER JOIN quotation_cost ON (id_quo=qc1.quoc_id)
    INNER JOIN product        ON (qc1.quoc_pro=pro_id)
    INNER JOIN product_size ON (pro_size=size_id)
    INNER JOIN convent ON (pro_id=con_product)
    INNER JOIN product_unit ON (con_unts=unit_id)
    LEFT JOIN (
    SELECT SUM(IFNULL(del_cnt,0)) as count,del_qoi,del_qor FROM delivery_list
    GROUP BY del_order,del_id
    ) as cnt ON (qc1.quoc_id=cnt.del_qoi AND qc1.quoc_order=cnt.del_qor)
    WHERE sol_id = '$head' AND sol_amt!=IFNULL(cnt.count,0)
    GROUP BY qc1.quoc_id,qc1.quoc_order,qc1.quoc_pro";
    $result_so = $db->query($txtSQL);
    while($row = mysqli_fetch_assoc($result_so)){
        $pro_name   = $row['pro_name'];
        $size_name  = $row['size_name'];
        $sol_amt    = $row['sol_amt'];
        $unit_name  = $row['unit_name'];
        $quoc_id    = $row['quoc_id'];
        $quoc_order = $row['quoc_order'];
        $quoc_pro   = $row['quoc_pro'];
        $sd_cnt     = $row['sd_cnt'];
        /// เก็บตัวแปรไว้ใน array ///
        $arr_so[] = array(
            "pro_name"   => $pro_name,
            "size_name"  => $size_name,
            "sol_amt"    => $sol_amt,
            "unit_name"  => $unit_name,
            "quoc_id"    => $quoc_id,
            "quoc_order" => $quoc_order,
            "quoc_pro"   => $quoc_pro,
            "sd_cnt"     => $sd_cnt
        );
    }
}
/// แปลงค่า array อยู่ในรูป json ///
echo json_encode($arr_so);
?>

