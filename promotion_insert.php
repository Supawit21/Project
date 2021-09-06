<?php
require('config/connect.php');
$db = new DB();
/// สร้างรหัสรันเอง ///
$txtSQL = "SELECT concat('PROMO',NVL(lpad(substr(max(promo_id),6,3)+1,3,'0'),'001')) as promo FROM promotion";
$result = $db->query($txtSQL);
$fetch_id = mysqli_fetch_array($result);
/// บันทึกข้อมูลโปรโมชั่น ///
if (isset($_POST['promo_insert'])){
    $promo_id = $fetch_id['promo'];
    $promo_name = $_POST['promo_name'];
    $promo_start = $_POST['promo_start'];
    $promo_end = $_POST['promo_end'];
    $count = $_POST['count'];
    $lpromo_pro = $_POST['lpromo_pro'];
    $arr = array(
        "promo_id" => $promo_id,
        "promo_name" => $promo_name,
        "promo_start" => $promo_start,
        "promo_end" => $promo_end,
        "count" => $count
    );
    $insert = $db->insert("promotion",$arr);
    foreach($lpromo_pro as $key => $data){
        $order = $key + 1;
        $arr1 = array(
            "lpromo_order" => $order,
            "list_promo" => $data,
            "list_id" => $promo_id
        );
    $insert2 = $db->insert("list_promotion",$arr1);
    if ($insert2) {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มข้อมูลสำเร็จ');";
        echo "window.location = 'promotion.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มข้อมูลไม่สำเร็จ');";
        echo "</script>";
    }
    }
}
?>
