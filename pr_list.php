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
$txtSQL = "SELECT count(pr_id) as total FROM pr_receipt";
$result = $db->query($txtSQL);
$fetch_rec  = mysqli_fetch_array($result);
$record  = $fetch_rec['total'];
$total_pages   = ceil($record / $limit);
/// แสดงข้อมูลใบสั่งซื้อ ///
$txtSQL = "SELECT pr_id,emp_nic,com_name,pr_status FROM pr_receipt
           INNER JOIN employee     ON (pr_emp=emp_id)
           INNER JOIN prl_receipt  ON (pr_id=prl_id)
           INNER JOIN pol_purchase ON (prl_po=pol_id)
           INNER JOIN proposed_cpl ON (pol_com=cpl_com)
           INNER JOIN cost_price   ON (cpl_com=cp_com)
           INNER JOIN company      ON (cp_com=com_id)
           GROUP BY pr_id,emp_nic,com_name,pr_status LIMIT $start, $limit";
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
                        <h2>ข้อมูลใบรับสินค้า</h2>
                        <form class="d-sm-flex justify-content-center" name="Search" id="Search">
                            <input type="search" name="txtsearch" id="txtsearch" autocomplete="off" placeholder="ค้นหา" class="form-control">
                            <input type="submit" name="in_search" id="in_search" class="btn btn-primary ml-2 mb-2" value="ค้นหา">
                        </form>
                    </div>
                </div>
                <div class="card mt-3">
                    <a href="pr_form.php" class="btn btn-success mt-3 ml-auto mr-4">สร้างใบรับสินค้า</a>
                    <div class="card-body d-sm-flex justify-content-between">
                        <table class="table table-bordered ">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align:center">ลำดับ</th>
                                    <th style="text-align:center">ชื่อผู้รับสินค้า</th>
                                    <th style="text-align:center">ชื่อบริษัท</th>
                                    <th style="text-align:center">สถานะ</th>
                                    <th style="text-align:center">เพิ่มเติม</th>
                                </tr>
                            </thead>
                            <?php foreach ($result_show as $data) { ?>
                                <tr>
                                    <?php if ($data['pr_status'] == "1") { ?>
                                        <th style="text-align:center"><?= $data['pr_id']; ?></th>
                                        <th style="text-align:center"><?= $data['emp_nic']; ?></th>
                                        <th style="text-align:center"><?= $data['com_name']; ?></th>
                                        <th style="text-align:center">รับสินค้าครบแล้ว</th>
                                        <th style="text-align:center">
                                        <a href="#show<?= $data['pr_id']; ?>" class="btn btn-info shw" data-toggle="modal" data-id="<?= $data['pr_id']; ?><?= $data['pr_status']; ?>">รายละเอียดการรับสินค้า</a>
                                            <div class="modal fade bd-example-modal-lg" id="show<?= $data['pr_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h2 class="modal-title" id="exampleModalLongTitle">ใบรับสินค้าสินค้า<h3 class="mt-2">(<?= $data['pr_id'] ?>)</h3>
                                                            </h2>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form method="post" class="form-horizontal">
                                                            <div class="modal-body">
                                                                <div id="table-responsive">
                                                                    <table class="table table-bordered text-center" id="pr_id">
                                                                        <thead class="thead-dark">
                                                                            <tr>
                                                                                <th>เลขล็อต</th>
                                                                                <th>ลำดับ</th>
                                                                                <th>สินค้า</th>
                                                                                <th>จำนวน</th>
                                                                                <th>ราคา/หน่วย</th>
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
                                    <?php } else { ?>
                                        <th style="text-align:center"><?= $data['pr_id']; ?></th>
                                        <th style="text-align:center"><?= $data['emp_nic']; ?></th>
                                        <th style="text-align:center"><?= $data['com_name']; ?></th>
                                        <th style="text-align:center" class="text-danger">รับสินค้ายังไม่ครบ</th>
                                        <th style="text-align:center">
                                            <a href="#show<?= $data['pr_id']; ?>" class="btn btn-info shw" data-toggle="modal" data-id="<?= $data['pr_id']; ?><?= $data['pr_status']; ?>">รายละเอียดการรับสินค้า</a>
                                            <div class="modal fade bd-example-modal-lg" id="show<?= $data['pr_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h2 class="modal-title" id="exampleModalLongTitle">ใบรับสินค้าสินค้า<h3 class="mt-2">(<?= $data['pr_id'] ?>)</h3>
                                                            </h2>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form method="post" class="form-horizontal">
                                                            <div class="modal-body">
                                                                <div id="table-responsive">
                                                                    <table class="table table-bordered text-center" id="pr_id">
                                                                        <thead class="thead-dark">
                                                                            <tr>
                                                                                <th>เลขล็อต</th>
                                                                                <th>ลำดับ</th>
                                                                                <th>สินค้า</th>
                                                                                <th>จำนวน</th>
                                                                                <th>จำนวนที่เหลือ</th>
                                                                                <th>ราคา/หน่วย</th>
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
                                        <?php } ?>
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
                                <li class="page-item"><a class="page-link text-dark" href="pr_list.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                            <?php } ?>
                            <li class="page-item" <?php if ($page >= $total_pages) {
                                                        echo "class='disabled'";
                                                    } ?>>
                                <a class="page-link text-dark" <?php if ($page < $total_pages) {
                                                                    echo "href='?page_no=$next'";
                                                                } ?>>Next</a>
                            </li>
                            <li class="ml-auto">
                                <h5>ข้อมูลใบรับสินค้าทั้งหมด <?= $record . " รายการ " ?> </้h5>
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
                let pr = $(this).data('id');
                $.ajax({
                    type: "post",
                    url: "show_pr.php",
                    data: {
                        rs: pr
                    },
                    success: function(data) {
                        $('#pr_id tbody').html(data);
                    }
                });
            });
        });
    </script>
</body>

</html>