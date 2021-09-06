<?php
session_start();
require('config/connect.php');
$db = new DB();
$txt_nic = $_SESSION['emp_nic'];
$txt_id  = $_SESSION['emp_id'];
/// สร้างรหัสรันเอง ///
$txtSQL = "SELECT concat('PR',NVL(lpad(substr(max(pr_id),4)+1,5,'0'),'00001')) as pr FROM pr_receipt";
$result = $db->query($txtSQL);
$fetch_id = mysqli_fetch_array($result);
/// สร้างเลขล็อต ///
$txtSQL = "SELECT concat('LOT',NVL(lpad(substr(max(lot_id),4)+1,5,'0'),'00001')) as lt FROM lot";
$result_lot = $db->query($txtSQL);
$fetch_lot = mysqli_fetch_array($result_lot);
/// ดึงข้อมูลใบสั่งซื้อสินค้า ///
$txtSQL = "SELECT pol_id,com_name FROM pol_purchase
           INNER JOIN proposed_cpl ON (pol_com=cpl_com)
           INNER JOIN cost_price ON (cpl_com=cp_com)
           INNER JOIN company ON (cp_com=com_id)
           LEFT JOIN prl_receipt as pr1 ON (pol_id=pr1.prl_po)
           LEFT JOIN prl_receipt ON (pol_order=pr1.prl_por)
           LEFT JOIN pr_receipt  ON (pr1.prl_id=pr_id)
           WHERE IFNULL(pr_status,0) = 0
           GROUP BY pol_id,com_name";
