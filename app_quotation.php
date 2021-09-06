<?php
session_start();
require('config/connect.php');
$db = new DB();
// $txt_nic = $_SESSION['emp_nic'];
// $txt_id  = $_SESSION['emp_id'];
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if (isset($_SESSION['pos_permit'])) {
//     if (substr($_SESSION['pos_permit'], 2, 1) != 1) {
//         echo "<script type='text/javascript'>";
//         echo "alert('ไม่มีสิทธิ์ในการใช้งาน');";
//         echo "window.location = 'index.php'; ";
//         echo "</script>";
//     }
// }
/// ตัวแปร id ที่ส่งมากจาก ปุ่มแก้ไข ///
$id =  $_GET['pd_id'];
/// ดึงหัวใบเสนอสินค้า ///
$txtSQL = "SELECT pdq_id,pdq_start,pdq_end,emp_nic,pdq_emp from pd_quotation
           INNER JOIN employee ON (pdq_emp=emp_id) WHERE pdq_id = '$id'";
$result_head = $db->query($txtSQL);
$app_head = mysqli_fetch_assoc($result_head);
/// เฉพาะสินค้า ///
$txtSQL = "SELECT pro_id,pro_name,size_name,pdl1.pdl_order,pdl1.pdl_count,unit_name from proposed_cpl
           INNER JOIN pdl_quotation as pdl1 ON (cpl_id=pdl1.pdl_id)
           INNER JOIN pdl_quotation as pdl2 ON (cpl_order=pdl1.pdl_order)
           INNER JOIN cost_price ON (cpl_pro=cp_pro)
           INNER JOIN product ON (cp_pro=pro_id)
           INNER JOIN product_size ON (pro_size=size_id)
           INNER JOIN product_unit ON (pdl1.pdl_unit=unit_id)
           WHERE cpl_id = '$id'
           GROUP BY pro_id,pro_name,size_name,pdl1.pdl_order,pdl1.pdl_count,unit_name";
$result_pro = $db->query($txtSQL);
/// อนุมัติใบเสนอสินค้า ///
if (isset($_POST['app_insert'])) {
    $cpl_status = $_POST['order_com'];
    $pdl_order  = $_POST['app_order'];
    $pdl_app = $_POST['app_approve'];
    $status_pdq = $_POST['pdq_id'];
    $pdq_total = $_POST['pro_count'];
    /// วนค่าจำนวนที่อนุมัติเพื่อบันทึก ///
    foreach ($pdl_app as $key => $cnt_app) {
        $arr = array(
            "pdl_app" => $cnt_app
        );
        $where_condition3 = array(
            "pdl_id" => $status_pdq,
            "pdl_order" => $pdl_order[$key]
        );
        $update_app = $db->update("pdl_quotation", $arr, $where_condition3);
    }
    /// วนค่าเลือกบริษัทคู่ค้ามาอัพสเตตัส ///
    foreach ($cpl_status as $sta) {
        $id_com = substr($sta, 0, 7);
        $id_ord  = substr($sta, 7,8);
        $arr1 = array(
            "cpl_status" => 1
        );
        $where_condition = array(
            "cpl_com" => $id_com,
            "cpl_pro" => $id_ord,
        );
        $update_status = $db->update("proposed_cpl", $arr1, $where_condition);
    }
    /// อัพสเตตัสหัวใบเสนอสินค้า ///
    $arr2 = array(
        "pdq_status" => 1,
        "pdq_total" => $pdq_total
    );
    $where_condition1 = array(
        "pdq_id" => $status_pdq
    );
    $update_pdq = $db->update("pd_quotation", $arr2, $where_condition1);
    if ($update_pdq) {
        echo "<script type='text/javascript'>";
        echo "alert('อนุมัติใบเสนอสำเร็จ');";
        echo "window.location = 'pd_quo.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('อนุมัติใบเสนอไม่สำเร็จ');";
        echo "</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('includes/head.php') ?>

<body>
    <?php include('includes/sidebar.php') ?>
    <main>
        <div class="container-fluid pt-3">
            <div class="row">
                <div class="col-md-10 ml-auto">
                    <div class="card border-0 mt-5">
                        <div class="card-body">
                            <h2>อนุมัติใบเสนอสินค้า</h2>
                        </div>
                    </div>
                    <form name="app_quotation" method="post">
                        <div class="card border-0  mt-3">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="mess">
                                    </div>
                                    <!-- <div class="alert alert-danger ml-1" role="alert">
                                        จำนวนที่อนุมัติไม่เท่ากับจำนวนที่เสนอ
                                    </div> -->
                                    <div class="form-group col-md-2 ml-auto">
                                        <label>รหัสใบเสนอสินค้า</label>
                                        <input type="text" name="pdq_id" value="<?= $app_head['pdq_id'] ?>" class="form-control" readonly>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>วันที่เสนอ</label>
                                        <input type="date" name="pdq_start" class="form-control" value="<?= $app_head['pdq_start'] ?>" readonly>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-2 ml-auto">
                                        <label>ชื่อผู้เสนอ</label>
                                        <input type="text" name="pdq_name" class="form-control" value="<?= $app_head['emp_nic'] ?>" readonly>
                                        <input type="hidden" name="emp_id" value="<?= $app_head['pdq_emp'] ?>">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>วันที่ครบกำหนด</label>
                                        <input type="date" name="pdq_end" class="form-control" value="<?= $app_head['pdq_end'] ?>" readonly>
                                    </div>
                                </div>
                                <table class="table table-bordered">
                                    <thead class="thead-dark text-center">
                                        <tr>
                                            <th>ลำดับ</th>
                                            <th scope="col" width="20%">สินค้า</th>
                                            <th scope="col" width="14%">จำนวนที่เสนอ</th>
                                            <th scope="col" width="20%"> บริษัทคู่ค้า</th>
                                            <th scope="col" width="12%">ราคาต้นทุน</th>
                                            <th scope="col" width="15%">จำนวนที่อนุมัติ</th>
                                            <th scope="col" width="12%">รวมเงิน</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $order = 1;
                                        foreach ($result_pro as $key => $data) {
                                        ?>
                                            <tr>
                                                <td class="text-center"><strong><?= $order ?></strong></td>
                                                <td>
                                                    <input type="text" name="app_pro" class="form-control" value="<?= $data['pro_name'] . ' ' . $data['size_name'] ?>" readonly>
                                                    <input type="hidden" name="app_pro" id="app_pro" class="form-control" value="<?= $data['pro_id'] ?>">
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="app_amount[]" class="form-control app_amount" value="<?= $data['pdl_count'] ?>" aria-describedby="basic-addon2" readonly>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon2"><?= $data['unit_name'] ?></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <select name="sel_com" class="form-control custom-select sel_com" required>
                                                        <option value="">กรุณาเลือกบริษัทคู่ค้า</option>
                                                        <?php
                                                        $id_pro = $data['pro_id'];
                                                        $txtSQL = "SELECT com_id,com_name,cpl_pro from proposed_cpl
                                                        INNER JOIN cost_price ON (cpl_com=cp_com)
                                                        INNER JOIN company ON (cp_com=com_id)
                                                        WHERE cpl_pro = '$id_pro'
                                                        GROUP BY com_id,com_name,cpl_pro";
                                                        $result_company = $db->query($txtSQL);
                                                        foreach ($result_company as $com) {
                                                        ?>
                                                            <option value="<?= $com['com_id'] ?><?= $com['cpl_pro'] ?>"><?= $com['com_name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <input type="hidden" name="order_com[]" class="order_com">
                                                </td>
                                                <td class="text-center">
                                                    <span class="app_txt"></span>
                                                    <input type="hidden" name="app_price" class="form-control app_price" aria-describedby="basic-addon4" readonly>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="app_approve[]" class="form-control app_approve num" placeholder="จำนวน" aria-describedby="basic-addon3" autocomplete="off">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon3"><?= $data['unit_name'] ?></span>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="app_order[]" value="<?= $data['pdl_order'] ?>">
                                                    <span class='message'></span>
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
                                                        <input type="hidden" name="txt_con" class="txt_con" value="<?= $con['con_ratio'] ?>">
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="grand">
                                                        <span class="txt_tol"></span>
                                                        <input type="hidden" name="total" class="form-control total" readonly>
                                                    </div>
                                                </td>
                                            </tr>
                                    </tbody>
                                <?php $order++;
                                        } ?>
                                </table>
                                <table class="table table-bordered ml-auto" style="width: 40%;">
                                    <tr>
                                        <td colspan="2" style="text-align: right;"><strong>ราคารวมสินค้าทั้งหมด</strong></td>
                                        <td class="text-center">
                                            <span class="txt_cnt"></span>
                                            <input type="hidden" name="pro_count" id="pro_count" class="form-control" readonly>
                                        </td>
                                    </tr>
                                </table>
                                <div class="form-row">
                                    <div class="form-group ml-auto">
                                        <input type="submit" name="app_insert" class="btn btn-primary sub" value="อนุมัติใบเสนอสินค้า">
                                        <a href="pd_quo.php" type="button" class="btn btn-danger">ยกเลิกอนุมัติใบเสนอ</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/script.php') ?>
    <script src="js/valid.js"></script>
    <script>
        $(function() {
            $('.sel_com').on('change', function() {
                let company = $(this).val();
                let index_key = $('.sel_com').index(this);
                $('.order_com').eq(index_key).val(company);
                $.ajax({
                    url: "product_cost.php",
                    type: "post",
                    data: {
                        cost: company,
                    },
                    success: function(data) {
                        $('.app_txt').eq(index_key).text(Number(data).toLocaleString('en')+' '+'บาท')
                        $('.app_price').eq(index_key).val(data)
                    }
                });
            });
            $('input.app_approve').on('keyup', function() {
                let par = $(this).closest('tr')
                let int_count = par.find('input.app_approve').val();
                let int_count1 = par.find('input.txt_con').val();
                let price = par.find('input.app_price').val();
                if (int_count1) {
                    par.find('.txt_tol').text(Number(price*int_count1).toLocaleString('en')+' '+'บาท');
                    par.find('.total').val(price * int_count1);
                } else {
                    par.find('.txt_tol').text(Number(price*int_count).toLocaleString('en')+' '+'บาท');
                    par.find('.total').val(price * int_count);
                }
                var cnt = 0;
                $('.grand').find('input.total').each(function() {
                    if (!isNaN($(this).val())) {
                        cnt += parseInt($(this).val());
                    }
                });
                if (isNaN(cnt)) {
                    cnt = parseInt($('.total').val());
                }
                $('.txt_cnt').text(Number(cnt).toLocaleString('en')+' '+'บาท');
                $('#pro_count').val(cnt);
            });
            $('input.app_approve').on('keyup', function() {
                let par  = $(this).closest('tr');
                let cnt1 = par.find('input.app_amount').val();
                let cnt2 = par.find('input.app_approve').val();
                let cnt3 = par.find('input.total');
                if (cnt1 < cnt2) {
                    $('.mess').addClass('alert alert-danger').html('จำนวนที่อนุมัติไม่เท่ากับจำนวนที่เสนอ');
                    $(':input[type="submit"]').prop('disabled', true);
                    cnt3.val("");
                    $('#pro_count').val("");
                } else{
                    $('.mess').removeClass('alert alert-danger').empty();
                    $(':input[type="submit"]').prop('disabled', false);
                }
            });
        });
    </script>
</body>

</html>