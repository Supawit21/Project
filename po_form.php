<?php
session_start();
require('config/connect.php');
$db = new DB();
$txt_nic = $_SESSION['emp_nic'];
$txt_id  = $_SESSION['emp_id'];
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if (isset($_SESSION['pos_permit'])) {
//     if (substr($_SESSION['pos_permit'], 2, 1) != 1) {
//         echo "<script type='text/javascript'>";
//         echo "alert('ไม่มีสิทธิ์ในการใช้งาน');";
//         echo "window.location = 'index.php'; ";
//         echo "</script>";
//     }
// }
/// สร้างรหัสรันเอง ///
$txtSQL = "SELECT concat('PO',NVL(lpad(substr(max(po_id),4)+1,5,'0'),'00001')) as po FROM po_purchase";
$result = $db->query($txtSQL);
$fetch_id = mysqli_fetch_assoc($result);
/// เลือกใบเสนอ,บริษัทคู่ค้าที่อนุมัติ ///
$txtSQL = "SELECT cpl_id,cpl_com,com_name FROM proposed_cpl
           INNER JOIN cost_price ON (cpl_com=cp_com)
           INNER JOIN company    ON (cp_com=com_id)
           WHERE cpl_status = 1 AND NOT EXISTS (SELECT pol_pdq,pol_com FROM pol_purchase WHERE cpl_id=pol_pdq AND cpl_com=pol_com)
           GROUP BY cpl_id,cpl_com,com_name";
$rs_quo = $db->query($txtSQL);
/// บันทึกใบเสนอสินค้า ///
if (isset($_POST['po_insert'])) {
    /// table po_purchase ///
    $po_id = $_POST['po_id'];
    $po_start = $_POST['po_start'];
    $po_end = $_POST['po_end'];
    $po_emp = $_POST['po_emp'];
    $po_total = $_POST['po_total'];
    /// table pol_purchase ///
    $pol_amount = $_POST['pol_amount'];
    $pol_all  = $_POST['pol_all'];
    $pol_pdq  = $_POST['pol_pdq'];
    $pol_por  = $_POST['pol_por'];
    $pol_pro = $_POST['pol_pro'];
    $pol_com  = $_POST['pol_com'];
    /// array หัวใบสั่งซื้อ ///
    $arr = array(
        "po_id" => $po_id,
        "po_start" => $po_start,
        "po_end" => $po_end,
        "po_emp" => $po_emp,
        "po_total" => $po_total,
        "po_status" => 1
    );
    $insert = $db->insert("po_purchase",$arr);
    foreach($pol_pro as $key => $data){
        $order = $key + 1;
        $arr1 = array(
            "pol_order" => $order,
            "pol_amount" => $pol_amount[$key],
            "pol_all" => $pol_all[$key],
            "pol_id" => $po_id,
            "pol_pdq" => $pol_pdq[$key],
            "pol_por" => $pol_por[$key],
            "pol_pro" => $data,
            "pol_com" => $pol_com[$key]
        );
        $insert1 = $db->insert("pol_purchase",$arr1);
        if ($insert1) {
            echo "<script type='text/javascript'>";
            echo "alert('เพิ่มข้อมูลสำเร็จ');";
            echo "window.location = 'po_list.php'; ";
            echo "</script>";
        } else {
            echo "<script type='text/javascript'>";
            echo "alert('เพิ่มข้อมูลไม่สำเร็จ');";
            echo "</script>";
        }
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
                        <div class="card-body d-sm-flex justify-content-between">
                            <h2>ใบสั่งซื้อสินค้า</h2>
                        </div>
                    </div>
                    <form name="pd_form" method="post">
                        <div class="card border-0  mt-3">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>รหัสใบเสนอสินค้า/ชื่อบริษัทคู่ค้า</label>
                                        <select name="quo_list" id="quo_list" class="form-control custom-select">
                                            <option selected disabled>เลือกรหัสใบเสนอ/บริษัทคู่ค้า</option>
                                            <?php foreach ($rs_quo as $value) { ?>
                                                <option value="<?= $value['cpl_id'] ?><?= $value['cpl_com'] ?>"><?= $value['cpl_id'] ?>/<?= $value['com_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="quo_id" id="quo_id">
                                    </div>
                                    <div class="form-group mt-auto">
                                        <button type="button" class="btn btn-primary select"><i class="fas fa-search"></i></button>
                                    </div>
                                    <div class="form-group col-md-2 ml-auto">
                                        <label>รหัสใบสั่งซื้อ</label>
                                        <input type="text" name="po_id" class="form-control" value="<?= $fetch_id['po'] ?>" readonly>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>วันที่สั่งซื้อ</label>
                                        <input type="date" name="po_start" class="form-control" value="<?= date('Y-m-d'); ?>" readonly>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-2 ml-auto">
                                        <label>ชื่อผู้สั่งซื้อ</label>
                                        <input type="text" name="po_name" class="form-control" value="<?= $txt_nic ?>" readonly>
                                        <input type="hidden" name="po_emp" value="<?= $txt_id ?>">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>วันที่ครบกำหนด</label>
                                        <input type="date" name="po_end" class="form-control">
                                    </div>
                                </div>
                                <table class="table table-bordered text-center">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>ลำดับ</th>
                                            <th>รายการสินค้า</th>
                                            <th>จำนวน</th>
                                            <th>ราคา/หน่วย</th>
                                            <th>รวมเงิน</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list">
                                    </tbody>
                                </table>
                                <div class="form-row">
                                    <div class="form-group ml-auto">
                                        <input type="submit" name="po_insert" class="btn btn-primary" value="บันทึกใบสั่งซื้อสินค้า">
                                        <button type="button" class="btn btn-danger cancel">ยกเลิกรายการใบสั่งซื้อสินค้า</button>
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
    <script>
        $(function() {
            $("#quo_list").on("change", function() {
                var value = $(this).children("option:selected").attr("value");
                $("#quo_id").val(value)
            });
            $('.select').on('click', function(e) {
                e.preventDefault();
                var h_quo = $('#quo_id').val();
                var tbody = $('.list');
                if(tbody.children().length == 0){
                    $.ajax({
                    url: "list_product.php",
                    type: "post",
                    data: {
                        data_pro: h_quo
                    },
                    success: function(data) {
                        $('.list').append(data);
                    }
                });
                }else{
                    alert('มีรายการสั่งซื้อในตารางอยู่');
                    e.preventDefault();
                }
            });
            $('.cancel').on('click',function(e){
                e.preventDefault();
                $('.list').empty();
                $("#quo_list").prop('selectedIndex',0);
                $("#quo_id").val("");
            });
        });
    </script>
</body>

</html>