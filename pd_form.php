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
$txtSQL = "SELECT concat('PDQ',NVL(lpad(substr(max(pdq_id),4,5)+1,5,'0'),'00001')) as quo FROM pd_quotation";
$result = $db->query($txtSQL);
$fetch_id = mysqli_fetch_assoc($result);
/// บันทึกใบเสนอสินค้า ///
if (isset($_POST['pqd_insert'])) {
    $pdq_id    = $fetch_id['quo'];
    $pdq_start = $_POST['pdq_start'];
    $pdq_end   = $_POST['pdq_end'];
    $pdq_emp   = $_POST['pdq_id'];
    $pdl_count = $_POST['pdl_count'];
    $pdl_unit  = $_POST['pdl_unit'];
    $cpl_com   = $_POST['cpl_com'];
    $cpl_pro   = $_POST['cpl_pro'];
    $cpl_price = $_POST['cpl_price'];
    $arr1 = array(
        "pdq_id" => $pdq_id,
        "pdq_start" => $pdq_start,
        "pdq_end" => $pdq_end,
        "pdq_emp" => $pdq_emp
    );
    $insert1 = $db->insert("pd_quotation", $arr1);
    foreach ($pdl_count as $key => $count_odr) {
        $count = $key + 1;
        $arr2 = array(
            "pdl_order" => $count,
            "pdl_count" => $count_odr,
            "pdl_unit"  => $pdl_unit[$key],
            "pdl_id" => $pdq_id
        );
        $insert2 = $db->insert("pdl_quotation", $arr2);
    }
    $i = 0;
    $w = array();
    $s = array();
    foreach ($cpl_pro as $key => $data) {
        if (!isset($w[$data])) {
            $w[$data] = array();
            $i++;
            $s[$key] = $i;
        } else {
            $s[$key] = $i;
        }
        $arr3 = array(
            "cpl_id" => $pdq_id,
            "cpl_order" => $s[$key],
            "cpl_pro" => $data,
            "cpl_com" => $cpl_com[$key],
            "cpl_price" => $cpl_price[$key]
        );
        $insert3 = $db->insert("proposed_cpl", $arr3);
        if ($insert3) {
            echo "<script type='text/javascript'>";
            echo "alert('เพิ่มข้อมูลสำเร็จ');";
            echo "window.location = 'pd_quo.php'; ";
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
                        <div class="card-body">
                            <h2>ใบเสนอสินค้า</h2>
                        </div>
                    </div>
                    <form name="pd_form" method="post">
                        <div class="card border-0  mt-3">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-6 mt-auto">
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModalLong">เลือกสินค้าที่เสนอ</button>
                                        <div class="modal fade bd-example-modal-lg" id="exampleModalLong<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" style="z-index: 9999">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h2 class="modal-title" id="exampleModalLongTitle">สินค้าที่ต่ำกว่าจุดสั่งซื้อ/สินค้าทั้งหมด</h2>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body" id="tab">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2 ml-auto">
                                        <label>รหัสใบเสนอสินค้า</label>
                                        <input type="text" name="pdq_id" value="<?= $fetch_id['quo'] ?>" class="form-control" readonly>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>วันที่เสนอ</label>
                                        <input type="date" name="pdq_start" class="form-control" value="<?= date('Y-m-d'); ?>" readonly>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-2 ml-auto">
                                        <label>ชื่อผู้เสนอ</label>
                                        <input type="text" name="pdq_name" class="form-control" value="<?= $txt_nic ?>" readonly>
                                        <input type="hidden" name="pdq_id" value="<?= $txt_id ?>">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>วันที่ครบกำหนด</label>
                                        <input type="date" name="pdq_end" class="form-control">
                                    </div>
                                </div>
                                <table class="table table-bordered text-center">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>ชื่อบริษัทคู่ค้า</th>
                                            <th>ชื่อสินค้า</th>
                                            <th>ราคาต้นทุน</th>
                                            <!-- <th>เพิ่มเติม</th> -->
                                        </tr>
                                    </thead>
                                    <tbody class="main">
                                    </tbody>
                                </table>
                                <div class="form-row">
                                    <div class="form-group ml-auto">
                                        <input type="submit" name="pqd_insert" class="btn btn-primary" value="บันทึกใบเสนอสินค้า">
                                        <a href="pd_quo.php" type="button" class="btn btn-danger">ยกเลิกใบเสนอสินค้า</a>
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
                    url: "pagination_pro.php",
                    type: "POST",
                    // cache: false,
                    data: {
                        page_no: page
                    },
                    success: function(data) {
                        $("#tab").html(data);
                    }
                });
            }
            loadData();
            $(document).on("click", ".pagination li a", function(e) {
                e.preventDefault();
                var pageId = $(this).attr("id");
                loadData(pageId);
            });
        });
    </script>
</body>

</html>