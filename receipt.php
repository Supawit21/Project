<?php
require('config/connect.php');
$db = new DB();
$head =  $_POST['data_pr'];
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($head)) {
    $txtSQL = "SELECT prl_id FROM prl_receipt
    RIGHT JOIN pol_purchase ON (prl_po=pol_id)
    WHERE pol_id = '$head'
    GROUP BY prl_id";
    $result_po = $db->query($txtSQL);
}
?>
<?php
foreach ($result_po as $data) {
?>
    <input type="hidden" name="pr[]" class="form-control" value="<?=$data['prl_id']?>">
<?php } ?>