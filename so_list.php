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
$txtSQL = "SELECT count(so_id) as total FROM sales_order";
$result = $db->query($txtSQL);
$fetch_rec  = mysqli_fetch_assoc($result);
$record  = $fetch_rec['total'];
$total_pages   = ceil($record / $limit);
/// แสดงข้อมูลใบเสนอราคา ///
$txtSQL = "SELECT so_id,so_date,cus_name,so_type FROM sales_order
INNER JOIN sales_order_list ON (so_id=sol_id)
INNER JOIN quotation_cost as qc1 ON (order_quo=qc1.quoc_order)
INNER JOIN quotation_cost ON (id_quo=qc1.quoc_id)
INNER JOIN quotation_list as ql1 ON (qc1.quoc_order=ql1.quol_order)
INNER JOIN quotation_list ON (qc1.quoc_id=ql1.quol_id)
INNER JOIN quotation ON (ql1.quol_id=quo_id)
INNER JOIN customer ON (quo_cus=cus_id)
GROUP BY so_id";
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
                        <h2>ข้อมูลใบสั่งขายสินค้า</h2>
                        <form class="d-sm-flex justify-content-center" name="Search" id="Search">
                            <input type="search" name="txtsearch" id="txtsearch" autocomplete="off" placeholder="ค้นหา" class="form-control">
                            <input type="submit" name="in_search" id="in_search" class="btn btn-primary ml-2 mb-2" value="ค้นหา">
                        </form>
                    </div>
                </div>
                <div class="card mt-3">
                    <a href="so_form.php" class="btn btn-success mt-3 ml-auto mr-4">สร้างใบสั่งขายสินค้า</a>
                    <div class="card-body d-sm-flex justify-content-between">
                        <table class="table table-bordered ">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align:center">ลำดับ</th>
                                    <th style="text-align:center">วันที่ขาย</th>
                                    <th style="text-align:center">ชื่อลูกค้า</th>
                                    <th style="text-align:center">ประเภทชำระเงิน</th>
                                    <th style="text-align:center">เพิ่มเติม</th>
                                </tr>
                            </thead>
                            <?php foreach ($result_show as $data) { ?>
                                <tr>
                                    <th style="text-align:center"><?=$data['so_id']?></th>
                                    <th style="text-align:center"><?=$data['so_date']?></th>
                                    <th style="text-align:center"><?=$data['cus_name']?></th>
                                    <th style="text-align:center"><?=$data['so_type']?></th>
                                    <th style="text-align:center">
                                        <a href="so_pdf.php?so_id=<?= $data['so_id']; ?>" class="btn btn-secondary" target="_blank">พิมพ์ใบเสร็จ</a>
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
                                <li class="page-item"><a class="page-link text-dark" href="so_list.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                            <?php } ?>
                            <li class="page-item" <?php if ($page >= $total_pages) {
                                                        echo "class='disabled'";
                                                    } ?>>
                                <a class="page-link text-dark" <?php if ($page < $total_pages) {
                                                                    echo "href='?page_no=$next'";
                                                                } ?>>Next</a>
                            </li>
                            <li class="ml-auto">
                                <h5>ข้อมูลใบเสนอราคาทั้งหมด <?= $record . " รายการ " ?> </้h5>
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