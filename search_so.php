<?php
require('config/connect.php');
$db = new DB();
/// รับค่า จาก jquery search ///
$txt_search =  $_POST['del_so'];
/// เช็คค่ากรอกข้อมูลไม่เป็นค่าว่าง ///
if (!empty($txt_search)) {
    $txtSQL = "SELECT so_id,so_date,cus_name,so_total FROM sales_order
    INNER JOIN sales_order_list ON (so_id=sol_id)
    INNER JOIN quotation_cost as qc1 ON (order_quo=qc1.quoc_order)
    INNER JOIN quotation_cost ON (id_quo=qc1.quoc_id)
    INNER JOIN quotation_list as ql1 ON (qc1.quoc_order=ql1.quol_order)
    INNER JOIN quotation_list ON (qc1.quoc_id=ql1.quol_id)
    INNER JOIN quotation ON (ql1.quol_id=quo_id)
    INNER JOIN customer ON (quo_cus=cus_id)
    WHERE so_id = '$txt_search'
    GROUP BY so_id";
    $query = $db->query($txtSQL);
}
?>
<?php
foreach ($query as $data) {
?>
    <tr>
    <td><?=$data['so_id']?></td>
    <td><?=$data['so_date']?></td>
    <td><?=$data['cus_name']?></td>
    <td><?=number_format($data['so_total']).' '. 'บาท' ?></td>
    <td><button type="button" class="btn btn-info cho" value="<?=$data['so_id']?>">เลือกข้อมูลใบสั่งขาย</button></td>
    </tr>
<?php } ?>
<script>
    $(function() {
        $('.cho').on('click', function(e) {
            e.preventDefault();
            var so = $(this).val();
            $.ajax({
                url: "de_so.php",
                type: "post",
                data: {
                    txt: so
                },
                dataType: "json",
                success: function(response) {
                    for (let x = 0; x < response.length; x++) {
                        let pro_id = response[x].pro_id;
                        let pro_name = response[x].pro_name;
                        let size_name = response[x].size_name;
                        let sol_amt = response[x].sol_amt;
                        let unit_name = response[x].unit_name;
                        let sol_id = response[x].sol_id;
                        let sol_order = response[x].sol_order;
                        /// ทำรายการใบสั่งขาย ///
                        let row_dl = "<tr>" +
                            "<td>" + (x + 1) + "</td>" +
                            "<td>" + pro_name + ' ' + size_name + "<input type='hidden' name='id[]' class='form-control' value=" + sol_id + ">" + "</td>" +
                            "<td>" + sol_amt + ' ' + unit_name  + "<input type='hidden' name='order[]' class='form-control' value=" + sol_order + ">" + "<input type='hidden' name='ct' class='form-control ct' value=" + sol_amt + ">"+ "</td>" +
                            "<td>" + "<div class='input-group'><input type='text' name='key_cnt[]' autocomplete='off' class='form-control text-center key_cnt'><div class='input-group-append'><span class='input-group-text' id='basic-addon4'>" + unit_name + "</span></div></div>" + "</td>" +
                            "<td>" + "<span class='txt_cnt'></span>" + "<input type='hidden' name='txt_cnt[]' class='form-control txt_cnt'>" + "</td>" +
                            "<td>" + "<button class='btn btn-danger remove' type='button'><i class='fa fa-minus'></i></button>" + "</td>" +
                            "</tr>";
                        /// ตารางใบสั่งขาย ///
                        $('.main').append(row_dl);
                        /// คำนวณจำนวนคงเหลือ ///
                        $('input.key_cnt').on('keyup', function() {
                            let par = $(this).closest('tr')
                            let dl_amo = par.find('input.ct').val();
                            let dl_rec = par.find('input.key_cnt').val();
                            if (dl_rec != '') {
                                par.find('.txt_cnt').text(dl_amo-dl_rec+' '+unit_name);
                                par.find('input.txt_cnt').val(dl_amo-dl_rec);
                            } else {
                                par.find('.txt_cnt').text('');
                                par.find('input.txt_cnt').val('');
                            }
                        });
                        $('.remove').on('click', function() {
                            $(this).parents('tr').remove();
                        });
                    }
                }
            });
        });
    });
</script>