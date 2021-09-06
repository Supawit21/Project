<?php
session_start();
require('config/connect.php');
$db = new DB();
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if(isset($_SESSION['pos_permit']))
// {
//     if(substr($_SESSION['pos_permit'],7,1) != 1){
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
$txtSQL  = "SELECT count(unit_id) as total FROM product_unit where status_unit = 1";
$result = $db->query($txtSQL);
$fetch_rec  = mysqli_fetch_array($result);
$record  = $fetch_rec['total'];
$total_pages   = ceil($record / $limit);
///  แสดงตารางหน่วยสินค้า ///
$txtSQL = "SELECT unit_id,unit_name FROM product_unit WHERE status_unit = 1 LIMIT $start, $limit";
$result_unit = $db->query($txtSQL);
/// สร้างรหัสรันเอง ///
$txtSQL = "SELECT concat('UNT',NVL(lpad(substr(max(unit_id),4,3)+1,3,'0'),'001')) as unit FROM product_unit";
$result = $db->query($txtSQL);
$fetch_id = mysqli_fetch_array($result);
/// บันทึกหน่วยสินค้า ///
if (isset($_POST['unit_insert'])) {
    $unit_id   = $fetch_id['unit'];
    $unit_name = mysqli_real_escape_string($db->conn, $_POST["unit_name"]);
    $arr = array(
        "unit_id" => $unit_id,
        "unit_name" => $unit_name
    );
    $insert = $db->insert("product_unit",$arr);
    if ($insert) {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มข้อมูลสำเร็จ');";
        echo "window.location = 'prounit.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มข้อมูลไม่สำเร็จ');";
        echo "</script>";
    }
}
/// แก้ไขหน่วยสินค้า ///
if (isset($_POST['unit_edit'])) {
    $unit_id   = mysqli_real_escape_string($db->conn, $_POST["unit_id"]);
    $unit_name = mysqli_real_escape_string($db->conn, $_POST["unit_name"]);
    $arr1 = array(
        "unit_name" => $unit_name
    );
    $where_condition = array(
        "unit_id" => $unit_id
    );
    $update = $db->update("product_unit",$arr1,$where_condition);
    if ($update) {
        echo "<script type='text/javascript'>";
        echo "alert('แก้ไขข้อมูลสำเร็จ');";
        echo "window.location = 'prounit.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('แก้ไขข้อมูลไม่สำเร็จ');";
        echo "</script>";
    }
}
/// ลบหน่วยสินค้า ///
if (isset($_GET['unit_id'])) {
    $size_id = $_GET['unit_id'];
    $txtSQL = "UPDATE product_unit SET status_unit = 0 WHERE unit_id = '$unit_id'";
    $result_del = $db->query($txtSQL);
    if ($result_del) {
        echo "<script type='text/javascript'>";
        echo "alert('ลบข้อมูลสำเร็จ');";
        echo "window.location = 'prounit.php'; ";
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
    <div class="container-fluid pt-3">
        <div class="row">
            <div class="col-md-10 ml-auto">
                <div class="card border-0 mt-5">
                    <div class="card-body d-sm-flex justify-content-between">
                        <h2>ข้อมูลหน่วยสินค้า</h2>
                        <form class="d-sm-flex justify-content-center" name="Search" id="Search">
                            <!-- Default input -->
                            <input type="search" name="txtsearch" id="txtsearch" autocomplete="off" placeholder="ค้นหา" class="form-control">
                            <input type="submit" name="in_search" id="in_search" class="btn btn-primary ml-2 mb-2" value="ค้นหา">
                        </form>
                    </div>
                </div>
                <form method="post" class="form-horizontal">
                    <div class="card mt-3">
                    <div class="col-md-4 mt-3 ml-1">
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModalLong">
                                เพิ่มข้อมูลหน่วยสินค้า
                            </button>
                            <!-- Modal เพิ่ม -->
                            <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" style="z-index: 9999">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h2 class="modal-title" id="exampleModalLongTitle">ข้อมูลหน่วยสินค้า</h2>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form method="post" class="form-horizontal" name="type_form" id="type_form">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                <label>ชื่อหน่วยสินค้า</label>
                                                <input type="text" name="unit_name" id="unit_name" class="form-control" placeholder="หน่วยสินค้า" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิกข้อมูล</button>
                                                <button type="submit" name="unit_insert" class="btn btn-primary">บันทึกข้อมูล</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body d-sm-flex justify-content-between">
                            <table class="table table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align:center">ลำดับ</th>
                                        <th style="text-align:center">ชื่อหน่วยสินค้า</th>
                                        <th style="text-align:center">เพิ่มเติม</th>
                                    </tr>
                                </thead>
                                <?php foreach ($result_unit as $data) { ?>
                                    <tr>
                                        <th style="text-align:center"><?php echo $data['unit_id'] ?></th>
                                        <th style="text-align:center"><?php echo $data['unit_name'] ?></th>
                                        <th style="text-align:center">
                                            <a href="#edit<?php echo $data['unit_id']; ?>" class="btn btn-secondary" data-toggle="modal">แก้ไข</a>
                                            <a href="?unit_id=<?php echo $data['unit_id']; ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
                                        </th>
                                    </tr>
                                    <!-- Modal เพิ่ม -->
                            <div class="modal fade" id="edit<?=$data['unit_id']?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" style="z-index: 9999">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h2 class="modal-title" id="exampleModalLongTitle">ข้อมูลหน่วยสินค้า</h2>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form method="post" class="form-horizontal" name="type_form" id="type_form">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                <label>ชื่อหน่วยสินค้า</label>
                                                <input type="text" name="unit_name" id="unit_name" class="form-control" placeholder="หน่วยสินค้า" autocomplete="off" value="<?=$data['unit_name']?>">
                                                <input type="hidden" name="unit_id" value="<?=$data['unit_id']?>">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิกข้อมูล</button>
                                                <button type="submit" name="unit_edit" class="btn btn-primary">บันทึกข้อมูล</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                                <?php } ?>
                            </table>
                        </div>
                        <nav aria-label="Page navigation example" class="mx-3">
                        <ul class="pagination">
                            <li class="page-item" <?php if ($page <= 1) {echo "class='disabled'";} ?>>
                                <a class="page-link text-dark" <?php if ($page > 1) {echo "href='?page=$previous'";} ?>>Previous</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                <li class="page-item"><a class="page-link text-dark" href="protype.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                            <?php } ?>
                            <li class="page-item" <?php if ($page >= $total_pages) {echo "class='disabled'";} ?>>
                                <a class="page-link text-dark" <?php if ($page < $total_pages) {echo "href='?page_no=$next'";} ?>>Next</a>
                            </li>
                            <li class="ml-auto">
                                <h5>ข้อมูลหน่วยสินค้าสินค้าทั้งหมด <?= $record . " รายการ " ?> </้h5>
                            </li>
                        </ul>
                    </nav>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
                    url: "prounit_search.php",
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