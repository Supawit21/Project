<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$txtsearch = mysqli_real_escape_string($db->conn, $_POST['query']);
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($txtsearch)) {
    $txtSQL = "SELECT cp_price,cp_com,cp_pro,com_name,pro_name,size_name FROM cost_price 
    INNER JOIN company ON (cost_price.cp_com=company.com_id) 
    INNER JOIN product ON (cost_price.cp_pro=product.pro_id) 
    INNER JOIN product_size ON (pro_size=size_id) WHERE com_name LIKE '%$txtsearch%' OR pro_name LIKE '%$txtsearch%' GROUP BY cp_com";
    $result_search = $db->query($txtSQL);
}
?>
<?php foreach ($result_search as $data) { ?>
    <tr>
        <th style="text-align:center"><?php echo $data['com_name'] ?></th>
        <th style="text-align:center"><?php echo $data['pro_name'].' '.$data['size_name']?></th>
        <th style="text-align:center"><?php echo $data['cp_price'] ?></th>
        <th style="text-align:center">
            <a href="cost_edit.php?id=<?php echo $data['cp_com']; ?>" class="btn btn-secondary">แก้ไข</a>
            <a href="?cp_com=<?php echo $data['cp_com']; ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
        </th>
    </tr>
<?php } ?>