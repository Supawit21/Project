<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$txtsearch = mysqli_real_escape_string($db->conn, $_POST['query']);
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($txtsearch)) {
    $txtSQL = "SELECT unit_id,unit_name FROM product_unit WHERE unit_name LIKE '%$txtsearch%' AND status_unit = 1";
    $result_search = $db->query($txtSQL);
}
?>
<?php foreach ($result_search as $data) { ?>
    <tr>
        <th style="text-align:center"><?php echo $data['unit_id'] ?></th>
        <th style="text-align:center"><?php echo $data['unit_name'] ?></th>
        <th style="text-align:center">
            <a href="#edit<?php echo $data['unit_id']; ?>" class="btn btn-secondary" data-toggle="modal">แก้ไข</a>
            <a href="?unit_id=<?php echo $data['unit_id']; ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
        </th>
    </tr>
<?php } ?>