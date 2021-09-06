<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$txtsearch = mysqli_real_escape_string($db->conn, $_POST['query']);
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($txtsearch)) {
    $txtSQL = "SELECT pos_id,pos_name FROM position WHERE pos_name LIKE '%$txtsearch%' AND pos_status = 1";
    $query = $db->query($txtSQL);
}
?>
<?php foreach ($query as $data) { ?>
    <tr>
        <th style="text-align:center"><?php echo $data['pos_id'] ?></th>
        <th style="text-align:center"><?php echo $data['pos_name'] ?></th>
        <th style="text-align:center">
            <a href="#edit<?php echo $data['pos_id']; ?>" class="btn btn-secondary" data-toggle="modal">แก้ไข</a>
            <a href="?pos_id=<?php echo $data['pos_id']; ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
        </th>
<?php } ?>