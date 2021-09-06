<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$txtsearch =  mysqli_real_escape_string($db->conn,$_POST['query']);
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($txtsearch)) {
    $txtSQL = "SELECT con_product,con_ratio,pro_name,size_name,con_untf,con_unts,ut1.unit_name as unt_main ,ut2.unit_name as unt_sub FROM convent 
    INNER JOIN product ON (con_product=pro_id)
    INNER JOIN product_size ON (pro_size=size_id)
    INNER JOIN product_unit as ut1 ON (con_untf=ut1.unit_id)
    INNER JOIN product_unit as ut2 ON (con_unts=ut2.unit_id) WHERE pro_name LIKE '%$txtsearch%' AND con_status = 1";
    $query = $db->query($txtSQL);
}
?>
<?php foreach ($query as $data) { ?>
    <tr>
        <th style="text-align:center"><?php echo $data['pro_name'] . ' ' . $data['size_name'] ?></th>
        <th style="text-align:center"><?php echo $data['unt_main'] ?></th>
        <th style="text-align:center"><?php echo $data['con_ratio'] . ' ' . $data['unt_sub'] ?></th>
        <th style="text-align:center">
            <a href="#edit<?php echo $data['con_product']; ?>" class="btn btn-secondary" data-toggle="modal">แก้ไข</a>
            <a href="?con_product=<?php echo $data['con_product']; ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
        </th>
    </tr>
<?php } ?>