<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$txtsearch = $_POST['query'];
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if(!empty($txtsearch)){
    $txtSQL = "SELECT company.com_id,com_name,com_contact,com_email,com_tel FROM company
    INNER JOIN company_tel ON(company.com_id = company_tel.com_id) WHERE com_name LIKE '%$txtsearch%' OR com_contact LIKE '%$txtsearch%' AND status_com = 1 GROUP BY company.com_id,com_name";
    $result = $db->query($txtSQL);
}
?>
<?php foreach ($result as $data) { ?>
    <tr>
        <th style="text-align:center"><?php echo $data['com_id'] ?></th>
        <th style="text-align:center"><?php echo $data['com_name'] ?></th>
        <th style="text-align:center"><?php echo $data['com_contact'] ?></th>
        <th style="text-align:center"><?php echo $data['com_email'] ?></th>
        <th style="text-align:center"><?php echo $data['com_tel'] ?></th>
        <th style="text-align:center">
            <a href="company_edit.php?id=<?php echo $data['com_id'] ?>" class="btn btn-secondary">แก้ไข</a>
            <a href="?com_id=<?php echo $data['com_id'] ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
        </th>
    </tr>
<?php } ?>