<?php
session_start();
require('config/connect.php');
$db = new DB();
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if (isset($_SESSION['pos_permit'])) {
//     if (substr($_SESSION['pos_permit'], 5, 1) != 1) {
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
$txtSQL = "SELECT count(type_id) as total FROM product_type where status_type = 1";
$result = $db->query($txtSQL);
$fetch_rec  = mysqli_fetch_array($result);
$record  = $fetch_rec['total'];
$total_pages   = ceil($record / $limit);
///  แสดงตารางประเภทสินค้า ///
$txtSQL = "SELECT type_id,type_name FROM product_type WHERE status_type = 1 LIMIT $start, $limit";
$result_type = $db->query($txtSQL);
/// สร้างรหัสรันเอง ///
$txtSQL = "SELECT concat('TYP',NVL(lpad(substr(max(type_id),4,3)+1,3,'0'),'001')) as type FROM product_type";
$result = $db->query($txtSQL);
$fetch_id = mysqli_fetch_array($result);
/// บันทึกข้อมูลประเภทสินค้า ///
if (isset($_POST['type_insert'])) {
    $type_id = $fetch_id['type'];
    $type_name = mysqli_real_escape_string($db->conn, $_POST["type_name"]);
    $arr = array(
        "type_id" => $type_id,
        "type_name" => $type_name
    );
    $insert = $db->insert("product_type", $arr);
    if ($insert) {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มข้อมูลสำเร็จ');";
        echo "window.location = 'protype.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มข้อมูลไม่สำเร็จ');";
        echo "</script>";
    }
}
/// แก้ไขข้อมูล ///
if (isset($_POST['type_edit'])) {
    $type_id   = mysqli_real_escape_string($db->conn, $_POST["type_id"]);
    $type_name = mysqli_real_escape_string($db->conn, $_POST["type_name"]);
    $arr = array(
        "type_name" => $type_name
    );
    $where_condition = array(
        "type_id" => $type_id
    );
    $update = $db->update("product_type", $arr, $where_condition);
    if ($update) {
        echo "<script type='text/javascript'>";
        echo "alert('แก้ไขข้อมูลสำเร็จ');";
        echo "window.location = 'protype.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('แก้ไขข้อมูลไม่สำเร็จ');";
        echo "</script>";
    }
}
/// ลบข้อมูลประเภทสินค้า ///
if (isset($_GET['type_id'])) {
    $type_id = $_GET['type_id'];
    $arr = array(
        "status_type" => 0
    );
    $where_condition = array(
        "type_id" => $type_id
    );
    $delete = $db->update("product_type", $arr, $where_condition);
    if ($delete) {
        echo "<script type='text/javascript'>";
        echo "alert('ลบข้อมูลสำเร็จ');";
        echo "window.location = 'protype.php'; ";
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
                        <h2>ข้อมูลประเภทสินค้า</h2>
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
                                เพิ่มข้อมูลประเภทสินค้า
                            </button>
                            <!-- Modal เพิ่ม -->
                            <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" style="z-index: 9999">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h2 class="modal-title" id="exampleModalLongTitle">ข้อมูลประเภทสินค้า</h2>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form method="post" class="form-horizontal" name="type_form" id="type_form">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>ชื่อประเภทสินค้า</label>
                                                    <div>
                                                        <input type="text" name="type_name" id="type_name" autocomplete="off" class="form-control" placeholder="ประเภทสินค้า">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิกข้อมูล</button>
                                                <button type="submit" name="type_insert" class="btn btn-primary">บันทึกข้อมูล</button>
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
                                        <th style="text-align:center">ชื่อประเภทสินค้า</th>
                                        <th style="text-align:center">เพิ่มเติม</th>
                                    </tr>
                                </thead>
                                <?php foreach ($result_type as $data) { ?>
                                    <tr>
                                        <th style="text-align:center"><?php echo $data['type_id'] ?></th>
                                        <th style="text-align:center"><?php echo $data['type_name'] ?></th>
                                        <th style="text-align:center">
                                            <a href="#edit<?php echo $data['type_id']; ?>" class="btn btn-secondary" data-toggle="modal">แก้ไข</a>
                                            <a href="?type_id=<?php echo $data['type_id']; ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
                                        </th>
                                    </tr>
                                    <!-- Modal แก้ไข -->
                                    <div class="modal fade" id="edit<?php echo $data['type_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" style="z-index: 9999">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h2 class="modal-title" id="exampleModalLongTitle">แก้ไขข้อมูลประเภทสินค้า</h2>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form method="post" class="form-horizontal" name="dep_form" id="dep_form">
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>ชื่อประเภทสินค้า</label>
                                                            <div>
                                                                <input type="text" name="type_name" id="type_name" class="form-control" autocomplete="off" placeholder="แผนก" value="<?= $data['type_name'] ?>">
                                                                <input type="hidden" name="type_id" value="<?= $data['type_id'] ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิกข้อมูล</button>
                                                        <button type="submit" name="type_edit" class="btn btn-primary">บันทึกข้อมูล</button>
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
                                <li class="page-item" <?php if ($page <= 1) {
                                                            echo "class='disabled'";
                                                        } ?>>
                                    <a class="page-link text-dark" <?php if ($page > 1) {
                                                                        echo "href='?page=$previous'";
                                                                    } ?>>Previous</a>
                                </li>
                                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                    <li class="page-item"><a class="page-link text-dark" href="protype.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                                <?php } ?>
                                <li class="page-item" <?php if ($page >= $total_pages) {
                                                            echo "class='disabled'";
                                                        } ?>>
                                    <a class="page-link text-dark" <?php if ($page < $total_pages) {
                                                                        echo "href='?page_no=$next'";
                                                                    } ?>>Next</a>
                                </li>
                                <li class="ml-auto">
                                    <h5>ข้อมูลประเภทสินค้าทั้งหมด <?= $record . " รายการ " ?> </้h5>
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
                    url: "protype_search.php",
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
            // var status = false;
            // $('#type_name').blur(function() {
            //     var type_name = $('#type_name').val();
            //     if(type_name==""){
            //         $('#validate').html("กรุณากรอกข้อมูลให้เรียบร้อย");
            //         $('#validate').addClass("text-danger");
            //         /// remove class ///
            //         $('#validate').removeClass("text-success");
            //         status = false;
            //         return;
            //     }
            //     $.ajax({
            //         type: "post",
            //         url: "check.php",
            //         data: {
            //             type_name: type_name
            //         },
            //         success: function(data) {
            //             if (data != '0') {
            //                 $('#validate').html("ไม่สามารถใช้ชื่อแผนกนี้ได้");
            //                 $('#validate').addClass("text-danger");
            //                 /// remove class ///
            //                 $('#validate').removeClass("text-success");
            //                 status = false;
            //             } else {
            //                 $('#validate').html("สามารถใช้ชื่อแผนกนี้ได้");
            //                 $('#validate').addClass("text-success");
            //                 /// remove class ///
            //                 $('#validate').removeClass("text-danger");
            //                 status = true;
            //             }
            //         }
            //     });
            // });
            // $('#type_insert').click(function(e) {
            //     if (status == false) {
            //         e.preventDefault();
            //         $('#validate').html("กรุณากรอกข้อมูลให้เรียบร้อย");
            //         $('#validate').addClass("text-danger");
            //         /// remove class ///
            //         $('#validate').removeClass("text-success");
            //     }
            // });
        });
    </script>
</body>

</html>