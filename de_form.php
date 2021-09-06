<?php
session_start();
require('config/connect.php');
$db = new DB();
$txt_nic = $_SESSION['emp_nic'];
$txt_id  = $_SESSION['emp_id'];
/// สร้างรหัสรันเอง ///
$txtSQL = "SELECT concat('DL',NVL(lpad(substr(max(dl_id),4)+1,6,'0'),'000001')) as dl FROM delivery_note";
$result = $db->query($txtSQL);
$fetch_id = mysqli_fetch_assoc($result);
/// บันทึกใบรับสินค้า ///
if (isset($_POST['de_insert'])) {
    /// delivery_note ///
    $dl_id    = $_POST['dl_id'];
    $dl_date  = $_POST['dl_date'];
    $dl_truck = $_POST['tru_id'];
    $dl_way   = $_POST['tra_id'];
    $dl_emp   = $_POST['emp_id'];
    /// delivery_list ///
    $del_cnt  = $_POST['key_cnt'];
    $del_re   = $_POST['txt_cnt'];
    $del_soi  = $_POST['id'];
    $del_sor  = $_POST['order'];
    $prod     = $_POST['pro'];
    $so_ct    = $_POST['ct'];
    /// chk status ///
    $dl       = $_POST['dl'];
    /// insert delivery_note ///
    $arr = array(
        "dl_id"    => $dl_id,
        "dl_date"  => $dl_date,
        "dl_truck" => $dl_truck,
        "dl_way"   => $dl_way,
        "dl_emp"   => $dl_emp
    );
    $insert = $db->insert("delivery_note",$arr);
    /// insert delivery_list ///
    foreach($del_cnt as $key => $data){
        $order = $key + 1;
        $arr1 = array(
            "del_order" => $order,
            "del_id"    => $dl_id,
            "del_cnt"   => $data,
            "del_re"    => $del_re[$key],
            "del_qor"   => $del_sor[$key],
            "del_qoi"   => $del_soi[$key],
            "del_qpr"   => $prod[$key],
        );
        $insert1 = $db->insert("delivery_list",$arr1);
    }
        /// ตัดสต็อก ///
    foreach ($prod as $key => $data) {
        $txtSQL = "SELECT lot_id,lot_pr,lot_amount,lot_order,lot_pro FROM lot WHERE lot_pro = '$data' AND lot_amount != 0";
        $rs_stock = $db->query($txtSQL);
        /// จำนวนที่เสนอราคา ///
        $amount = $del_cnt[$key];
        $ttt  = $data;
        foreach ($rs_stock as $data) {
            $num_lot = $data['lot_id'];
            $amt_lot = $data['lot_amount'];
            $pro_lot = $data['lot_pro'];
            $lot_pr  = $data['lot_pr'];
            $lot_order = $data['lot_order'];
            if ($amt_lot >= $amount) {
                $arr = array(
                    "lot_amount" => $amt_lot - $amount
                );
                $where_condition = array(
                    "lot_id" => $num_lot,
                    "lot_pro" => $pro_lot
                );
                $update_stc = $db->update("lot", $arr, $where_condition);
                $arr_up = array(
                    "del_lot" => $num_lot,
                    "del_pr" => $lot_pr,
                    "del_lotr" => $lot_order
                );
                $where_condition = array(
                    "del_id" => $dl_id,
                    "del_qpr" => $ttt
                );
                $update = $db->update("delivery_list",$arr_up,$where_condition);
                break;
            } else if ($amt_lot - $amount != 0) {
                $arr = array(
                    "lot_amount" => 0
                );
                $where_condition = array(
                    "lot_id" => $num_lot,
                    "lot_pro" => $pro_lot
                );
                $update_stc = $db->update("lot", $arr, $where_condition);
                $arr_up = array(
                    "del_lot" => $num_lot,
                    "del_pr" => $lot_pr,
                    "del_lotr" => $lot_order
                );
                $where_condition = array(
                    "del_id" => $dl_id,
                    "del_qpr" => $ttt
                );
                $update = $db->update("delivery_list",$arr_up,$where_condition);
                $amount = $amount - $amt_lot;
            } else {
                $arr = array(
                    "lot_amount" => $amt_lot - $amount
                );
                $where_condition = array(
                    "lot_id" => $num_lot,
                    "lot_pro" => $pro_lot
                );
                $update_stc = $db->update("lot", $arr, $where_condition);
                $arr_up = array(
                    "del_lot" => $num_lot,
                    "del_pr" => $lot_pr,
                    "del_lotr" => $lot_order
                );
                $where_condition = array(
                    "del_id" => $dl_id,
                    "del_qpr" => $ttt
                );
                $update = $db->update("delivery_list",$arr_up,$where_condition);
                $amount = $amount - $amt_lot;
            }
        }
    }
    foreach ($prod as $key => $data) {
        $txtSQL = "SELECT quo_pro FROM product WHERE pro_id = '$data'";
        $rs     = $db->query($txtSQL);
        /// จำนวนที่เสนอราคา ///
        $amount = $del_cnt[$key];
        /// สินค้า ///
        $p = $data;
        foreach($rs as $data){
            $cnt = $data['quo_pro'];
            $arr_p = array(
                "quo_pro" => $cnt - $amount
            );
            $where_condition = array(
                "pro_id" => $p
            );
            $update = $db->update("product",$arr_p,$where_condition);
        }
    }
    ///
    if (!array_filter($del_re)) {
        foreach ($dl as $dt1) {
            $txtSQL = "UPDATE delivery_note SET dl_status = 1 WHERE dl_id = '$dl_id' OR dl_id = '$dt1'";
            $rs     = $db->query($txtSQL);
        }
    } else {
        $txtSQL = "UPDATE delivery_note SET dl_status = 0 WHERE dl_id IN ('$dl_id')";
        $rs     = $db->query($txtSQL);
    }
    $arr_tru = array(
        "empty_truck" => 0
    );
    $where_condition_way = array(
        "tru_id" => $dl_truck
    );
    $update_tru = $db->update("trucks",$arr_tru,$where_condition_way);
    if ($update_tru) {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มใบส่งของสำเร็จ');";
        echo "window.location = 'delivery.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มใบส่งของไม่สำเร็จ');";
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
                        <div class="card-body d-sm-flex justify-content-between">
                            <h2>ใบส่งของ</h2>
                        </div>
                    </div>
                    <form name="quo_form" method="post">
                        <div class="card border-0  mt-3">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>เลขทะเบียน/ประเภทรถ</label>
                                        <input type="text" name="tru_name" class="form-control tru_name" readonly>
                                        <input type="hidden" name="tru_id" class="form-control tru_id" readonly>
                                    </div>
                                    <div class="form-group mt-auto ml-1">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong"><i class="fas fa-search"></i></button>
                                        <div class="modal fade bd-example-modal-lg" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title" id="exampleModalLongTitle">ข้อมูลรถขนส่ง</h3>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body" id="info_tru">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mt-auto ml-1">
                                        <button type="button" class="btn btn-primary shw1" data-toggle="modal" data-target="#exampleModalLong1">เลือกรายการใบสั่งขาย</button>
                                        <div class="modal fade bd-example-modal-lg" id="exampleModalLong1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title" id="exampleModalLongTitle">ข้อมูลรายการใบสั่งขาย</h3>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body" id="info_so">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2 ml-auto">
                                        <label>รหัสใบส่งของ</label>
                                        <input type="text" name="dl_id" class="form-control" value="<?= $fetch_id['dl'] ?>" readonly>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>วันที่ส่งของ</label>
                                        <input type="date" name="dl_date" class="form-control">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>สถานที่จัดส่ง (หมายเหตุ)</label>
                                        <input type="text" name="tra_name" class="form-control tra_name" readonly>
                                        <input type="hidden" name="tra_id"   class="form-control tra_id">
                                    </div>
                                    <div class="form-group mt-auto ml-1">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong2"><i class="fas fa-search"></i></button>
                                        <div class="modal fade bd-example-modal-lg" id="exampleModalLong2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title" id="exampleModalLongTitle">ข้อมูลสถานที่ส่ง</h3>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body" id="info_tra">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2 ml-auto">
                                        <label>ชื่อพนักงานขาย</label>
                                        <input type="text" name="emp_name" class="form-control" value="<?= $txt_nic ?>" readonly>
                                        <input type="hidden" name="emp_id" value="<?= $txt_id ?>">
                                    </div>
                                </div>
                                <div class="dl_id">
                                </div>
                                <table class="table table-bordered text-center">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>ลำดับ</th>
                                            <th>รายการสินค้า</th>
                                            <th scope="col" width="16%">จำนวนที่ขาย</th>
                                            <th scope="col" width="16%">จำนวนที่ส่ง</th>
                                            <th scope="col" width="16%">จำนวนคงเหลือ</th>
                                            <th>เพิ่มเติม</th>
                                        </tr>
                                    </thead>
                                    <tbody class="main">
                                    </tbody>
                                </table>
                                <div class="form-row">
                                    <div class="form-group">
                                        <input type="submit" name="de_insert" class="btn btn-primary" value="บันทึกใบส่งของ">
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
            function loadData(page) {
                $.ajax({
                    url: "pagination_tru.php",
                    type: "POST",
                    // cache: false,
                    data: {
                        page_no: page
                    },
                    success: function(data) {
                        $("#info_tru").html(data);
                    }
                });
            }
            loadData();
            $(document).on("click", ".pagination li a", function(e) {
                e.preventDefault();
                var pageId = $(this).attr("id");
                loadData(pageId);
            });

            function loadData1(page) {
                $.ajax({
                    url: "pagination_so.php",
                    type: "POST",
                    // cache: false,
                    data: {
                        page_no: page
                    },
                    success: function(data) {
                        $("#info_so").html(data);
                    }
                });
            }
            loadData1();
            $(document).on("click", ".pagination li a", function(e) {
                e.preventDefault();
                var pageId = $(this).attr("id");
                loadData1(pageId);
            });
            function loadData2(page) {
                $.ajax({
                    url: "pagination_tran.php",
                    type: "POST",
                    // cache: false,
                    data: {
                        page_no: page
                    },
                    success: function(data) {
                        $("#info_tra").html(data);
                    }
                });
            }
            loadData2();
            $(document).on("click", ".pagination li a", function(e) {
                e.preventDefault();
                var pageId = $(this).attr("id");
                loadData1(pageId);
            });
        });
    </script>
</body>

</html>