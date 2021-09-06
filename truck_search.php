<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$txtsearch = mysqli_real_escape_string($db->conn,$_POST['query']);
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($txtsearch)) {
    $txtSQL = "SELECT tru_id,tru_reg,tru_type FROM trucks WHERE tru_reg LIKE '%$txtsearch%'  OR tru_type LIKE '%$txtsearch%' AND status_truck = 1";
    $query = $db->query($txtSQL);
}
?>
<?php foreach ($query as $data) { ?>
    <tr>
        <th style="text-align:center"><?php echo $data['tru_id'] ?></th>
        <th style="text-align:center"><?php echo $data['tru_reg'] ?></th>
        <th style="text-align:center"><?php echo $data['tru_type'] ?></th>
        <th style="text-align:center">
            <a href="#edit<?php echo $data['tru_id'] ?>" class="btn btn-secondary" data-toggle="modal">แก้ไข</a>
            <a href="?tru_id=<?php echo $data['tru_id'] ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
        </th>
    </tr>
<?php } ?>