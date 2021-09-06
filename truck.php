<?php
session_start();
require('config/connect.php');
$db = new DB();
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if (isset($_SESSION['pos_permit'])) {
//     if (substr($_SESSION['pos_permit'], 11, 1) != 1) {
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
$txtSQL = "SELECT count(tru_id) as total FROM trucks where status_truck = 1";
$result = $db->query($txtSQL);
$fetch_rec  = mysqli_fetch_array($result);
$record  = $fetch_rec['total'];
$total_pages   = ceil($record / $limit);
///  แสดงตารางรถขนส่ง ///
$txtSQL  = "SELECT tru_id,tru_reg,tru_type,empty_truck FROM trucks WHERE status_truck = 1 LIMIT $start, $limit";
$query_show = $db->query($txtSQL);
/// สร้างรหัสรันเอง ///
$txtSQL = "SELECT concat('TRU',NVL(lpad(substr(max(tru_id),4,3)+1,3,'0'),'001')) as truck FROM trucks";
$result = $db->query($txtSQL);
$fetch_id = mysqli_fetch_array($result);
/// บันทึกรถขนส่ง ///
if (isset($_POST['truck_insert'])) {
    $tru_id   = $fetch_id['truck'];
    $tru_reg  = $_POST['tru_reg'];
    $tru_type = $_POST['tru_type'];
    $arr = array(
        "tru_id" => $tru_id,
        "tru_reg" => $tru_reg,
        "tru_type" => $tru_type
    );
    $insert = $db->insert("trucks",$arr);
    if ($insert) {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มข้อมูลสำเร็จ');";
        echo "window.location = 'truck.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มข้อมูลไม่สำเร็จ');";
        echo "</script>";
    }
}
/// บันทึกรถขนส่ง ///
if (isset($_POST['truck_edit'])) {
    $tru_id   = mysqli_real_escape_string($db->conn, $_POST["tru_id"]);
    $tru_reg  = $_POST['tru_reg'];
    $tru_type = $_POST['tru_type'];
    $arr1 = array(
        "tru_reg" => $tru_reg,
        "tru_type" => $tru_type
    );
    $where_condition = array(
        "tru_id" => $tru_id
    );
    $update = $db->update("trucks",$arr1,$where_condition);
    if ($update) {
        echo "<script type='text/javascript'>";
        echo "alert('แก้ไขข้อมูลสำเร็จ');";
        echo "window.location = 'truck.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('แก้ไขข้อมูลไม่สำเร็จ');";
        echo "</script>";
    }
}
/// ลบข้อมูลรถขนส่ง ///
if (isset($_GET['tru_id'])) {
    $tru_id = $_GET['tru_id'];
    $txtSQL = "UPDATE trucks SET status_truck = 0 WHERE tru_id = '$tru_id'";
    $query = $db->query($txtSQL);
    if ($query) {
        echo "<script type='text/javascript'>";
        echo "alert('ลบข้อมูลสำเร็จ');";
        echo "window.location = 'truck.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('ลบข้อมูลไม่สำเร็จ');";
        echo "</script>";
    }
}
/// เช็ครถกลับเข้าบริษัท ///
if (isset($_GET['status'])) {
    $tru_id = $_GET['status'];
    $txtSQL = "UPDATE trucks SET empty_truck = 1 WHERE tru_id = '$tru_id'";
    $query = $db->query($txtSQL);
    if ($query) {
        echo "<script type='text/javascript'>";
        echo "alert('เช็คข้อมูลสำเร็จ');";
        echo "window.location = 'truck.php'; ";
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
                            <h2>ข้อมูลรถขนส่ง</h2>
                            <form class="d-sm-flex justify-content-center" name="Search" id="Search">
                                <!-- Default input -->
                                <input type="search" name="txtsearch" id="txtsearch" autocomplete="off" placeholder="ค้นหา" class="form-control">
                                <input type="submit" name="in_search" id="in_search" class="btn btn-primary ml-2 mb-2" value="ค้นหา">
                            </form>
                        </div>
                    </div>
                    <div class="card border-0 mt-3">
                        <div class="col-md-4 mt-3 ml-1">
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModalLong">
                                เพิ่มข้อมูลรถขนส่ง
                            </button>
                            <!-- Modal เพิ่ม -->
                            <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" style="z-index: 9999">
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
                                                    <label>เลขทะเบียนรถ</label>
                                                    <input type="text" name="tru_reg" id="tru_reg" class="form-control" placeholder="เลขทะเบียน" autocomplete="off">
                                                </div>
                                                <div class="form-group">
                                                    <label>ประเภทรถขนส่ง</label>
                                                    <select name="tru_type" id="tru_type" class="form-control custom-select">
                                                        <option selected disabled>รถขนส่ง</option>
                                                        <option value="รถเทลเลอร์">รถเทลเลอร์</option>
                                                        <option value="รถ 6 ล้อ">รถ 6 ล้อ</option>
                                                        <option value="รถ 4 ล้อเล็ก">รถ 4 ล้อเล็ก</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิกข้อมูล</button>
                                                <button type="submit" name="truck_insert" class="btn btn-primary">บันทึกข้อมูล</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body d-sm-flex justify-content-between">
                            <table class="table table-bordered" id="table-data">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align:center">ลำดับ</th>
                                        <th style="text-align:center">เลขทะเบียนรถ</th>
                                        <th style="text-align:center">ประเภทรถขนส่ง</th>
                                        <th style="text-align:center">เพิ่มเติม</th>
                                    </tr>
                                </thead>
                                <?php foreach ($query_show as $data) { ?>
                                    <?php if($data['empty_truck']=="1") {?>
                                    <tr>
                                        <th style="text-align:center"><?php echo $data['tru_id'] ?></th>
                                        <th style="text-align:center"><?php echo $data['tru_reg'] ?></th>
                                        <th style="text-align:center"><?php echo $data['tru_type'] ?></th>
                                        <th style="text-align:center">
                                            <a href="#edit<?php echo $data['tru_id'] ?>" class="btn btn-secondary" data-toggle="modal">แก้ไข</a>
                                            <a href="?tru_id=<?php echo $data['tru_id'] ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
                                        </th>
                                    </tr>
                                    <?php }else{ ?>
                                        <tr>
                                        <th style="text-align:center"><?php echo $data['tru_id'] ?></th>
                                        <th style="text-align:center"><?php echo $data['tru_reg'] ?></th>
                                        <th style="text-align:center"><?php echo $data['tru_type'] ?></th>
                                        <th style="text-align:center">
                                            <a href="#edit<?php echo $data['tru_id'] ?>" class="btn btn-secondary" data-toggle="modal">แก้ไข</a>
                                            <a href="?tru_id=<?php echo $data['tru_id'] ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
                                            <a href="?status=<?php echo $data['tru_id'] ?>" class="btn btn-primary">เช็ครถกลับเข้าบริษัท</a>
                                        </th>
                                    </tr>
                                    <?php } ?>
                                                                <!-- Modal แก้ไข -->
                            <div class="modal fade" id="edit<?=$data['tru_id']?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" style="z-index: 9999">
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
                                                    <label>เลขทะเบียนรถ</label>
                                                    <input type="text" name="tru_reg" id="tru_reg" class="form-control" placeholder="เลขทะเบียน" autocomplete="off" value="<?=$data['tru_reg']?>">
                                                    <input type="hidden" name="tru_id" value="<?=$data['tru_id']?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>ประเภทรถขนส่ง</label>
                                                    <select name="tru_type" id="tru_type" class="form-control custom-select">
                                                        <option selected disabled>รถขนส่ง</option>
                                                        <option value="รถเทลเลอร์"<?php
                                                                if ($data['tru_type'] == "รถเทลเลอร์") {
                                                                    echo "selected";
                                                                }
                                                                ?>>รถเทลเลอร์</option>
                                                        <option value="รถ 6 ล้อ"<?php
                                                                if ($data['tru_type'] == "รถ 6 ล้อ") {
                                                                    echo "selected";
                                                                }
                                                                ?>>รถ 6 ล้อ</option>
                                                        <option value="รถ 4 ล้อเล็ก"<?php
                                                                if ($data['tru_type'] == "รถ 4 ล้อเล็ก") {
                                                                    echo "selected";
                                                                }
                                                                ?>>รถ 4 ล้อเล็ก</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิกข้อมูล</button>
                                                <button type="submit" name="truck_edit" class="btn btn-primary">บันทึกข้อมูล</button>
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
                                    <li class="page-item"><a class="page-link text-dark" href="truck.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                                <?php } ?>
                                <li class="page-item" <?php if ($page >= $total_pages) {
                                                            echo "class='disabled'";
                                                        } ?>>
                                    <a class="page-link text-dark" <?php if ($page < $total_pages) {
                                                                        echo "href='?page_no=$next'";
                                                                    } ?>>Next</a>
                                </li>
                                <li class="ml-auto">
                                    <h5>ข้อมูลรถขนส่งทั้งหมด <?= $record . " รายการ " ?> </้h5>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/script.php') ?>
    <script>
        $(function() {
            $('#Search').submit(function(e) {
                e.preventDefault();
                var txt = $('#txtsearch').val();
                $.ajax({
                    type: "post",
                    url: "truck_search.php",
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
            // $('#tru_reg').blur(function() {
            //     var tru_reg = $('#tru_reg').val();
            //     if (tru_reg == "") {
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
            //             tru_reg: tru_reg
            //         },
            //         success: function(data) {
            //             if (data != '0') {
            //                 $('#validate').html("มีเลขป้ายทะเบียนนี้ในระบบ");
            //                 $('#validate').addClass("text-danger");
            //                 /// remove class ///
            //                 $('#validate').removeClass("text-success");
            //                 status = false;
            //             } else {
            //                 $('#validate').html("สามารถใช้เลขป้ายทะเบียนนี้นี้ได้");
            //                 $('#validate').addClass("text-success");
            //                 /// remove class ///
            //                 $('#validate').removeClass("text-danger");
            //                 status = true;
            //             }
            //         }
            //     });
            // });
            // $('#tru_type').change(function() {
            //     var combobox = $('#tru_type').val();
            //     if (combobox == "") {
            //         status = false;
            //         return;
            //     } else {
            //         $('#combobox').html("");
            //     }
            // });
            // $('#truck_insert').click(function(e) {
            //     if (status == false) {
            //         e.preventDefault();
            //         $('#combobox').html("กรุณาเลือกข้อมูล");
            //         $('#combobox').addClass("text-danger");
            //         $('#combobox').removeClass("text-success");
            //         /// error txt pos ///
            //         $('#validate').html("กรุณากรอกข้อมูลให้เรียบร้อย");
            //         $('#validate').addClass("text-danger");
            //         $('#validate').removeClass("text-success");
            //     }
            // });
        });
    </script>
</body>

</html>