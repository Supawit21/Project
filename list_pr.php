<?php
require('config/connect.php');
$db = new DB();
$head =  $_POST['data_po'];
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($head)) {
    $txtSQL = "SELECT pol_order,pol_id,pro_id,pro_name,size_name,pol_amount,unit_name,cpl_price FROM pol_purchase
    INNER JOIN proposed_cpl  ON (pol_pro=cpl_pro)
    INNER JOIN pdl_quotation as pd1 ON (cpl_order=pd1.pdl_order)
    INNER JOIN pdl_quotation ON (cpl_id=pd1.pdl_id)
    INNER JOIN product_unit  ON (pd1.pdl_unit=unit_id)
    INNER JOIN cost_price    ON (cpl_pro=cp_pro)
    INNER JOIN product       ON (cp_pro=pro_id)
    INNER JOIN product_size  ON (pro_size=size_id)
    WHERE pol_id = '$head' AND cpl_status = 1 AND pol_amount NOT IN (SELECT SUM(IFNULL(prl_rec,0)) FROM prl_receipt WHERE prl_po = '$head' GROUP BY prl_por) 
    GROUP BY pol_order,pol_id";
    $result_po = $db->query($txtSQL);
}
?>
<?php
foreach ($result_po as $data) {
?>
    <tr class="del">
        <td>
            <?= $data['pro_name'] . ' ' . $data['size_name'] ?>
            <input type="hidden" name="pol_order[]" class="form-control" value="<?= $data['pol_order'] ?>">
            <input type="hidden" name="pol_id[]" value="<?= $data['pol_id'] ?>">
            <input type="hidden" name="pr_pro[]" class="form-control" value="<?= $data['pro_id'] ?>">
        </td>
        <td>
            <?php
            $ord_po = $data['pol_order'];
            $id_po  = $data['pol_id'];
            $txtSQL = "SELECT SUM(IFNULL(prl_rec,0)) as num FROM prl_receipt
                   WHERE prl_por = '$ord_po' AND prl_po = '$id_po'";
            $rs_re  = $db->query($txtSQL);
            foreach ($rs_re as $da) {
            ?>
                <?= ($data['pol_amount'] - $da['num']) . ' ' . $data['unit_name'] ?>
                <input type="hidden" name="prl_amount[]" class="form-control pr_amount" value="<?= ($data['pol_amount'] - $da['num']) ?>">
            <?php } ?>
        </td>
        <td>
            <div class="input-group">
                <input type="text" name="prl_rec[]" autocomplete="off" class="form-control text-center prl_rec num">
                <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon4"><?= $data['unit_name'] ?></span>
                </div>
            </div>
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
            <?php } ?>
            <?php if ($data['unit_name'] == 'มัด' || $data['unit_name'] == 'ลัง') { ?>
                <input type="hidden" name="cnt_con[]" class="cnt_con form-control" value="<?= $con['con_ratio'] ?>" readonly>
            <?php } else { ?>
                <input type="hidden" name="cnt_con[]" class="cnt_con1 form-control">
            <?php } ?>
        </td>
        <td>
            <div class="input-group">
                <input type="text" name="prl_bal[]" class="form-control text-center prl_bal" readonly>
                <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon4"><?= $data['unit_name'] ?></span>
                </div>
            </div>
        </td>
        <td>
            <?=number_format($data['cpl_price']).' '.'บาท'?>
            <input type="hidden" name="price[]" class="form-control text-center" value="<?= $data['cpl_price'] ?>">
        </td>
        <td>
            <button class="btn btn-danger remove" type="button"><i class="fa fa-minus"></i></button>
        </td>
    </tr>
<?php
} ?>
<script src="js/valid.js"></script>
<script>
    $(function() {
        $('input.prl_rec').on('keyup', function() {
            let par = $(this).closest('tr')
            let pr_amo = par.find('input.pr_amount').val();
            let pr_rec = par.find('input.prl_rec').val();
            if (pr_rec != '') {
                par.find('.prl_bal').val(pr_amo - pr_rec);
            } else {
                par.find('.prl_bal').val('');
            }

        });
        $('.remove').on('click', function() {
            $(this).parents('.del').remove();
        });
        $('input.prl_rec').on('blur', function() {
            let par = $(this).closest('tr');
            let cnt1 = par.find('input.pr_amount').val();
            let cnt2 = par.find('input.prl_rec').val();
            let cnt3 = par.find('input.prl_bal');
            if (cnt1 < cnt2) {
                alert('จำนวนที่รับมากกว่าจำนวนที่สั่งซื้อ');
                $(':input[type="submit"]').prop('disabled', true);
                cnt3.val("");
            } else {
                $(':input[type="submit"]').prop('disabled', false);
            }
        });
        $('input.prl_rec').on('keyup', function() {
            let par  = $(this).closest('tr');
            par.find('input.cnt_con1').val($(this).val());
        });
    });
</script>