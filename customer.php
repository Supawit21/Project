<?php
session_start();
require('config/connect.php');
$db = new DB();
/// เช็คสิทธิ์การเข้าใช้งาน ///
if(isset($_SESSION['pos_permit']))
{
    if(substr($_SESSION['pos_permit'],3,1) != 1){
        echo "<script type='text/javascript'>";
        echo "alert('ไม่มีสิทธิ์ในการใช้งาน');";
        echo "window.location = 'index.php'; ";
        echo "</script>";
    } 
}
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
$txtSQL = "SELECT count(cus_id) total FROM customer where status_cus = 1";
$result = $db->query($txtSQL);
$fetch_rec  = mysqli_fetch_array($result);
$record  = $fetch_rec ['total'];
$total_pages   = ceil($record / $limit);
///  แสดงตารางลูกค้า ///
$txtSQL  = "SELECT customer.cus_id,cus_title,cus_name,cus_surname,cus_email,cus_add,cus_tel FROM customer 
INNER JOIN customer_tel ON(customer.cus_id = customer_tel.cus_id) where status_cus = 1 GROUP BY customer.cus_id LIMIT $start, $limit";
$result = $db->query($txtSQL);
/// ลบข้อมูลลูกค้า ///
if (isset($_GET['cus_id'])) {
    $cus_id = $_GET['cus_id'];
    $txtSQL = "UPDATE customer SET status_cus = 0 WHERE cus_id = '$cus_id'";
    $result = $db->query($txtSQL);
    if ($result) {
        echo "<script type='text/javascript'>";
        echo "alert('ลบข้อมูลสำเร็จ');";
        echo "window.location = 'customer.php'; ";
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
                            <h2>ข้อมูลลูกค้า</h2>
                            <form class="d-sm-flex justify-content-center" name="Search" id="Search">
                                <!-- Default input -->
                                <input type="search" name="txtsearch" id="txtsearch" autocomplete="off" placeholder="ค้นหา" class="form-control">
                                <input type="submit" name="in_search" id="in_search" class="btn btn-primary ml-2 mb-2" value="ค้นหา">
                            </form>
                        </div>
                    </div>
                    <form action="customer.php" method="post" class="form-horizontal" >
                        <div class="card border-0 mt-3">
                            <div class="col-md-4 mt-3 ml-1">
                                <a href="customer_form.php" class="btn btn-success">เพิ่มข้อมูลลูกค้า</a>
                            </div>
                            <div class="card-body d-sm-flex justify-content-between table-responsive-md">
                                <table class="table table-bordered" id="table-data">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="text-align:center">ลำดับ</th>
                                            <th style="text-align:center">ชื่อ-นามสกุล</th>
                                            <th style="text-align:center">อีเมล</th>
                                            <th style="text-align:center">ที่อยู่</th>
                                            <th style="text-align:center">เบอร์โทร</th>
                                            <th style="text-align:center">เพิ่มเติม</th>
                                        </tr>
                                    </thead>
                                    <?php foreach ($result as $data) { ?>
                                        <tr>
                                            <th style="text-align:center"><?php echo $data['cus_id'] ?></th>
                                            <th style="text-align:center"><?php echo $data['cus_title'].' '.$data['cus_name'].' '.$data['cus_surname']?></th>
                                            <th style="text-align:center"><?php echo $data['cus_email'] ?></th>
                                            <th style="text-align:center"><?php echo $data['cus_add'] ?></th>
                                            <th style="text-align:center"><?php echo $data['cus_tel'] ?></th>
                                            <th style="text-align:center">
                                                <a href="customer_edit.php?id=<?php echo $data['cus_id'] ?>" class="btn btn-secondary">แก้ไข</a>
                                                <a href="?cus_id=<?php echo $data['cus_id'] ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
                                            </th>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>
                            <nav aria-label="Page navigation example" class="mx-3">
                            <ul class="pagination">
                                <li class="page-item" <?php if($page <= 1){echo "class='disabled'";}?>>
                                    <a class="page-link text-dark" <?php if($page > 1){ echo "href='?page=$previous'";}?>>Previous</a>
                                </li>
                                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                    <li class="page-item"><a class="page-link text-dark" href="customer.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                                <?php } ?>
                                <li class="page-item" <?php if($page >= $total_pages){ echo "class='disabled'"; } ?>>
                                    <a class="page-link text-dark" <?php if($page < $total_pages) { echo "href='?page_no=$next'"; } ?>>Next</a>
                                </li>
                                <li class="ml-auto">
                                    <h5>ข้อมูลลูกค้าทั้งหมด <?= $record . " รายการ " ?> </้h5>
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
                    url: "cus_search.php",
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
        });
    </script>
</body>

</html>