<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$id_pdq =  $_POST['rs'];
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($id_pdq)) {
    $txtSQL = "SELECT pro_id,pro_name,size_name,com_name,pdl_app,unit_name,p1.cpl_price,pdq_total FROM pd_quotation
    INNER JOIN pdl_quotation ON (pdq_id=pdl_id)
    INNER JOIN product_unit  ON (pdl_unit=unit_id)
    INNER JOIN proposed_cpl as p1 ON (pdl_id=p1.cpl_id)
    INNER JOIN proposed_cpl ON (pdl_order=p1.cpl_order)
    INNER JOIN cost_price as c1 ON (p1.cpl_pro=c1.cp_pro)
    INNER JOIN cost_price ON (p1.cpl_com=c1.cp_com)
    INNER JOIN product ON (c1.cp_pro=pro_id)
    INNER JOIN product_size ON (pro_size=size_id)
    INNER JOIN company ON (c1.cp_com=com_id)
    WHERE p1.cpl_status = 1 AND pdq_id = '$id_pdq'
    GROUP BY pro_id";
    $query = $db->query($txtSQL);
}
?>
<?php
foreach ($query as $data) {
?>
    <tr>
        <td><?= $data['pro_name'] . ' ' . $data['size_name'] ?></td>
        <td><?= $data['com_name'] ?></strong></td>
        <td>
            <?= $data['pdl_app'] . ' ' . $data['unit_name'] ?>
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
        <td><?= number_format($data['cpl_price']) . ' ' . 'บาท' ?></td>
        <td>
            <?php
                if ($data['unit_name'] == 'มัด' || $data['unit_name'] == 'ลัง'){
                    $txt = number_format($con['con_ratio']*$data['cpl_price']).' '.'บาท';
                }else{
                    $txt = number_format($data['pdl_app']*$data['cpl_price']).' '.'บาท';
                }
            ?>
            <?=$txt?>
        </td>
    </tr>
<?php } ?>
    <tr>
        <td colspan="4">ราคารวมสินค้าทั้งหมด</td>
        <td><?=number_format($data['pdq_total']) . ' ' . 'บาท' ?></td>
    </tr>