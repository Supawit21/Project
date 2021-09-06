<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$txt_search =  $_POST['order'];
$pro_id  =  substr($txt_search, 0, 8);
$lim     =  substr($txt_search, 8, 7);
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (empty($lim)) {
    $txtSQL = "SELECT com_id,com_name,pro_id,pro_name,size_name,cp_price,con_untf,ut1.unit_name as unt_main,con_unts,ut2.unit_name as unt_sec FROM cost_price 
    INNER JOIN product ON (cp_pro=pro_id)
    INNER JOIN convent ON (pro_id=con_product)
    INNER JOIN product_unit as ut1 ON (con_untf=ut1.unit_id)
    INNER JOIN product_unit as ut2 ON (con_unts=ut2.unit_id)
    INNER JOIN product_size ON (pro_size=size_id)
    INNER JOIN company ON (cp_com=com_id)
    WHERE cp_pro = '$pro_id'";
    $query = $db->query($txtSQL);
} else {
    $txtSQL = "SELECT com_id,com_name,pro_id,pro_name,size_name,cp_price,con_untf,ut1.unit_name as unt_main,con_unts,ut2.unit_name as unt_sec FROM cost_price 
    INNER JOIN product ON (cp_pro=pro_id)
    INNER JOIN convent ON (pro_id=con_product)
    INNER JOIN product_unit as ut1 ON (con_untf=ut1.unit_id)
    INNER JOIN product_unit as ut2 ON (con_unts=ut2.unit_id)
    INNER JOIN product_size ON (pro_size=size_id)
    INNER JOIN company ON (cp_com=com_id)
    WHERE cp_pro = '$pro_id' LIMIT $lim";
    $query = $db->query($txtSQL);
}
?>
<?php
$i = 1;
foreach ($query as $data) {
?>
    <tr>
        <td>
            <?= $data['com_name'] ?>
            <input type="hidden" name="cpl_com[]" class="form-control" value="<?= $data['com_id'] ?>">
        </td>
        <td>
            <?= $data['pro_name'] . ' ' . $data['size_name'] ?>
            <input type="hidden" name="cpl_pro[]" class="form-control" value="<?= $data['pro_id'] ?>">
        </td>
        <td>
            <?= number_format($data['cp_price']) . ' ' . 'บาท' ?>
            <input type="hidden" name="cpl_price[]" class="form-control" value="<?= $data['cp_price'] ?>" readonly>
        </td>
    </tr>
<?php 
} ?>
<tr>
    <td colspan="2" style="text-align: right;">จำนวนสินค้าที่เสนอ</td>
    <td>
        <div class="form-row">
            <input type="text" name="pdl_count[]" placeholder="จำนวน" class="form-control col-md-6 num" autocomplete="off">
            <select name="pdl_unit[]" id="pdl_unit" class="form-control col-md-6 custom-select">
                <option selected disabled>กรุณาเลือกหน่วย</option>
                <option value="<?= $data['con_untf'] ?>"><?= $data['unt_main'] ?></option>
                <option value="<?= $data['con_unts'] ?>"><?= $data['unt_sec'] ?></option>
            </select>
        </div>
    </td>
</tr>
    <script src="js/valid.js"></script>