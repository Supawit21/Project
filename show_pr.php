<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$id_pr =  $_POST['rs'];
$id1 = substr($id_pr,0,7);
$id2 = substr($id_pr,7,8);
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($id_pr) && $id2==1) {
    $txtSQL = "SELECT lot_id,pro_name,size_name,lot_amount,unit_name,lot_cost FROM lot
    INNER JOIN product ON (lot_pro=pro_id)
    INNER JOIN product_size ON (pro_size=size_id)
    INNER JOIN convent ON (pro_id=con_product)
    INNER JOIN product_unit ON (con_unts=unit_id)
    WHERE lot_pr = '$id1'";
    $query = $db->query($txtSQL);
}else{
    $txtSQL = "SELECT lot_id,pro_name,size_name,lot_amount,unit_name,lot_cost,pr1.prl_bal FROM lot
    INNER JOIN prl_receipt as pr1 ON (lot_pr=pr1.prl_id)
    INNER JOIN prl_receipt ON (lot_order=pr1.prl_order)
    INNER JOIN product ON (lot_pro=pro_id)
    INNER JOIN product_size ON (pro_size=size_id)
    INNER JOIN convent ON (pro_id=con_product)
    INNER JOIN product_unit ON (con_unts=unit_id)
    WHERE lot_pr = '$id1' 
    GROUP BY lot_id,pro_name,size_name,lot_amount,lot_cost,pr1.prl_bal";
    $query1 = $db->query($txtSQL);
}
?>
<?php if($query) {
    $count = 1;
    foreach ($query as $data) {
        ?>
            <tr>
                <td><?= $count?></td>
                <td><?= $data['lot_id']?></td>
                <td><?= $data['pro_name'] . ' ' . $data['size_name'] ?></td>
                <td><?= $data['lot_amount'] .' '. $data['unit_name']?></td>
                <td><?= number_format($data['lot_cost']) . ' ' . 'บาท' ?></td>
            </tr>
        <?php $count++; } ?>
<?php } ?>
<?php if($query1) {
$count = 1;
foreach ($query1 as $data) {
?>
    <tr>
        <td><?= $count?></td>
        <td><?= $data['lot_id']?></td>
        <td><?= $data['pro_name'] . ' ' . $data['size_name'] ?></td>
        <td><?= $data['lot_amount'] .' '. $data['unit_name']?></td>
        <td><?= $data['prl_bal'] .' '. $data['unit_name']?></td>
        <td><?= number_format($data['lot_cost']) . ' ' . 'บาท' ?></td>
    </tr>
<?php $count++; } ?>
<?php } ?>