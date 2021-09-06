<?php
require('config/connect.php');
$db = new DB();
///
$head =  $_POST['data_pro'];
$pdq  =  substr($head, 0, 8);
$cid  =  substr($head, 8, 7);
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($head)) {
    $txtSQL = "SELECT pro_id,pro_name,size_name,pd1.pdl_app,unit_name,cpl_price,cpl_id,cpl_order,cpl_com FROM proposed_cpl
    INNER JOIN pdl_quotation as pd1 ON (cpl_id=pd1.pdl_id)
    INNER JOIN pdl_quotation ON (cpl_order=pd1.pdl_order)
    INNER JOIN cost_price ON (cpl_pro=cp_pro)
    INNER JOIN product ON (cp_pro=pro_id)
    INNER JOIN product_size ON (pro_size=size_id)
    INNER JOIN product_unit ON (pd1.pdl_unit=unit_id)
    WHERE cpl_id = '$pdq' AND cpl_com = '$cid' AND cpl_status = 1
    GROUP BY pro_id,pro_name,size_name,pd1.pdl_app,unit_name,cpl_price,cpl_id,cpl_order,cpl_com";
    $result_list = $db->query($txtSQL);
}
?>
<?php
$num = 1;
$x = 0;
foreach ($result_list as $data) {
?>
    <tr>
        <td><?= $num ?></td>
        <td>
            <?= $data['pro_name'] . ' ' . $data['size_name'] ?>
            <input type="hidden" name="pol_pro[]" class="form-control" value="<?= $data['pro_id'] ?>">
            <input type="hidden" name="pol_pdq[]" value="<?=$data['cpl_id']?>">
        </td>
        <td>
            <?= $data['pdl_app'] . ' ' . $data['unit_name'] ?>
            <input type="hidden" name="pol_amount[]" class="form-control" value="<?= $data['pdl_app'] ?>">
            <input type="hidden" name="pol_por[]" value="<?=$data['cpl_order']?>">
            <?php
            if ($data['unit_name'] == 'มัด' || $data['unit_name'] == 'ลัง') {
            $id_con = $data['pro_id'];
            $txtSQL = "SELECT con_ratio FROM convent
            WHERE con_product = '$id_con'";
            $result_con = $db->query($txtSQL);
            } else {
            $id_con = "";
            $txtSQL = "SELECT con_ratio FROM convent
            WHERE con_product = '$id_con'";
            $result_con = $db->query($txtSQL);
            }
            foreach ($result_con as $con) {
            ?>
                <label hidden><?=$con['con_ratio']?></label>
        <?php } ?>
        </td>
        <td>
            <?= number_format($data['cpl_price']) . ' ' . 'บาท' ?>
            <input type="hidden" name="pol_com[]" value="<?=$data['cpl_com']?>">
        </td>
        <td>
        <?php
                if ($data['unit_name'] == 'มัด' || $data['unit_name'] == 'ลัง'){
                    $txt = $con['con_ratio']*$data['cpl_price'];
                }else{
                    $txt = $data['pdl_app']*$data['cpl_price'];
                }
            ?>
            <?=number_format($txt).' '.'บาท'?>
            <input type="hidden" name="pol_all[]" class="form-control" value="<?= $txt ?>">
        </td>
    </tr>
    <?=$x = $x + $txt?>
<?php $num++;
} ?>
    <tr>
        <td colspan="4" style="text-align: right;"><strong>รวมราคาสินค้า</strong></td>
        <td>
            <?=number_format($x).' '.'บาท';?>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="text-align: right;"><strong>ภาษีมูลเพิ่ม (7%)</strong></td>
        <td>
            <?=number_format($x*0.07).' '.'บาท';?>
        </td>
    </tr>
    <?= $all_sum = $x + ($x*0.07);?>
    <tr>
        <td colspan="4" style="text-align: right;"><strong>รวมเงินทั้งหมด</strong></td>
        <td>
            <?=number_format($all_sum).' '.'บาท';?>
            <input type="hidden" name="po_total" value="<?=$all_sum?>">
        </td>
    </tr>