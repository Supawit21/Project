<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$txt_search =  $_POST['product'];
/// *** ///
$arr_quo = array();
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($txt_search)) {
    $txtSQL = "SELECT pro_id,pro_name,size_name,unit_name,pro_price,count,status_promo FROM product
    INNER JOIN product_size   ON (pro_size=size_id)
    INNER JOIN convent        ON (pro_id=con_product)
    INNER JOIN product_unit   ON (con_unts=unit_id)
    LEFT JOIN list_promotion  ON (pro_id=list_promo)
    LEFT JOIN promotion       ON (list_id=promo_id)
    WHERE pro_id='$txt_search'";
    $query = $db->query($txtSQL);
    while($row = mysqli_fetch_assoc($query)){
        $pro_id       = $row['pro_id'];
        $pro_name     = $row['pro_name'];
        $size_name    = $row['size_name'];
        $unit_name    = $row['unit_name'];
        $pro_price    = $row['pro_price'];
        $count        = $row['count'];
        $status_promo = $row['status_promo'];
        /// เก็บตัวแปรไว้ใน array ///
        $arr_quo[] = array(
            "pro_id"       => $pro_id,
            "pro_name"     => $pro_name,
            "size_name"    => $size_name,
            "unit_name"    => $unit_name,
            "pro_price"    => $pro_price,
            "count"        => $count,
            "status_promo" => $status_promo
        );
    }
    /// แปลงค่า array อยู่ในรูป json ///
    echo json_encode($arr_quo);
}
?>
