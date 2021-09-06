<?php
session_start();
require('config/connect.php');
$db = new DB();
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if (isset($_SESSION['pos_permit'])) {
//     if (substr($_SESSION['pos_permit'], 1, 1) != 1) {
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
$txtSQL = "SELECT count(pos_id) total FROM position where pos_status = 1";
$result = $db->query($txtSQL);
$fetch_rec  = mysqli_fetch_array($result);
$record  = $fetch_rec['total'];
$total_pages   = ceil($record / $limit);
///  แสดงตารางตำแหน่ง ///
$txtSQL = "SELECT pos_id,pos_name FROM position where pos_status = 1  LIMIT $start, $limit";
$result_show = $db->query($txtSQL);
/// สร้างรหัสรันเอง ///
$txtSQL = "SELECT concat('POS',NVL(lpad(substr(max(pos_id),4,3)+1,3,'0'),'001')) as pos FROM position";
$result = $db->query($txtSQL);
$fetch_id = mysqli_fetch_array($result);
/// ดึงข้อมูลแผนก ///
$txtSQL = "SELECT dep_id,dep_name FROM department";
$result_dep = $db->query($txtSQL);
/// บันทึกข้อมูลตำแหน่ง ///
if (isset($_POST['pos_insert'])) {
    $pos_id   = $fetch_id['pos'];
    $pos_name = mysqli_real_escape_string($db->conn, $_POST['pos_name']);
    $check_dep = $_POST['check_dep'];
    $check_pos = $_POST['check_pos'];
    $check_emp = $_POST['check_emp'];
    $check_cus = $_POST['check_cus'];
    $check_com = $_POST['check_com'];
    $check_type = $_POST['check_type'];
    $check_size = $_POST['check_size'];
    $check_unit = $_POST['check_unit'];
    $check_product = $_POST['check_product'];
    $check_cost = $_POST['check_cost'];
    $check_promo = $_POST['check_promo'];
    $check_truck = $_POST['check_truck'];
    $check_way = $_POST['check_way'];
    $check_pd  = $_POST['check_pd'];
    $check_quo = $_POST['check_quo'];
    $check     = $check_dep . $check_pos . $check_emp . $check_cus . $check_com . $check_type . $check_size . $check_unit . $check_product . $check_cost . $check_promo . $check_truck . $check_way . $check_pd . $check_quo;
    $did       = $_POST['department'];
    $arr = array(
        "pos_id" => $pos_id,
        "pos_name" => $pos_name,
        "pos_permit" => $check,
        "pos_dep" => $did
    );
    $insert = $db->insert("position", $arr);
    if ($insert) {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มข้อมูลสำเร็จ');";
        echo "window.location = 'position.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มข้อมูลไม่สำเร็จ');";
        echo "</script>";
    }
}
/// แก้ไขข้อมูลตำแหน่ง ///
// if (isset($_POST['pos_edit'])) {
//     $pos_id = mysqli_real_escape_string($db->conn, $_POST['pos_id']);
//     $pos_name = mysqli_real_escape_string($db->conn, $_POST['pos_name']);
//     $check_dep = $_POST['check_dep'];
//     $check_pos = $_POST['check_pos'];
//     $check_emp = $_POST['check_emp'];
//     $check_cus = $_POST['check_cus'];
//     $check_com = $_POST['check_com'];
//     $check_type = $_POST['check_type'];
//     $check_size = $_POST['check_size'];
//     $check_unit = $_POST['check_unit'];
//     $check_product = $_POST['check_product'];
//     $check_cost = $_POST['check_cost'];
//     $check_promo = $_POST['check_promo'];
//     $check_truck = $_POST['check_truck'];
//     $check_way  = $_POST['check_way'];
//     $check_pd  = $_POST['check_pd'];
//     $check_quo = $_POST['check_quo'];
//     $check    = $check_dep . $check_pos . $check_emp . $check_cus . $check_com . $check_type . $check_size . $check_unit . $check_product . $check_cost . $check_promo . $check_truck . $check_way . $check_pd . $check_quo;
//     $did      = $_POST['department'];
//     $arr = array(
//         "pos_name" => $pos_name,
//         "pos_permit" => $check,
//         "pos_dep" => $did
//     );
//     $where_condition = array(
//         "pos_id" => $pos_id
//     );
//     $update = $db->update("position", $arr, $where_condition);
//     if ($update) {
//         echo "<script type='text/javascript'>";
//         echo "alert('แก้ไขข้อมูลสำเร็จ');";
//         echo "window.location = 'position.php'; ";
//         echo "</script>";
//     } else {
//         echo "<script type='text/javascript'>";
//         echo "alert('แก้ไขข้อมูลไม่สำเร็จ');";
//         echo "</script>";
//     }
// }
/// ลบข้อมูลตำแหน่ง ///
if (isset($_GET['pos_id'])) {
    $pos_id = $_GET['pos_id'];
    $sql = "UPDATE position SET status_pos = 0 WHERE pos_id = '$pos_id'";
    $query = mysqli_query($conn, $sql);
    if ($query) {
        echo "<script type='text/javascript'>";
        echo "alert('ลบข้อมูลสำเร็จ');";
        echo "window.location = 'position.php'; ";
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
                <div class="card mt-5 ">
                    <div class="card-body d-sm-flex justify-content-between">
                        <h2>ข้อมูลตำแหน่ง </h2>
                        <form class="d-sm-flex justify-content-center" name="Search" id="Search">
                            <input type="search" name="txtsearch" id="txtsearch" autocomplete="off" placeholder="ค้นหา" class="form-control">
                            <input type="submit" name="in_search" id="in_search" class="btn btn-primary ml-2 mb-2" value="ค้นหา">
                        </form>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="col-md-4 mt-3 ml-1">
                    </div>
                    <div class="card-body d-sm-flex justify-content-between table-responsive-md">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align:center">ลำดับ</th>
                                    <th style="text-align:center">ชื่อตำแหน่ง</th>
                                    <th style="text-align:center">เพิ่มเติม</th>
                                </tr>
                            </thead>
                            <?php foreach ($result_show as $data) { ?>
                                <tr>
                                    <th style="text-align:center"><?php echo $data['pos_id'] ?></th>
                                    <th style="text-align:center"><?php echo $data['pos_name'] ?></th>
                                    <th style="text-align:center">
                                        <a href="position_edit.php?id=<?php echo $data['pos_id']; ?>" class="btn btn-secondary">แก้ไข</a>
                                        <a href="?pos_id=<?php echo $data['pos_id']; ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
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
                                <li class="page-item"><a class="page-link text-dark" href="position.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                            <?php } ?>
                            <li class="page-item" <?php if ($page >= $total_pages) {
                                                        echo "class='disabled'";
                                                    } ?>>
                                <a class="page-link text-dark" <?php if ($page < $total_pages) {
                                                                    echo "href='?page_no=$next'";
                                                                } ?>>Next</a>
                            </li>
                            <li class="ml-auto">
                                <h5>ข้อมูลตำแหน่งทั้งหมด <?= $record . " รายการ " ?> </้h5>
                            </li>
                        </ul>
                    </nav>
                </div>
                <div class="card mt-3">
                    <div class="card-body col-md-7">
                        <h2>เพิ่มข้อมูลตำแหน่ง</h2>
                        <form method="post" class="form-horizontal" name="pos_form" id="pos_form">
                            <div class="form-group">
                                <label>ชื่อแผนก</label>
                                <select name="department" id="department" class="form-control custom-select">
                                    <option value="" selected disabled>เลือกแผนก</option>
                                    <?php foreach ($result_dep as $value) { ?>
                                        <option value="<?php echo $value['dep_id'] ?>"><?php echo $value['dep_name'] ?></option>
                                    <?php } ?>
                                </select>
                                <span id="combobox"></span>
                            </div>
                            <label>ชื่อตำแหน่ง</label>
                            <div class="form-group">
                                <input type="text" name="pos_name" id="pos_name" class="form-control" autocomplete="off" placeholder="ตำแหน่ง">
                                <span id="validate"></span>
                            </div>
                            <div class="form-row" id="checkbox">
                                <div class="form-group ml-2">
                                    <label>สิทธิ์การใช้งาน</label>
                                    <div class="form-check">
                                        <input type="hidden" value="0" name="check_dep">
                                        <input class="form-check-input" type="checkbox" value="1" name="check_dep">
                                        <label>จัดการข้อมูลแผนก</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="hidden" value="0" name="check_pos">
                                        <input class="form-check-input" type="checkbox" value="1" name="check_pos">
                                        <label>จัดการข้อมูลตำแหน่ง</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="hidden" value="0" name="check_emp">
                                        <input class="form-check-input" type="checkbox" value="1" name="check_emp">

                                        <label>จัดการข้อมูลพนักงาน</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="hidden" value="0" name="check_cus">
                                        <input class="form-check-input" type="checkbox" value="1" name="check_cus">

                                        <label>จัดการข้อมูลลูกค้า</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="hidden" value="0" name="check_com">
                                        <input class="form-check-input" type="checkbox" value="1" name="check_com">

                                        <label>จัดการข้อมูลบริษัทคู่ค้า</label>
                                    </div>
                                </div>
                                <div class="form-group ml-3 mt-4 p-1">
                                    <div class="form-check">
                                        <input type="hidden" value="0" name="check_type">
                                        <input class="form-check-input" type="checkbox" value="1" name="check_type">

                                        <label>จัดการข้อมูลประเภทสินค้า</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="hidden" value="0" name="check_size">
                                        <input class="form-check-input" type="checkbox" value="1" name="check_size">

                                        <label>จัดการข้อมูลขนาดสินค้า</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="hidden" value="0" name="check_unit">
                                        <input class="form-check-input" type="checkbox" value="1" name="check_unit">

                                        <label>จัดการข้อมูลหน่วยสินค้า</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="hidden" value="0" name="check_product">
                                        <input class="form-check-input" type="checkbox" value="1" name="check_product">

                                        <label>จัดการข้อมูลสินค้า</label>
                                    </div>
                                    <div class="form-check">

                                        <input type="hidden" value="0" name="check_cost">
                                        <input class="form-check-input" type="checkbox" value="1" name="check_cost">

                                        <label>จัดการข้อมูลราคาต้นทุน</label>
                                    </div>
                                </div>
                                <div class="form-group ml-3 mt-4 p-1">
                                    <div class="form-check">
                                        <input type="hidden" value="0" name="check_promo">
                                        <input class="form-check-input" type="checkbox" value="1" name="check_promo">

                                        <label>จัดการข้อมูลโปรโมชั่น</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="hidden" value="0" name="check_truck">
                                        <input class="form-check-input" type="checkbox" value="1" name="check_truck">

                                        <label>จัดการข้อมูลรถขนส่ง</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="hidden" value="0" name="check_way">
                                        <input class="form-check-input" type="checkbox" value="1" name="check_way">

                                        <label>จัดการข้อมูลเส้นทางรถขนส่ง</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="hidden" value="0" name="check_pd">
                                        <input class="form-check-input" type="checkbox" value="1" name="check_pd">

                                        <label>จัดการอนุมัติใบเสนอสินค้า</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="hidden" value="0" name="check_quo">
                                        <input class="form-check-input" type="checkbox" value="1" name="check_quo">

                                        <label>จัดการอนุมัติใบเสนอราคา</label>
                                    </div>
                                </div>
                                <!-- <div class="form-group ml-3 mt-4 p-1"> -->
                                    <div class="form-check ml-3 mt-4 p-1">
                                        <input class="form-check-input" type="checkbox" id="check_all" name="check_all">
                                        <label>ทั้งหมด</label>
                                    </div>
                                <!-- </div> -->
                            </div>
                            <div class="form-group">
                                <input type="submit" name="pos_insert" id="pos_insert" class="btn btn-primary btn-lg" value="บันทึกข้อมูล">
                            </div>
                        </form>
                    </div>
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
                    url: "pos_search.php",
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
            $('#check_all').click(function() {
                var chk = $('#check_all').prop("checked");
                if (chk) {
                    $(".form-check-input").prop("checked", true);
                } else {
                    $(".form-check-input").prop("checked", false);
                }
            });
        });
    </script>
</body>

</html>