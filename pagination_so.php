<?php
require('config/connect.php');
$db = new DB();
if (isset($_POST['page_no'])) {
    $page_no = $_POST['page_no'];
} else {
    $page_no = 1;
}
$limit = 5;
$start    = ($page_no - 1) * $limit;
$previous =  $page_no - 1;
$next     =  $page_no + 1;
/// ดึงข้อมูลสินค้าที่ต่ำกว่าจุดสั่งซื้อและสินค้าทั้งหมด ///
$txtSQL = "SELECT so_id,so_date,cus_name,so_total FROM quotation
INNER JOIN customer       ON (quo_cus=cus_id)
INNER JOIN quotation_list ON (quo_id=quol_id)
INNER JOIN quotation_cost as qc1 ON (quol_id=qc1.quoc_id)
INNER JOIN quotation_cost ON (quol_order=qc1.quoc_order)
INNER JOIN sales_order_list as s1 ON (qc1.quoc_id=s1.id_quo)
INNER JOIN sales_order_list ON (qc1.quoc_order=s1.order_quo)
INNER JOIN sales_order    ON (s1.sol_id=so_id)
LEFT JOIN  delivery_list as de1 ON (qc1.quoc_order=de1.del_qor)
LEFT JOIN  delivery_list ON (qc1.quoc_id=de1.del_qoi)
LEFT JOIN  delivery_note ON (de1.del_id=dl_id)
WHERE IFNULL(dl_status,0) = 0
GROUP BY so_id LIMIT $start,$limit";
$result_so  =  $db->query($txtSQL);
$output = "";
$output .= '<div class="form-row">
            <div class="form-group col-md-3 mt-auto ml-auto">
            <input type="text" name="id_so" id="id_so" autocomplete="off" placeholder="รหัสใบสั่งขาย" class="form-control">
            </div>
            <div class="form-group mt-auto mr-2">
            <button type="button" class="btn btn-primary select"><i class="fas fa-search"></i></button>
            </div>
            </div>
            <table class="table table-bordered text-center display">
                <thead class="thead-dark">
                    <tr>
                        <th>รหัสใบสั่งขาย</th>
                        <th>ชื่อลูกค้า</th>
                        <th>วันที่สั่งขาย</th>
                        <th>ราคารวมทั้งหมด</th>
                        <th>เพิ่มเติม</th>
                    </tr>
                </thead>
            <tbody class="dl_list">';
foreach ($result_so as $da_ta) {
    $output .= '<tr>
                <td>' . $da_ta['so_id'] . '</td>
                <td>' . $da_ta['so_date'] . '</td>
                <td>' . $da_ta['cus_name'] . '</td>
                <td>' . number_format($da_ta['so_total']) . ' ' . 'บาท' . '</td>
                <td>
                    <button type="button" class="btn btn-info cho" value="' . $da_ta['so_id'] . '">เลือกข้อมูลใบสั่งขาย</button>
                </td>
            </tr>';
}
$output .= '</tbody>
                </table>';
$txtSQL = "SELECT count(so_id) as total FROM sales_order";
$result = $db->query($txtSQL);
$fetch_rec  = mysqli_fetch_assoc($result);
$record  = $fetch_rec['total'];
$total_pages   = ceil($record / $limit);
$output .= '<nav aria-label="Page navigation example"><ul class="pagination">';
if ($page_no > 1) {
    $output .= '<li class="page-item"><a class="page-link" id="' . $previous . '">Previous</a></li>';
} else {
    $output .= '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">Previous</a></li>';
}
for ($i = 1; $i <= $total_pages; $i++) {
    if ($i == $page_no) {
        $active = "active";
    } else {
        $active = "";
    }
    $output .= '<li class="page-item ' . $active . '"><a class="page-link" id="' . $i . '" href="">' . $i . '</a></li>';
}
if ($page_no < $total_pages) {
    $output .= '<li class="page-item"><a class="page-link" id="' . $next . '">Next</a></li>';
} else {
    $output .= '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">Next</a></li>';
}

$output .= '</ul></nav>';

echo $output;
?>
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
                        let pro_name = response[x].pro_name;
                        let size_name = response[x].size_name;
                        let sol_amt = response[x].sol_amt;
                        let unit_name = response[x].unit_name;
                        let quoc_id = response[x].quoc_id;
                        let quoc_order = response[x].quoc_order;
                        let quoc_pro = response[x].quoc_pro;
                        let sd_cnt   = response[x].sd_cnt;
                        /// ทำรายการใบสั่งขาย ///
                        let row_dl = "<tr>" +
                            "<td>" + (x + 1) + "<input type='hidden' name='pro[]' class='form-control' value=" + quoc_pro + ">" + "</td>" +
                            "<td>" + pro_name + ' ' + size_name + "<input type='hidden' name='id[]' class='form-control' value=" + quoc_id + ">" + "</td>" +
                            "<td>" + (sol_amt-sd_cnt) + ' ' + unit_name + "<input type='hidden' name='order[]' class='form-control' value=" + quoc_order + ">" + "<input type='hidden' name='ct[]' class='form-control ct' value=" + (sol_amt-sd_cnt)  + ">" + "</td>" +
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
                                par.find('.txt_cnt').text(dl_amo - dl_rec + ' ' + unit_name);
                                par.find('input.txt_cnt').val(dl_amo - dl_rec);
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
        $('.select').on('click', function(e) {
            e.preventDefault();
            var txt_so = $('#id_so').val();
            $.ajax({
                url: "search_so.php",
                type: "post",
                data: {
                    del_so: txt_so
                },
                success: function(data) {
                    $('.dl_list').html(data);
                }
            });
        });
        $('.cho').on('click', function(e) {
            e.preventDefault();
            var dl = $(this).val();
            $.ajax({
                url: "deliver.php",
                type: "post",
                data: {
                    txt: dl
                },
                success: function(response) {
                    $('.dl_id').append(response);
                }
            });
        });
    });
</script>