<?php
require('config/connect.php');
$db = new DB();
/// สร้างรหัสรันเอง ///
$txtSQL = "SELECT concat('PRO',NVL(lpad(substr(max(pro_id),4,5)+1,5,'0'),'00001')) as pro FROM product";
$result = $db->query($txtSQL);
$fetch_id = mysqli_fetch_array($result);
/// บันทึกข้อมูลสินค้า ///
if (isset($_POST['pro_insert'])) {
    $pro_id      = $fetch_id['pro'];
    $pro_name    = $_POST['pro_name'];
    $pro_size    = $_POST['pro_size'];
    $pro_amount  = $_POST['pro_amount'];
    $pro_price   = $_POST['pro_price'];
    $con_unts    = $_POST['pro_unit'];
    // $pro_weight   = $_POST['pro_weight'];
    /// เก็บชื่อไฟล์ ///
    $imgFile = $_FILES['pro_img']['name'];
    $tmp = $_FILES['pro_img']['tmp_name'];
    /// เก็บ path ไฟล์ ///
    $folder = "image_pro/";
    /// บันทึกรูปเข้าไฟล์ ///
    move_uploaded_file($tmp,$folder.$imgFile);
    $arr = array(
        "pro_id" => $pro_id,
        "pro_img" => $imgFile,
        "pro_name" => $pro_name,
        "pro_size" => $pro_size,
        "pro_amount" => $pro_amount,
        // "pro_weight" => $pro_weight,
        "pro_price" => $pro_price
    );
    $insert = $db->insert("product",$arr);
    $arr1 = array(
        "con_product" => $pro_id,
        "con_unts"    => $con_unts
    );
    $insert_con = $db->insert("convent",$arr1);
    if ($insert_con) {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มข้อมูลสำเร็จ');";
        echo "window.location = 'product.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มข้อมูลไม่สำเร็จ');";
        echo "</script>";
    }
}
?>
