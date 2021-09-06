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
$txtSQL = "SELECT count(dl_id) as total FROM delivery_note";
$result = $db->query($txtSQL);
$fetch_rec  = mysqli_fetch_assoc($result);
$record  = $fetch_rec['total'];
$total_pages   = ceil($record / $limit);
/// แสดงข้อมูลใบเสนอราคา ///
$txtSQL = "SELECT dl_id,dl_date,emp_name,dl_status,cus_name FROM delivery_note
INNER JOIN employee ON (dl_emp=emp_id)
INNER JOIN delivery_list ON (dl_id=del_id)
INNER JOIN quotation_cost as qc1 ON (del_qor=qc1.quoc_order)
INNER JOIN quotation_cost ON (del_qoi=qc1.quoc_id)
-- INNER JOIN sales_order_list as s1 ON (qc1.quoc_id=s1.id_quo)
-- INNER JOIN sales_order_list ON (qc1.quoc_order=s1.order_quo)
-- INNER JOIN sales_order ON (s1.sol_id=so_id)
INNER JOIN quotation_list as ql1 ON (qc1.quoc_order=ql1.quol_order)
INNER JOIN quotation_list ON (qc1.quoc_id=ql1.quol_id)
INNER JOIN quotation      ON (ql1.quol_id=quo_id)
INNER JOIN customer       ON (quo_cus=cus_id)
GROUP BY dl_id";
$result_show = $db->query($txtSQL);
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
                        <h2>ข้อมูลใบส่งของ</h2>
                        <form class="d-sm-flex justify-content-center" name="Search" id="Search">
                            <input type="search" name="txtsearch" id="txtsearch" autocomplete="off" placeholder="ค้นหา" class="form-control">
                            <input type="submit" name="in_search" id="in_search" class="btn btn-primary ml-2 mb-2" value="ค้นหา">
                        </form>
                    </div>
                </div>
                <div class="card mt-3">
                    <a href="de_form.php" class="btn btn-success mt-3 ml-auto mr-4">สร้างใบส่งของ</a>
                    <div class="card-body d-sm-flex justify-content-between">
                        <table class="table table-bordered ">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align:center">ลำดับ</th>
                                    <th style="text-align:center">วันที่ส่งของ</th>
                                    <th style="text-align:center">ชื่อลูกค้า</th>
                                    <th style="text-align:center">ชื่อผู้ออกใบส่งของ</th>
                                    <th style="text-align:center">สถานะ</th>
                                    <th style="text-align:center">เพิ่มเติม</th>
                                </tr>
                            </thead>
                            <?php foreach ($result_show as $data) { ?>
                                <?php if ($data['dl_status'] == "1") { ?>
                                    <tr>
                                        <th style="text-align:center"><?= $data['dl_id'] ?></th>
                                        <th style="text-align:center"><?= $data['dl_date'] ?></th>
                                        <th style="text-align:center"><?= $data['cus_name'] ?></th>
                                        <th style="text-align:center"><?= $data['emp_name'] ?></th>
                                        <th style="text-align:center">ส่งของครบแล้ว</th>
                                        <th style="text-align:center">
                                            <a href="dl_pdf.php?dl_id=<?= $data['dl_id']; ?>" class="btn btn-secondary" target="_blank">พิมพ์ใบส่งของ</a>
                                            <a href="#show<?= $data['dl_id']; ?>" class="btn btn-info shw" data-toggle="modal" data-id="<?= $data['dl_id']; ?><?= $data['dl_status']; ?>">รายละเอียดการส่งของ</a>
                                            <div class="modal fade bd-example-modal-lg" id="show<?= $data['dl_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h2 class="modal-title" id="exampleModalLongTitle">ใบส่งของ<h3 class="mt-2">(<?= $data['dl_id'] ?>)</h3>
                                                            </h2>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form method="post" class="form-horizontal">
                                                            <div class="modal-body">
                                                                <div id="table-responsive">
                                                                    <div class="dt mb-1 text-left">
                                                                        วันที่ส่งของ: <?= $data['dl_date'] ?>
                                                                    </div>
                                                                    <table class="table table-bordered text-center" id="dl_id">
                                                                        <thead class="thead-dark">
                                                                            <tr>
                                                                                <th>ชื่อสินค้า</th>
                                                                                <th>จำนวนที่ส่ง</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                <?php } else { ?>
                                    <tr>
                                        <th style="text-align:center"><?= $data['dl_id'] ?></th>
                                        <th style="text-align:center"><?= $data['dl_date'] ?></th>
                                        <th style="text-align:center"><?= $data['cus_name'] ?></th>
                                        <th style="text-align:center"><?= $data['emp_name'] ?></th>
                                        <th style="text-align:center" class="text-danger">ส่งของยังไม่ครบ</th>
                                        <th style="text-align:center">
                                            <a href="dl_pdf.php?dl_id=<?= $data['dl_id']; ?>" class="btn btn-secondary" target="_blank">พิมพ์ใบส่งของ</a>
                                            <a href="#show<?= $data['dl_id']; ?>" class="btn btn-info shw" data-toggle="modal" data-id="<?= $data['dl_id']; ?><?= $data['dl_status']; ?>">รายละเอียดการส่งของ</a>
                                            <div class="modal fade bd-example-modal-lg" id="show<?= $data['dl_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h2 class="modal-title" id="exampleModalLongTitle">ใบส่งของ<h3 class="mt-2">(<?= $data['dl_id'] ?>)</h3>
                                                            </h2>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form method="post" class="form-horizontal">
                                                            <div class="modal-body">
                                                                <div id="table-responsive">
                                                                    <div class="dt mb-1 text-left">
                                                                        วันที่ส่งของ: <?= $data['dl_date'] ?>
                                                                    </div>
                                                                    <table class="table table-bordered text-center" id="dl_id">
                                                                        <thead class="thead-dark">
                                                                            <tr>
                                                                                <th>ชื่อสินค้า</th>
                                                                                <th>จำนวนที่ส่ง</th>
                                                                                <th>จำนวนคงเหลือ</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                <?php } ?>
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
                                <li class="page-item"><a class="page-link text-dark" href="delivery.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                            <?php } ?>
                            <li class="page-item" <?php if ($page >= $total_pages) {
                                                        echo "class='disabled'";
                                                    } ?>>
                                <a class="page-link text-dark" <?php if ($page < $total_pages) {
                                                                    echo "href='?page_no=$next'";
                                                                } ?>>Next</a>
                            </li>
                            <li class="ml-auto">
                                <h5>ข้อมูลใบส่งของทั้งหมด <?= $record . " รายการ " ?> </้h5>
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
            $('.shw').on('click', function(e) {
                e.preventDefault();
                let dl = $(this).data('id');
                $.ajax({
                    type: "post",
                    url: "show_dl.php",
                    data: {
                        form: dl
                    },
                    success: function(data) {
                        $('#dl_id tbody').html(data);
                    }
                });
            });
        });
    </script>
</body>

</html>