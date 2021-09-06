<?php
session_start();
require('config/connect.php');
$db = new DB();
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if (isset($_SESSION['pos_permit'])) {
//     if (substr($_SESSION['pos_permit'], 8, 1) != 1) {
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
$start    = ($page - 1) * $limit;
$previous =  $page - 1;
$next     =  $page + 1;
/// ทำจำนวนข้อมูลกี่ข้อมูล ///
$txtSQL = "SELECT count(pro_id) total FROM product where status_pro = 1";
$result = $db->query($txtSQL);
$fetch_rec  = mysqli_fetch_array($result);
$record  = $fetch_rec['total'];
$total_pages   = ceil($record / $limit);
/// แสดงตารางสินค้า ///
$txtSQL = "SELECT pro_id,pro_img,pro_name,size_name,pro_price,pro_amount FROM product 
           INNER JOIN product_size ON (product.pro_size=product_size.size_id) WHERE status_pro = 1 ORDER BY pro_id LIMIT $start, $limit";
$result_product = $db->query($txtSQL);
/// ลบข้อมูลสินค้า ///
if (isset($_GET['pro_id'])) {
    $pro_id = $_GET['pro_id'];
    /// ลบชื่อภาพจาก database ///
    $txtSQL = "SELECT pro_img FROM product WHERE pro_id = '$pro_id'";
    $query_delete = $db->query($txtSQL);
    $row_delete  = mysqli_fetch_assoc($query_delete);
    /// ลบรูปจาก folder ///
    $dir = "image_pro/";
    $pic = $row_delete['pro_img'];
    unlink($dir . $pic);
    /// ลบข้อมูลโดยการเปลี่ยนสถานะ จาก 1 เป็น 0 ///
    $txtSQL = "UPDATE product SET status_pro = 0 WHERE pro_id = '$pro_id'";
    $result_up = $db->query($txtSQL);
    if ($result_up) {
        echo "<script type='text/javascript'>";
        echo "alert('ลบข้อมูลสำเร็จ');";
        echo "window.location = 'product.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('ลบข้อมูลไม่สำเร็จ');";
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
                            <h2>ข้อมูลสินค้า</h2>
                            <form class="d-sm-flex justify-content-center" name="Search" id="Search">
                                <!-- Default input -->
                                <input type="search" name="txtsearch" id="txtsearch" autocomplete="off" placeholder="ค้นหา" class="form-control">
                                <input type="submit" name="in_search" id="in_search" class="btn btn-primary ml-2 mb-2" value="ค้นหา">
                            </form>
                        </div>
                    </div>
                    <form action="" method="post" class="form-horizontal">
                        <div class="card border-0 mt-3">
                            <div class="col-md-4 mt-3 ml-1">
                                <a href="product_form.php" class="btn btn-success">เพิ่มข้อมูลสินค้า</a>
                            </div>
                            <div class="card-body d-sm-flex justify-content-between table-responsive-md">
                                <table class="table table-bordered" id="table-data">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="text-align:center">ลำดับ</th>
                                            <th style="text-align:center">รูปสินค้า</th>
                                            <th style="text-align:center">ชื่อสินค้า</th>
                                            <th style="text-align:center">ราคาขาย</th>
                                            <th style="text-align:center">เพิ่มเติม</th>
                                        </tr>
                                    </thead>
                                    <?php foreach ($result_product as $data) { ?>
                                        <tr>
                                            <th style="text-align:center"><?php echo $data['pro_id'] ?></th>
                                            <th style="text-align:center"><img src="image_pro/<?php echo $data['pro_img'] ?>" alt="" width="100px" height="100px"></th>
                                            <th style="text-align:center"><?php echo $data['pro_name'] . ' ' . $data['size_name'] ?></th>
                                            <th style="text-align:center"><?php echo number_format($data['pro_price']); ?></th>
                                            <th style="text-align:center">
                                                <a href="#show<?= $data['pro_id']; ?>" class="btn btn-info shw" data-toggle="modal" data-id="<?= $data['pro_id']; ?>">ดูรายละเอียดสินค้า</a>
                                                <div class="modal fade bd-example-modal-lg" id="show<?= $data['pro_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h2 class="modal-title" id="exampleModalLongTitle">รายละเอียดสินค้า</h2>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                            <table class="table table-bordered" id="steel_show">
                                                            <tbody>
                                                            </tbody>
                                                            </table>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <a href="product_edit.php?id=<?php echo $data['pro_id'] ?>" class="btn btn-secondary">แก้ไข</a>
                                                <a href="?pro_id=<?php echo $data['pro_id'] ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
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
                                        <li class="page-item"><a class="page-link text-dark" href="product.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                                    <?php } ?>
                                    <li class="page-item" <?php if ($page >= $total_pages) {
                                                                echo "class='disabled'";
                                                            } ?>>
                                        <a class="page-link text-dark" <?php if ($page < $total_pages) {
                                                                            echo "href='?page_no=$next'";
                                                                        } ?>>Next</a>
                                    </li>
                                    <li class="ml-auto">
                                        <h5>ข้อมูลสินค้าทั้งหมด <?= $record . " รายการ " ?> </้h5>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/script.php') ?>
    <script>
        $(document).ready(function() {
            ///ดักปุ่ม submit ไม่ให้รีหน้า///
            $('#Search').submit(function(event) {
                event.preventDefault();
                /// รับค่าจาก Form ///
                var txt = $('#txtsearch').val();

                $.ajax({
                    type: "post",
                    url: "pro_search.php",
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
                let pro = $(this).data('id');
                $.ajax({
                    type: "post",
                    url: "show_det.php",
                    data: {
                        rs: pro
                    },
                    success: function(data) {
                        $('#steel_show tbody').html(data);
                    }
                });
            });
        });
    </script>
</body>

</html>