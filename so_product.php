<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$txt_so =  $_POST['id'];
/// *** ///
$arr_so = array();
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($txt_so)) {
    $txtSQL = "SELECT pro_img,quoc_order,quoc_id,pro_id,pro_name,size_name,ql.quol_cnt,ql.quol_cnte,unit_name,quoc_cost,quoc_coste,ql.quol_sum,count,status_promo FROM product
    INNER JOIN quotation_cost ON (pro_id=quoc_pro)
    INNER JOIN quotation_list as ql ON (quoc_id=ql.quol_id)
    INNER JOIN quotation_list ON (quoc_order=ql.quol_order)
    INNER JOIN product_size ON (pro_size=size_id)
    INNER JOIN convent      ON (pro_id=con_product)
    INNER JOIN product_unit ON (con_unts=unit_id)
    LEFT JOIN  list_promotion ON (pro_id=list_promo)
    LEFT JOIN  promotion ON (list_id=promo_id)
    WHERE quoc_id = '$txt_so'
    GROUP BY pro_id";
    $query = $db->query($txtSQL);
    while($row = mysqli_fetch_assoc($query)){
        $pro_img      = $row['pro_img'];
        $quoc_order   = $row['quoc_order'];
        $quoc_id      = $row['quoc_id'];
        $pro_id       = $row['pro_id'];
        $pro_name     = $row['pro_name'];
        $size_name    = $row['size_name'];
        $quol_cnt     = $row['quol_cnt'];
        $quol_cnte    = $row['quol_cnte'];
        $unit_name    = $row['unit_name'];
        $quoc_cost    = $row['quoc_cost'];
        $quoc_coste   = $row['quoc_coste'];
        $quol_sum     = $row['quol_sum'];
        $count        = $row['count'];
        $status_promo = $row['status_promo'];
        /// เก็บตัวแปรไว้ใน array ///
        $arr_so[] = array(
            "pro_img"      => $pro_img,
            "quoc_order"   => $quoc_order,
            "quoc_id"      => $quoc_id,
            "pro_id"       => $pro_id,
            "pro_name"     => $pro_name,
            "size_name"    => $size_name,
            "quol_cnt"     => $quol_cnt,
            "quol_cnte"    => $quol_cnte,
            "unit_name"    => $unit_name,
            "quoc_cost"    => $quoc_cost,
            "quoc_coste"   => $quoc_coste,
            "quol_sum"     => $quol_sum,
            "count"        => $count,
            "status_promo" => $status_promo
        );
    }
    /// แปลงค่า array อยู่ในรูป json ///
    echo json_encode($arr_so);
}
?>
