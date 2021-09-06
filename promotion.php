<?php
session_start();
require('config/connect.php');
$db = new DB();
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if (isset($_SESSION['pos_permit'])) {
//     if (substr($_SESSION['pos_permit'], 10, 1) != 1) {
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
$txtSQL = "SELECT count(promo_id) total FROM promotion where status_promo = 1";
$result = $db->query($txtSQL);
$fetch_rec  = mysqli_fetch_array($result);
$record  = $fetch_rec['total'];
$total_pages   = ceil($record / $limit);
/// แสดงตารางโปรโมชั่น ///
$txtSQL = "SELECT promo_id,promo_name,promo_start,promo_end,count FROM promotion where status_promo = 1 GROUP BY promotion.promo_id LIMIT $start, $limit";
$query_table = $db->query($txtSQL);
/// ลบข้อมูลโปรโมชั่น ///
if (isset($_GET['promo_id'])) {
    $promo_id = $_GET['promo_id'];
    $txtSQL = "UPDATE promotion SET status_promo = 0 WHERE promo_id = '$promo_id'";
    $query_delete = $db->query($txtSQL);
    if ($query_delete) {
        echo "<script type='text/javascript'>";
        echo "alert('ลบข้อมูลสำเร็จ');";
        echo "window.location = 'promotion.php'; ";
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
                            <h2>ข้อมูลโปรโมชั่น</h2>
                            <form class="d-sm-flex justify-content-center" name="Search" id="Search">
                                <!-- Default input -->
                                <input type="search" name="txtsearch" id="txtsearch" placeholder="ค้นหา" class="form-control" autocomplete="off">
                                <input type="submit" name="in_search" id="in_search" class="btn btn-primary ml-2 mb-2" value="ค้นหา">
                            </form>
                        </div>
                    </div>
                    <form action="promotion.php" method="post" class="form-horizontal">
                        <div class="card border-0 mt-3">
                            <div class="col-md-4 mt-3 ml-1">
                                <a href="promotion_form.php" class="btn btn-success">เพิ่มข้อมูลโปรโมชั่น</a>
                            </div>
                            <div class="card-body d-sm-flex justify-content-between table-responsive-md">
                                <table class="table table-bordered" id="table-data">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="text-align:center">ลำดับ</th>
                                            <th style="text-align:center">ชื่อโปรโมชั่น</th>
                                            <th style="text-align:center">ช่วงเวลาของโปรโมชั่น</th>
                                            <th style="text-align:center">ส่วนลด%</th>
                                            <th style="text-align:center">เพิ่มเติม</th>
                                        </tr>
                                    </thead>
                                    <?php foreach ($query_table as $data) { ?>
                                        <tr>
                                            <th style="text-align:center"><?php echo $data['promo_id'] ?></th>
                                            <th style="text-align:center"><?php echo $data['promo_name'] ?></th>
                                            <?php 
                                                $Date     = date('Y-m-d');
                                                $Date     = date('Y-m-d', strtotime($Date));
                                                $End_date = date('Y-m-d', strtotime($data['promo_end']));
                                                if($Date > $End_date){
                                                    $arr = array(
                                                        "status_promo" => 0
                                                    );
                                                    $where_condition = array(
                                                        "promo_id" => $data['promo_id']
                                                    );
                                                    $update = $db->update("promotion",$arr,$where_condition);
                                                }
                                            ?>
                                            <th style="text-align:center"><?php echo $data['promo_start'].' - '.$data['promo_end']?></th>
                                            <th style="text-align:center"><?php echo $data['count'] ?></th>
                                            <th style="text-align:center">
                                                <a href="promotion_edit.php?promo_id=<?php echo $data['promo_id'] ?>" class="btn btn-secondary">แก้ไข</a>
                                                <a href="?promo_id=<?php echo $data['promo_id'] ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
                                            </th>
                                        </tr>
                            </div>
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
                                        <li class="page-item"><a class="page-link text-dark" href="promotion.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                                    <?php } ?>
                                    <li class="page-item" <?php if ($page >= $total_pages) {
                                                                echo "class='disabled'";
                                                            } ?>>
                                        <a class="page-link text-dark" <?php if ($page < $total_pages) {
                                                                            echo "href='?page_no=$next'";
                                                                        } ?>>Next</a>
                                    </li>
                                    <li class="ml-auto">
                                        <h5>ข้อมูลโปรโมชั่นทั้งหมด <?= $record . " รายการ " ?> </้h5>
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
            $('#Search').submit(function(event) {
                event.preventDefault();
                var txt = $('#txtsearch').val();
                $.ajax({
                    type: "post",
                    url: "promo_search.php",
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