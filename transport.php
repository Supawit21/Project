<?php
session_start();
require('config/connect.php');
$db = new DB();
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if(isset($_SESSION['pos_permit']))
// {
//     if(substr($_SESSION['pos_permit'],12,1) != 1){
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
$txtSQL = "SELECT count(tran_id) as total FROM transport where status_tran = 1";
$result = $db->query($txtSQL);
$fetch_rec  = mysqli_fetch_array($result);
$record  = $fetch_rec['total'];
$total_pages   = ceil($record / $limit);
///  แสดงตารางเส้นทาง ///
$txtSQL = "SELECT tran_id,tran_name FROM transport WHERE status_tran = 1 LIMIT $start, $limit";
$query = $db->query($txtSQL);
/// สร้างรหัสรันเอง ///
$txtSQL = "SELECT concat('WAY',NVL(lpad(substr(max(tran_id),4,3)+1,3,'0'),'001')) as trans FROM transport";
$result = $db->query($txtSQL);
$fetch_id = mysqli_fetch_array($result);
/// บันทึกข้อมูลเส้นทาง ///
if (isset($_POST['tran_insert'])) {
    $tran_id   = $fetch_id['trans'];
    $tran_name = mysqli_real_escape_string($db->conn, $_POST["tran_name"]);
    $arr = array(
        "tran_id" => $tran_id,
        "tran_name" => $tran_name
    );
    $insert = $db->insert("transport",$arr);
    if ($insert) {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มข้อมูลสำเร็จ');";
        echo "window.location = 'transport.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มข้อมูลไม่สำเร็จ');";
        echo "</script>";
    }
}
/// บันทึกข้อมูลเส้นทาง ///
if (isset($_POST['tran_edit'])) {
    $tran_id   = mysqli_real_escape_string($db->conn, $_POST["tran_id"]);
    $tran_name = mysqli_real_escape_string($db->conn, $_POST["tran_name"]);
    $arr1 = array(
        "tran_name" => $tran_name
    );
    $where_condition = array(
        "tran_id" => $tran_id
    );
    $update = $db->update("transport",$arr1,$where_condition);
    if ($update) {
        echo "<script type='text/javascript'>";
        echo "alert('แก้ไขข้อมูลสำเร็จ');";
        echo "window.location = 'transport.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('แก้ไขข้อมูลไม่สำเร็จ');";
        echo "</script>";
    }
}
/// ลบข้อมูลเส้นทาง///
if (isset($_GET['tran_id'])) {
    $tran_id = $_GET['tran_id'];
    $txtSQL = "UPDATE transport SET status_tran = 0 WHERE tran_id = '$tran_id'";
    $query = $db->query($txtSQL);
    if ($query) {
        echo "<script type='text/javascript'>";
        echo "alert('ลบข้อมูลสำเร็จ');";
        echo "window.location = 'transport.php'; ";
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
            <div class="col-lg-10 ml-auto">
                <div class="card border-0 mt-5">
                    <div class="card-body d-sm-flex justify-content-between">
                        <h2>ข้อมูลเส้นทาง</h2>
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
                                เพิ่มข้อมูลเส้นทาง
                            </button>
                            <!-- Modal เพิ่ม -->
                            <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" style="z-index: 9999">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h2 class="modal-title" id="exampleModalLongTitle">ข้อมูลเส้นทาง</h2>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form method="post" class="form-horizontal" name="dep_form" id="dep_form1" novalidate>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>ชื่อเส้นทาง</label>
                                                    <input type="text" name="tran_name" id="tran_name" class="form-control" autocomplete="off" placeholder="เส้นทาง">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิกข้อมูล</button>
                                                <button type="submit" name="tran_insert" class="btn btn-primary">บันทึกข้อมูล</button>
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
                                        <th style="text-align:center">ชื่อเส้นทาง</th>
                                        <th style="text-align:center">เพิ่มเติม</th>
                                    </tr>
                                </thead>
                                <?php foreach ($query as $data) { ?>
                                    <tr>
                                        <th style="text-align:center"><?php echo $data['tran_id'] ?></th>
                                        <th style="text-align:center"><?php echo $data['tran_name'] ?></th>
                                        <th style="text-align:center">
                                            <a href="#edit<?php echo $data['tran_id']; ?>" class="btn btn-secondary" data-toggle="modal">แก้ไข</a>
                                            <a href="?tran_id=<?php echo $data['tran_id']; ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
                                        </th>
                                    </tr>
                                                                                                    <!-- Modal แก้ไข -->
                            <div class="modal fade" id="edit<?=$data['tran_id']?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" style="z-index: 9999">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h2 class="modal-title" id="exampleModalLongTitle">ข้อมูลรถขนส่ง</h2>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form method="post" class="form-horizontal" name="dep_form" id="dep_form1" novalidate>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>ชื่อเส้นทาง</label>
                                                    <input type="text" name="tran_name" id="tran_name" class="form-control" autocomplete="off" placeholder="เส้นทาง" value="<?=$data['tran_name']?>">
                                                    <input type="hidden" name="tran_id" value="<?=$data['tran_id']?>">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิกข้อมูล</button>
                                                <button type="submit" name="tran_edit" class="btn btn-primary">บันทึกข้อมูล</button>
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
                                    <li class="page-item"><a class="page-link text-dark" href="transport.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                                <?php } ?>
                                <li class="page-item" <?php if ($page >= $total_pages) {
                                                            echo "class='disabled'";
                                                        } ?>>
                                    <a class="page-link text-dark" <?php if ($page < $total_pages) {
                                                                        echo "href='?page_no=$next'";
                                                                    } ?>>Next</a>
                                </li>
                                <li class="ml-auto">
                                    <h5>ข้อมูลเส้นทางทั้งหมด <?= $record . " รายการ " ?> </้h5>
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
                    url: "tran_search.php",
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
            // $('#tran_name').blur(function() {
            //     var tran_name = $('#tran_name').val();
            //     if(tran_name==""){
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
            //             tran_name: tran_name
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
            // $('#tran_insert').click(function(e) {
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