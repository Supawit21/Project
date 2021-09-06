<?php
require('config/connect.php');
$db = new DB();
$id_province =  $_POST['amp'];
/// substr ///
$id1 = substr($id_province, 0, 10);
$id2 = substr($id_province, 10, 10);
$id3 = substr($id_province, 20, 20);
/// array ///
$arr_amphur = array();
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($id_province)) {
    $txtSQL = "SELECT p1.AMPHUR_NAME,IFNULL(p2.cnt_cus,0) as count_cus,IFNULL(p2.sum_tol,0) as sum_total FROM amphur as p1
    INNER JOIN district  ON (p1.AMPHUR_ID=district.ID_AMPHUR)
    INNER JOIN zipcode   ON (district.DISTRICT_ID=zipcode.ID_DISTRICT) 
    LEFT JOIN(
    SELECT COUNT(cus_id) as cnt_cus,AMPHUR_ID,SUM(quo_tol) as sum_tol FROM quotation
    INNER JOIN customer ON (quo_cus=cus_id)
    INNER JOIN zipcode  ON (id_dis=ID_DISTRICT)
    INNER JOIN district ON (zipcode.ID_DISTRICT=district.DISTRICT_ID)
    INNER JOIN amphur   ON (district.ID_AMPHUR=amphur.AMPHUR_ID)
    WHERE quo_status = 1 AND quo_end BETWEEN '$id1' AND '$id2'
    GROUP BY cus_id
    ) as p2 ON p1.AMPHUR_ID = p2.AMPHUR_ID
    WHERE ID_PROVINCE = '$id3'
    GROUP BY p1.AMPHUR_ID
    ORDER BY sum_total DESC";
    $result_re3 = $db->query($txtSQL);
    while($row = mysqli_fetch_assoc($result_re3)){
        $AMPHUR_NAME  = $row['AMPHUR_NAME'];
        $CNT_CUSTOMER = $row['count_cus'];
        $SUM_TOTAL    = $row['sum_total'];
        /// เก็บตัวแปรไว้ใน array ///
        $arr_amphur[] = array(
            "AMPHUR_NAME"   => $AMPHUR_NAME,
            "CNT_CUSTOMER"  => $CNT_CUSTOMER,
            "SUM_TOTAL"     => $SUM_TOTAL
        );
    }
    /// แปลงค่า array อยู่ในรูป json ///
    echo json_encode($arr_amphur);
}
?>