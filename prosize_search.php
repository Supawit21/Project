<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$txtsearch = mysqli_real_escape_string($db->conn,$_POST['query']);
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($txtsearch)) {
    $txtSQL = "SELECT size_id,size_name,type_name FROM product_size INNER JOIN product_type ON (size_type=type_id) WHERE size_name LIKE '%$txtsearch%' OR type_name LIKE '%$txtsearch%' AND status_size = 1";
    $query = $db->query($txtSQL);
}
?>
<?php foreach ($query as $data) { ?>
    <tr>
        <th style="text-align:center"><?php echo $data['size_id'] ?></th>
        <th style="text-align:center"><?php echo $data['type_name'] . ' ' . $data['size_name'] ?></th>
        <th style="text-align:center">
            <a href="#edit<?php echo $data['size_id']; ?>" class="btn btn-secondary" data-toggle="modal">แก้ไข</a>
            <a href="?size_id=<?php echo $data['size_id']; ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
        </th>
    </tr>
<?php } ?>