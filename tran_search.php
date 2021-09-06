<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$txtsearch = mysqli_real_escape_string($db->conn,$_POST['query']);
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($txtsearch)) {
    $txtSQL = "SELECT tran_id,tran_name FROM transport WHERE tran_name LIKE '%$txtsearch%' AND status_tran = 1";
    $query = $db->query($txtSQL);
}
?>
<?php foreach ($query as $data) { ?>
    <tr>
        <th style="text-align:center"><?php echo $data['tran_id'] ?></th>
        <th style="text-align:center"><?php echo $data['tran_name'] ?></th>
        <th style="text-align:center">
            <a href="#edit<?php echo $data['tran_id']; ?>" class="btn btn-secondary" data-toggle="modal">แก้ไข</a>
            <a href="?tran_id=<?php echo $data['tran_id']; ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
        </th>
    </tr>
<?php } ?>