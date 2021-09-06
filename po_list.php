<?php
session_start();
require('config/connect.php');
$db = new DB();
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if (isset($_SESSION['pos_permit'])) {
//     if (substr($_SESSION['pos_permit'], 0, 1) != 1) {
//         echo "<script type='text/javascript'>";
//         echo "alert('ไม่มีสิทธิ์ในการใช้งาน');";
//         echo "window.location = 'index.php'; ";
//         echo "</script>";
//     }
// }
/// เช็คค่าเลขหน้าที่ส่งว่ามีค่าหรือไม่มีค่า ///
if (isset($_GET['page']) && $_GET['page'] != "") {
    $page = $_GET['page'];
} else {
    $page = 1;
}
/// ประกาศ ให้แสดงกี่แถว เริ่มตั้งแต่ หน้าก่อน หน้าถัดไป ///
$limit = 5;
$start    =  ($page - 1) * $limit;
$previous =  $page - 1;
$next     =  $page + 1;
/// ทำจำนวนข้อมูลกี่ข้อมูล ///
$txtSQL = "SELECT count(po_id) as total FROM po_purchase";
$result = $db->query($txtSQL);
$fetch_rec  = mysqli_fetch_array($result);
$record  = $fetch_rec['total'];
$total_pages   = ceil($record / $limit);
/// แสดงข้อมูลใบสั่งซื้อ ///
$txtSQL = "SELECT po_id,po_start,po_end,po_status,emp_nic FROM po_purchase
           INNER JOIN employee ON (po_emp=emp_id) LIMIT $start, $limit";
$result_show = $db->query($txtSQL);
/// อนุมัติใบสั่งซื้อ ///
if (isset($_POST['po_app'])) {
    $po_id = $_POST['po_id'];
    $arr = array(
        "po_status" => 1
    );
    $where_condition = array(
        "po_id" => $po_id
    );
    $update = $db->update("po_purchase", $arr, $where_condition);
    if ($update) {
        echo "<script type='text/javascript'>";
        echo "alert('อนุมัติใบสั่งซื้อสำเร็จ');";
        echo "window.location = 'po_list.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('อนุมัติใบสั่งซื้อไม่สำเร็จ');";
        echo "</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('includes/head.php') ?>

<body>
    <?php include('includes/sidebar.php') ?>
    <div class="container-fluid pt-3">
        <div class="row">
            <div class="col-md-10 col-lg-10 col-xl-10 ml-auto">
                <div class="card border-0 mt-5">
                    <div class="card-body d-sm-flex justify-content-between">
                        <h2>ข้อมูลใบสั่งซื้อสินค้า</h2>
                        <form class="d-sm-flex justify-content-center" name="Search" id="Search">
                            <input type="search" name="txtsearch" id="txtsearch" autocomplete="off" placeholder="ค้นหา" class="form-control">
                            <input type="submit" name="in_search" id="in_search" class="btn btn-primary ml-2 mb-2" value="ค้นหา">
                        </form>
                    </div>
                </div>
                <div class="card mt-3">
                    <a href="po_form.php" class="btn btn-success mt-3 ml-auto mr-4">สร้างใบสั่งซื้อสินค้า</a>
                    <div class="card-body d-sm-flex justify-content-between">
                        <table class="table table-bordered ">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align:center">ลำดับ</th>
                                    <th style="text-align:center">วันที่สั่งซื้อ</th>
                                    <th style="text-align:center">ชื่อผู้สั่งซื้อ</th>
                                    <th style="text-align:center">สถานะ</th>
                                    <th style="text-align:center">เพิ่มเติม</th>
                                </tr>
                            </thead>
                            <?php foreach ($result_show as $data) { ?>
                                <tr>
                                    <th style="text-align:center"><?= $data['po_id']; ?></th>
                                    <th style="text-align:center"><?= $data['po_start']; ?></th>
                                    <th style="text-align:center"><?= $data['emp_nic']; ?></th>
                                    <th style="text-align:center"><?php if ($data['po_status'] == "1") {
                                                                        echo 'สั่งซื้อสินค้าได้';
                                                                    }?></th>
                                    <th style="text-align:center">
                                        <a href="po_pdf.php?po_id=<?= $data['po_id']; ?>" class="btn btn-secondary" target="_blank">พิมพ์ใบสั่งซื้อสินค้า</a>
                                    </th>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                    <nav aria-label="Page navigation example" class="mx-3">
                        <ul class="pagination">
                            <li class="page-item" <?php if ($page <= 1) {
                                                        echo "class='disabled'";
                                                    } ?>>
                                <a class="page-link text-dark" <?php if ($page > 1) {
                                                                    echo "href='?page=$previous'";
                                                                } ?>>Previous</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                <li class="page-item"><a class="page-link text-dark" href="po_list.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                            <?php } ?>
                            <li class="page-item" <?php if ($page >= $total_pages) {
                                                        echo "class='disabled'";
                                                    } ?>>
                                <a class="page-link text-dark" <?php if ($page < $total_pages) {
                                                                    echo "href='?page_no=$next'";
                                                                } ?>>Next</a>
                            </li>
                            <li class="ml-auto">
                                <h5>ข้อมูลใบสั่งซื้อทั้งหมด <?= $record . " รายการ " ?> </้h5>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <?php include('includes/script.php') ?>
    <script>
        $(function() {
            // $('#in_search').click(function(e) {
            //     e.preventDefault();
            //     var txt = $('#txtsearch').val();
            //     $.ajax({
            //         type: "post",
            //         url: "pdq_search.php",
            //         data: {
            //             query: txt
            //         },
            //         success: function(data) {
            //             /// ข้อมูลไม่เป็น 0(ค่าว่าง) ///
            //             if (data.length != 0) {
            //                 $('table tbody').html(data);
            //             } else {
            //                 alert('ไม่พบข้อมูลที่ค้นหา');
            //             }
            //         }
            //     });
            // });
        });
    </script>
</body>

</html>