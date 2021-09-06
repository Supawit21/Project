<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$id_pr =  $_POST['form'];
$id1 = substr($id_pr,0,8);
$id2 = substr($id_pr,8,8);
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($id_pr) && $id2==1) {
    $txtSQL = "SELECT pro_name,size_name,de1.del_cnt,de1.del_re,unit_name FROM product
    INNER JOIN product_size   ON (pro_size=size_id)
    INNER JOIN quotation_cost ON (pro_id=quoc_pro)
    INNER JOIN delivery_list as de1 ON (quoc_order=de1.del_qor)
    INNER JOIN delivery_list ON (quoc_id=de1.del_qoi)
    INNER JOIN convent ON (pro_id=con_product)
    INNER JOIN product_unit ON (con_unts=unit_id)
    WHERE de1.del_id = '$id1'
    GROUP BY pro_id";
    $query = $db->query($txtSQL);
}else{
    $txtSQL = "SELECT pro_name,size_name,de1.del_cnt,de1.del_re,unit_name FROM product
    INNER JOIN product_size   ON (pro_size=size_id)
    INNER JOIN quotation_cost ON (pro_id=quoc_pro)
    INNER JOIN delivery_list as de1 ON (quoc_order=de1.del_qor)
    INNER JOIN delivery_list ON (quoc_id=de1.del_qoi)
    INNER JOIN convent ON (pro_id=con_product)
    INNER JOIN product_unit ON (con_unts=unit_id)
    WHERE de1.del_id = '$id1'
    GROUP BY pro_id";
    $query1 = $db->query($txtSQL);
}
?>
<?php if($query) {
    foreach ($query as $data) {
        ?>
            <tr>
                <td><?= $data['pro_name'].' '.$data['size_name']?></td>
                <td><?= $data['del_cnt'].' '.$data['unit_name']?></td>
            </tr>
        <?php } ?>
<?php } ?>
<?php if($query1) {
foreach ($query1 as $data) {
?>
            <tr>
                <td><?= $data['pro_name'].' '.$data['size_name']?></td>
                <td><?= $data['del_cnt'].' '.$data['unit_name']?></td>
                <td class="text-danger"><?= $data['del_re'].' '.$data['unit_name']?></td>
            </tr>
<?php  } ?>
<?php } ?>