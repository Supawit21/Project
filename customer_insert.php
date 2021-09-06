<?php
require('config/connect.php');
$db = new DB();
/// สร้างรหัสรันเอง ///
$txtSQL = "SELECT concat('CUS',NVL(lpad(substr(max(cus_id),4,4)+1,4,'0'),'0001')) as cus FROM customer";
$result = $db->query($txtSQL);
$fetch_id = mysqli_fetch_array($result);
/// บันทึกข้อมูลลูกค้า ///
if (isset($_POST['cus_insert'])) {
    $cus_id      = $fetch_id['cus'];
    $cus_title   = $_POST['cus_title'];
    $cus_name    = $_POST['cus_name'];
    $cus_surname = $_POST['cus_surname'];
    $cus_email   = $_POST['cus_email'];
    $cus_add     = $_POST['cus_add'];
    $id_dis      = $_POST['district'];
    $cus_zip     = $_POST['zipcode'];
    $cus_tel     = $_POST['cus_tel'];
    $arr = array(
         "cus_id" => $cus_id,
         "cus_title" => $cus_title,
         "cus_name" => $cus_name,
         "cus_surname" => $cus_surname,
         "cus_email" => $cus_email,
         "cus_add" => $cus_add,
         "id_dis" => $id_dis,
         "cus_zip" => $cus_zip
    );
    $insert = $db->insert("customer" , $arr);
    foreach ($cus_tel as  $tel) {
        $arr1 = array(
              "cus_id" => $cus_id,
              "cus_tel" => $tel
        );
        $insert_all = $db->insert("customer_tel" , $arr1);
        if ($insert_all) {
            echo "<script type='text/javascript'>";
            echo "alert('เพิ่มข้อมูลสำเร็จ');";
            echo "window.location = 'customer.php'; ";
            echo "</script>";
        } else {
            echo "<script type='text/javascript'>";
            echo "alert('เพิ่มข้อมูลไม่สำเร็จ');";
            echo "</script>";
        }
    }
}
?>
