<?php
require('config/connect.php');
$db = new DB();
$days =  $_POST['days'];
/// start date ///
$d1 = substr($days, 0, 10);
$d2 = substr($days, 10, 10);
/// array ///
$arr_overdue = array();
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($days)) {
    $txtSQL = "SELECT so_id,sol_order,pro_name,size_name,sol_amt,unit_name,IFNULL(cnt.count,0) as sd_cnt FROM sales_order_list
    INNER JOIN sales_order    ON (sol_id=so_id)
    INNER JOIN quotation_cost as qc1 ON (order_quo=qc1.quoc_order)
    INNER JOIN quotation_cost ON (id_quo=qc1.quoc_id)
    INNER JOIN product        ON (qc1.quoc_pro=pro_id)
    INNER JOIN product_size ON (pro_size=size_id)
    INNER JOIN convent ON (pro_id=con_product)
    INNER JOIN product_unit ON (con_unts=unit_id)
    LEFT JOIN (
    SELECT SUM(IFNULL(del_cnt,0)) as count,del_qoi,del_qor,dl_date FROM delivery_list
    INNER JOIN delivery_note ON (del_id=dl_id)
    WHERE dl_date BETWEEN '$d1' AND '$d2' 
    GROUP BY del_order,del_id
    ) as cnt ON (qc1.quoc_id=cnt.del_qoi AND qc1.quoc_order=cnt.del_qor)
    WHERE sol_amt!=IFNULL(cnt.count,0)
    GROUP BY qc1.quoc_id,qc1.quoc_order,qc1.quoc_pro";
    $result_re5 = $db->query($txtSQL);
    while($row = mysqli_fetch_assoc($result_re5)){
        $so_id     = $row['so_id'];
        $sol_order = $row['sol_order'];
        $pro_name  = $row['pro_name'];
        $size_name = $row['size_name'];
        $sol_amt   = $row['sol_amt'];
        $unit_name = $row['unit_name'];
        $sd_cnt    = $row['sd_cnt'];
        /// เก็บตัวแปรไว้ใน array ///
        $arr_overdue[] = array(
            "so_id"     => $so_id,
            "sol_order" => $sol_order,
            "pro_name"  => $pro_name,
            "size_name" => $size_name,
            "sol_amt"   => $sol_amt,
            "unit_name" => $unit_name,
            "sd_cnt"    => $sd_cnt
        );
    }
    /// แปลงค่า array อยู่ในรูป json ///
    echo json_encode($arr_overdue);
}
?>