<?php
require('config/connect.php');
$db = new DB();
/// สร้างรหัสรันเอง ///
$txtSQL = "SELECT concat('COM',NVL(lpad(substr(max(com_id),4,4)+1,4,'0'),'0001')) as com FROM company";
$result = $db->query($txtSQL);
$fetch_id = mysqli_fetch_array($result);
/// บันทึกข้อมูลบริษัทคู่ค้า ///
if (isset($_POST['com_insert'])) {
    $com_id      = $fetch_id['com'];
    $com_name    = $_POST['com_name'];
    $com_contact = $_POST['com_contact'];
    $com_email   = $_POST['com_email'];
    $com_add     = $_POST['com_add'];
    $id_dis     = $_POST['district'];
    $com_zip     = $_POST['zipcode'];
    $com_tel     = $_POST['com_tel'];
    $arr = array(
         "com_id" => $com_id,
         "com_name" => $com_name,
         "com_contact" => $com_contact,
         "com_email" => $com_email,
         "com_add" => $com_add,
         "id_dis" => $id_dis,
         "com_zip" => $com_zip
    );
    $insert = $db->insert("company" , $arr);
    foreach ($com_tel as  $tel) {
        $arr1 = array(
             "com_id" => $com_id,
             "com_tel" => $tel
        );
        $insert_all = $db->insert("company_tel" , $arr1);
        if ($insert_all) {
            echo "<script type='text/javascript'>";
            echo "alert('เพิ่มข้อมูลสำเร็จ');";
            echo "window.location = 'company.php'; ";
            echo "</script>";
        } else {
            echo "<script type='text/javascript'>";
            echo "alert('เพิ่มข้อมูลไม่สำเร็จ');";
            echo "</script>";
        }
    }
}
?>
