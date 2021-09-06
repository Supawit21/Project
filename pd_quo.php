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
$txtSQL = "SELECT count(pdq_id) as total FROM pd_quotation";
$result = $db->query($txtSQL);
$fetch_rec  = mysqli_fetch_assoc($result);
$record  = $fetch_rec['total'];
$total_pages   = ceil($record / $limit);
///  แสดงหัวใบเสนอสินค้า ///
$txtSQL = "SELECT pdq_id,pdq_start,pdq_end,pdq_status,emp_nic FROM pd_quotation
           INNER JOIN employee ON (pdq_emp=emp_id) LIMIT $start, $limit";
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
                        <h2>ข้อมูลใบเสนอสินค้า</h2>
                        <form class="d-sm-flex justify-content-center" name="Search" id="Search">
                            <input type="search" name="txtsearch" id="txtsearch" autocomplete="off" placeholder="ค้นหา" class="form-control">
                            <input type="submit" name="in_search" id="in_search" class="btn btn-primary ml-2 mb-2" value="ค้นหา">
                        </form>
                    </div>
                </div>
                <div class="card mt-3">
                    <a href="pd_form.php" class="btn btn-success mt-3 ml-auto mr-4">สร้างใบเสนอสินค้า</a>
                    <div class="card-body d-sm-flex justify-content-between">
                        <table class="table table-bordered ">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align:center">ลำดับ</th>
                                    <th style="text-align:center">วันที่เสนอ</th>
                                    <th style="text-align:center">ชื่อผู้เสนอ</th>
                                    <th style="text-align:center">สถานะ</th>
                                    <th style="text-align:center">เพิ่มเติม</th>
                                </tr>
                            </thead>
                            <?php foreach ($result_show as $data) { ?>
                                <tr>
                                    <th style="text-align:center"><?= $data['pdq_id']; ?></th>
                                    <th style="text-align:center"><?= $data['pdq_start']; ?></th>
                                    <th style="text-align:center"><?= $data['emp_nic']; ?></th>
                                    </th>
                                    <th style="text-align:center"><?php if ($data['pdq_status'] == "0") {
                                                                        echo 'ยังไม่อนุมัติ';
                                                                    } else if ($data['pdq_status'] == "1") {
                                                                        echo 'อนุมัติ';
                                                                    } ?></th>
                                    </th>
                                    <th style="text-align:center">
                                        <?php if ($data['pdq_status'] == "1") { ?>
                                            <a href="#show<?= $data['pdq_id']; ?>" class="btn btn-info shw" data-toggle="modal" data-id="<?= $data['pdq_id']; ?>">ดูใบเสนอสินค้า</a>
                                            <div class="modal fade bd-example-modal-lg" id="show<?= $data['pdq_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h2 class="modal-title" id="exampleModalLongTitle">ใบเสนอสินค้า<h3 class="mt-2">(<?= $data['pdq_id'] ?>)</h3>
                                                            </h2>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div id="table-responsive">
                                                                <table class="table table-bordered text-center" id="pdq_id">
                                                                    <thead class="thead-dark">
                                                                        <tr>
                                                                            <th>สินค้า</th>
                                                                            <th>บริษัทคู่ค้า</th>
                                                                            <th>จำนวน</th>
                                                                            <th>ราคา/หน่วย</th>
                                                                            <th>รวมเงิน</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <a href="app_quotation.php?pd_id=<?= $data['pdq_id']; ?>" class="btn btn-primary"                       
                                                                                                        <?php if (isset($_SESSION['pos_permit'])) {
                                                                                                              if (substr($_SESSION['pos_permit'], 13, 1) != 1) {
                                                                                                                    echo "hidden";
                                                                                                        }}?>>อนุมัติรายการใบเสนอสินค้า</a>
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
                                <li class="page-item"><a class="page-link text-dark" href="pd_quo.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                            <?php } ?>
                            <li class="page-item" <?php if ($page >= $total_pages) {
                                                        echo "class='disabled'";
                                                    } ?>>
                                <a class="page-link text-dark" <?php if ($page < $total_pages) {
                                                                    echo "href='?page_no=$next'";
                                                                } ?>>Next</a>
                            </li>
                            <li class="ml-auto">
                                <h5>ข้อมูลใบเสนอสินค้าทั้งหมด <?= $record . " รายการ " ?> </้h5>
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
            $('#in_search').click(function(e) {
                e.preventDefault();
                var txt = $('#txtsearch').val();
                $.ajax({
                    type: "post",
                    url: "pdq_search.php",
                    data: {
                        query: txt
                    },
                    success: function(data) {
                        /// ข้อมูลไม่เป็น 0(ค่าว่าง) ///
                        if (data.length != 0) {
                            $('table tbody').html(data);
                        } else {
                            alert('ไม่พบข้อมูลที่ค้นหา');
                        }
                    }
                });
            });
            $('.shw').on('click', function(e) {
                e.preventDefault();
                let pdq = $(this).data('id');
                $.ajax({
                    type: "post",
                    url: "show_quo.php",
                    data: {
                        rs: pdq
                    },
                    success: function(data) {
                        $('#pdq_id tbody').html(data);
                    }
                });
            });
        });
    </script>
</body>

</html>