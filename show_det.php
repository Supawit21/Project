<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$id_pro =  $_POST['rs'];
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($id_pro)) {
    $txtSQL = "SELECT pro_img,pro_name,pro_amount,pro_price,size_name,unit_name from product
    INNER JOIN convent ON (pro_id=con_product)
    INNER JOIN product_unit ON (con_unts=unit_id)
    INNER JOIN product_size ON (pro_size=size_id)
    WHERE pro_id = '$id_pro'";
    $result_sw = $db->query($txtSQL);
}
?>
<?php
foreach ($result_sw as $data) {
?>
    <img src="image_pro/<?= $data['pro_img'] ?>" class="rounded mx-auto d-block" alt="" width="150px" height="150px">
    <tr>
        <td><b>ชื่อสินค้า</b></td>
        <td><?= $data['pro_name'] ?></td>
    </tr>
    <tr>
        <td><b>ขนาดสินค้า</b></td>
        <td><?= $data['size_name'] ?></td>
    </tr>
    <tr>
        <td><b>จุดสั่งซื้อ</b></td>
        <td><?= $data['pro_amount'] ?></td>
    </tr>
    <tr>
        <td><b>หน่วยสินค้า</b></td>
        <td><?= $data['unit_name'] ?></td>
    </tr>
    <tr>
        <td><b>ราคาขาย</b></td>
        <td><?= number_format($data['pro_price']) . ' ' . 'บาท' ?></td>
    </tr>
<?php } ?>