$rs_pr = $db->query($txtSQL);
$f_po = mysqli_fetch_array($rs_pr);
$id_fpo = $f_po['pol_id'];
/// บันทึกใบรับสินค้า ///
if (isset($_POST['pr_insert'])) {
    /// table pr_receipt ///
    $pr_id    = $_POST['pr_id'];
    $pr_start = $_POST['pr_start'];
    $pr_emp   = $_POST['pr_emp'];
    /// table prl_receipt ///
    $prl_rec = $_POST['prl_rec'];
    $prl_bal = $_POST['prl_bal'];
    $prl_por = $_POST['pol_order'];
    $prl_po  = $_POST['pol_id'];
    /// table lot ///
    $lot_id   = $fetch_lot['lt'];
    $lot_cost = $_POST['price'];
    $lot_pro  = $_POST['pr_pro'];
    $lot_amount = $_POST['cnt_con'];
    /// status pr ///
    $pr       = $_POST['pr'];
    /// array หัวใบรับสินค้า ///
    $arr = array(
        "pr_id" => $pr_id,
        "pr_date" => $pr_start,
        "pr_emp" => $pr_emp
    );
    $insert = $db->insert("pr_receipt", $arr);
    foreach ($prl_rec as $key => $data) {
        $order = $key + 1;
        $arr1 = array(
            "prl_order" => $order,
            "prl_rec" => $data,
            "prl_bal" => $prl_bal[$key],
            "prl_id" => $pr_id,
            "prl_por" => $prl_por[$key],
            "prl_po" => $prl_po[$key]
        );
        $insert1 = $db->insert("prl_receipt", $arr1);
    }
    if (!array_filter($prl_bal)) {
        foreach ($pr as $dt1) {
            $txtSQL = "UPDATE pr_receipt SET pr_status = 1 WHERE pr_id = '$pr_id' OR pr_id = '$dt1'";
            $rs     = $db->query($txtSQL);
        }
    } else {
        $txtSQL = "UPDATE pr_receipt SET pr_status = 0 WHERE pr_id IN ('$pr_id')";
        $rs     = $db->query($txtSQL);
    }
    foreach ($lot_amount as $key => $data) {
        $order1 = $key + 1;
            $arr2 = array(
                "lot_id" => $lot_id,
                "lot_pr" => $pr_id,
                "lot_order" => $order1,
                "lot_date" => $pr_start,
                "lot_amount" => $data,
                "lot_cost" => $lot_cost[$key],
                "lot_pro" => $lot_pro[$key]
            );
        $insert2 = $db->insert("lot", $arr2);
        if ($insert2) {
            echo "<script type='text/javascript'>";
            echo "alert('เพิ่มใบรับสินค้าสำเร็จ');";
            echo "window.location = 'pr_list.php'; ";
            echo "</script>";
        } else {
            echo "<script type='text/javascript'>";
            echo "alert('เพิ่มใบรับสินค้าไม่สำเร็จ');";
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
                            <h2>ใบรับสินค้า</h2>
                        </div>
                    </div>
                    <form name="pr_form" method="post">
                        <div class="card border-0  mt-3">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>รหัสใบสั่่งซื้อสินค้า/ชื่อบริษัทคู่ค้า</label>
                                        <select name="po_list" id="po_list" class="form-control custom-select">
                                            <option selected disabled>เลือกรหัสใบสั่งซื้อ/บริษัทคู่ค้า</option>
                                            <?php foreach ($rs_pr as $value) { ?>
                                                <option value="<?= $value['pol_id'] ?>"><?= $value['pol_id'] ?>/<?= $value['com_name'] ?></option>
                                            <?php } ?>
                                            <input type="hidden" name="po_id" id="po_id">
                                        </select>
                                    </div>
                                    <div class="form-group mt-auto">
                                        <button type="button" class="btn btn-primary select"><i class="fas fa-search"></i></button>
                                    </div>
                                    <div class="form-group col-md-2 ml-auto">
                                        <label>รหัสใบรับสินค้า</label>
                                        <input type="text" name="pr_id" class="form-control" value="<?= $fetch_id['pr'] ?>" readonly>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>วันที่รับสินค้า</label>
                                        <input type="date" name="pr_start" class="form-control">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-2 ml-auto">
                                        <label>ชื่อผู้รับสินค้า</label>
                                        <input type="text" name="pr_name" class="form-control" value="<?= $txt_nic ?>" readonly>
                                        <input type="hidden" name="pr_emp" value="<?= $txt_id ?>">
                                    </div>
                                </div>
                                <div class="pr_id">
                                </div>
                                <table class="table table-bordered text-center">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>รายการสินค้า</th>
                                            <th scope="col" width="15%">จำนวนที่สั่ง</th>
                                            <th scope="col" width="15%">จำนวนที่ได้รับ</th>
                                            <th scope="col" width="15%">จำนวนคงเหลือ</th>
                                            <th scope="col" width="13%">ราคา/หน่วย</th>
                                            <th scope="col" width="15%">เพิ่มเติม</th>
                                        </tr>
                                    </thead>
                                    <tbody class="shw_list_pr">
                                    </tbody>
                                </table>
                                <div class="form-row">
                                    <div class="form-group ml-auto">
                                        <input type="submit" name="pr_insert" class="btn btn-primary" value="บันทึกใบรับสินค้า">
                                        <button type="button" class="btn btn-danger cancel">ยกเลิกรายการใบรับสินค้า</button>
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
            $("#po_list").on("change", function() {
                var value = $(this).children("option:selected").attr("value");
                $("#po_id").val(value)
            });
            $(".select").on("click", function() {
                var po_list = $('#po_id').val();
                var tbody   = $('.shw_list_pr');
                if(tbody.children().length == 0){
                    $.ajax({
                    url: "list_pr.php",
                    type: "post",
                    data: {
                        data_po: po_list
                    },
                    success: function(data) {
                        $('tbody').append(data);
                    }
                });
                }else{
                    alert('มีรายการรับในตารางอยู่');
                    e.preventDefault();
                }
            });
            $('.cancel').on('click',function(e){
                e.preventDefault();
                $('.shw_list_pr').empty();
                $("#po_list").prop('selectedIndex',0);
                $("#po_id").val("");
            });
            $("#po_list").on("change", function() {
                var value = $(this).children("option:selected").attr("value");
                $.ajax({
                    url: "receipt.php",
                    type: "post",
                    data: {
                        data_pr: value
                    },
                    success: function(data) {
                        $('.pr_id').append(data);
                    }
                });
            });
        });
    </script>
</body>

</html>