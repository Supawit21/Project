<?php
require('config/connect.php');
$db = new DB();
$head =  $_POST['txt'];
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($head)) {
    $txtSQL = "SELECT del_id FROM delivery_list 
    RIGHT JOIN quotation_cost as qc1 ON (del_qor=qc1.quoc_order)
    RIGHT JOIN quotation_cost ON (del_qoi=qc1.quoc_id)
    RIGHT JOIN sales_order_list as s1 ON (qc1.quoc_order=s1.order_quo)
    RIGHT JOIN sales_order_list ON (qc1.quoc_id=s1.id_quo)
    WHERE s1.sol_id = '$head'
    GROUP BY del_id";
    $result_po = $db->query($txtSQL);
}
?>
<?php
foreach ($result_po as $data) {
?>
    <input type="hidden" name="dl[]" class="form-control" value="<?=$data['del_id']?>" readonly>
<?php } ?>