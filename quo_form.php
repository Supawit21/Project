<?php
session_start();
require('config/connect.php');
$db = new DB();
$txt_nic = $_SESSION['emp_nic'];
$txt_id  = $_SESSION['emp_id'];
/// สร้างรหัสรันเอง ///
$txtSQL = "SELECT concat('QUO',NVL(lpad(substr(max(quo_id),4)+1,5,'0'),'00001')) as quo FROM quotation";
$result = $db->query($txtSQL);
$fetch_id = mysqli_fetch_assoc($result);
/// บันทึกใบรับสินค้า ///
if (isset($_POST['quo_insert'])) {
    /// table quotation ///
    $quo_id    = $_POST['quo_id'];
    $quo_start = $_POST['quo_start'];
    $quo_end   = $_POST['quo_end'];
    // $quo_cnt   = $_POST['day_cnt'];
    $quo_tol   = $_POST['txt_tol3'];
    $quo_emp   = $_POST['emp_id'];
    $quo_cus   = $_POST['cus_id'];
    /// table quotation_list ///
    $quol_cnt  = $_POST['cnt'];
    $quol_sum  = $_POST['txt_tol'];
    /// table quotation_cost ///
    $quoc_pro  = $_POST['pro_id'];
    $quoc_cost = $_POST['quo_cost'];
    $arr = array(
        "quo_id"     => $quo_id,
        "quo_start"  => $quo_start,
        "quo_end"    => $quo_end,
        // "quo_cnt"    => $quo_cnt,
        "quo_status" => 0,
        "quo_emp"    => $quo_emp,
        "quo_cus"    => $quo_cus
    );
    $insert = $db->insert("quotation", $arr);
    foreach ($quol_cnt as $key => $data) {
        $order = $key + 1;
        $arr1 = array(
            "quol_order" => $order,
            "quol_id"    => $quo_id,
            "quol_cnt"   => $data
        );
        $insert1 = $db->insert("quotation_list", $arr1);
    }
    foreach ($quoc_pro as $key => $data) {
        $order = $key + 1;
        $arr2 = array(
            "quoc_order"  => $order,
            "quoc_id"     => $quo_id,
            "quoc_pro"    => $data,
            "quoc_cost"   => $quoc_cost[$key]
        );
        $insert2 = $db->insert("quotation_cost", $arr2);
    }
    foreach($quoc_pro as $key => $data){
        $txtSQL = "SELECT quo_pro FROM product WHERE pro_id='$data'";
        $rs_stock = $db->query($txtSQL);
        /// จำนวนสินค้า ///
        $amount = $quol_cnt[$key];
        /// สินค้า ///
        $p = $data;
        foreach($rs_stock as $data){
            $cnt = $data['quo_pro'];
            $arr_p = array(
                "quo_pro" => $cnt + $amount
            );
            $where_condition = array(
                "pro_id" => $p
            );
            $update = $db->update("product",$arr_p,$where_condition);
            if ($update) {
                echo "<script type='text/javascript'>";
                echo "alert('เพิ่มใบเสนอราคาสำเร็จ');";
                echo "window.location = 'quo_list.php'; ";
                echo "</script>";
            } else {
                echo "<script type='text/javascript'>";
                echo "alert('เพิ่มใบเสนอราคาไม่สำเร็จ');";
                echo "</script>";
            }
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
                            <h2>ใบเสนอราคา</h2>
                        </div>
                    </div>
                    <form name="quo_form" method="post">
                        <div class="card border-0  mt-3">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group ml-2">
                                        <label>ชื่อลูกค้า</label>
                                        <input type="text" name="cus_name" class="form-control txt_name" readonly>
                                        <input type="hidden" name="cus_id" class="form-control txt_id" readonly>
                                    </div>
                                    <div class="form-group mt-auto ml-1">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong"><i class="fas fa-search"></i></button>
                                        <div class="modal fade bd-example-modal-lg" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title" id="exampleModalLongTitle">ข้อมูลลูกค้า</h3>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body" id="info_cus">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mt-auto ml-1">
                                        <button type="button" class="btn btn-primary shw1" data-toggle="modal" data-target="#exampleModalLong1">เลือกสินค้าที่จะเสนอราคา</button>
                                        <div class="modal fade bd-example-modal-lg" id="exampleModalLong1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title" id="exampleModalLongTitle">ข้อมูลสินค้า</h3>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body" id="info_lot">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2 ml-auto">
                                        <label>รหัสใบเสนอราคา</label>
                                        <input type="text" name="quo_id" class="form-control" value="<?= $fetch_id['quo'] ?>" readonly>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>วันที่เสนอราคา</label>
                                        <input type="date" name="quo_start" id="quo_start" class="form-control" value="<?= date('Y-m-d'); ?>" readonly>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-2 ml-auto">
                                        <label>ชื่อผู้รับสินค้า</label>
                                        <input type="text" name="emp_name" class="form-control" value="<?= $txt_nic ?>" readonly>
                                        <input type="hidden" name="emp_id" value="<?= $txt_id ?>">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>วันที่ครบกำหนด</label>
                                        <input type="date" name="quo_end" id="quo_end" class="form-control">
                                    </div>
                                </div>
                                <table class="table table-bordered text-center">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>รายการสินค้า</th>
                                            <th scope="col" width="16%">จำนวน</th>
                                            <th scope="col" width="16%">ราคา/หน่วย</th>
                                            <th scope="col" width="16%">ส่วนลด</th>
                                            <th>เพิ่มเติม</th>
                                        </tr>
                                    </thead>
                                    <tbody class="main">
                                    </tbody>
                                </table>
                                <div class="form-row">
                                    <div class="form-group">
                                        <input type="submit" name="quo_insert" class="btn btn-primary" value="บันทึกใบเสนอราคา">
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
                    url: "pagination_cus.php",
                    type: "POST",
                    // cache: false,
                    data: {
                        page_no: page
                    },
                    success: function(data) {
                        $("#info_cus").html(data);
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
                    url: "pagination_lot.php",
                    type: "POST",
                    // cache: false,
                    data: {
                        page_no: page
                    },
                    success: function(data) {
                        $("#info_lot").html(data);
                    }
                });
            }
            loadData1();
            $(document).on("click", ".pagination li a", function(e) {
                e.preventDefault();
                var page = $(this).attr("id");
                loadData1(page);
            });
        });
    </script>
</body>

</html>