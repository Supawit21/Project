<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$txtsearch = $_POST['query'];
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if(!empty($txtsearch)){
$txtSQL = "SELECT customer.cus_id,cus_title,cus_name,cus_surname,cus_email,cus_add,cus_tel FROM customer 
INNER JOIN customer_tel ON(customer.cus_id = customer_tel.cus_id) WHERE cus_name LIKE '%$txtsearch%' OR cus_surname LIKE '%$txtsearch%' AND status_cus = 1 GROUP BY customer.cus_id,cus_name";
$result = $db->query($txtSQL);
}
?>
<?php foreach ($result as $data) { ?>
    <tr>
        <th style="text-align:center"><?php echo $data['cus_id'] ?></th>
        <th style="text-align:center"><?php echo $data['cus_title'].' '.$data['cus_name'].' '.$data['cus_surname']?></th>
        <th style="text-align:center"><?php echo $data['cus_email'] ?></th>
        <th style="text-align:center"><?php echo $data['cus_add'] ?></th>
        <th style="text-align:center"><?php echo $data['cus_tel'] ?></th>
        <th style="text-align:center">
            <a href="customer_edit.php?id=<?php echo $data['cus_id'] ?>" class="btn btn-secondary">แก้ไข</a>
            <a href="?cus_id=<?php echo $data['cus_id'] ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
        </th>
    </tr>
<?php } ?>