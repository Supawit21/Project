<?php
session_start();
require('config/connect.php');
$db = new DB();
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if (isset($_SESSION['pos_permit'])) {
//     if (substr($_SESSION['pos_permit'], 6, 1) != 1) {
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
$txtSQL = "SELECT count(size_id) as total FROM product_size where status_size = 1";
$result = $db->query($txtSQL);
$fetch_rec  = mysqli_fetch_array($result);
$record  = $fetch_rec['total'];
$total_pages   = ceil($record / $limit);
///  แสดงตารางขนาดสินค้า ///
$txtSQL  = "SELECT size_id,size_name,type_name,size_type FROM product_size INNER JOIN product_type ON (product_size.size_type=product_type.type_id) WHERE status_type = 1 LIMIT $start, $limit";
$result_size = $db->query($txtSQL);
/// ดึงข้อมูลประเภทสินค้า ///
$txtSQL = "SELECT type_id,type_name FROM product_type";
$query_type = $db->query($txtSQL);
/// สร้างรหัสรันเอง ///
$txtSQL = "SELECT concat('SIZ',NVL(lpad(substr(max(size_id),4,3)+1,3,'0'),'001')) as size FROM product_size";
$result = $db->query($txtSQL);
$fetch_id = mysqli_fetch_array($result);
/// บันทึกข้อมูลขนาด ///
if (isset($_POST['size_insert'])) {
    $size_id = $fetch_id['size'];
    $size_name = mysqli_real_escape_string($db->conn, $_POST["size_name"]);
    $size_type = $_POST['type_name'];
    $arr = array(
        "size_id" => $size_id,
        "size_name" => $size_name,
        "size_type" => $size_type
    );
    $insert = $db->insert("product_size", $arr);
    if ($insert) {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มข้อมูลสำเร็จ');";
        echo "window.location = 'prosize.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มข้อมูลไม่สำเร็จ');";
        echo "</script>";
    }
}
/// แก้ไขข้อมูล ///
if (isset($_POST['size_edit'])) {
    $size_id   = mysqli_real_escape_string($db->conn, $_POST["size_id"]);
    $size_name = mysqli_real_escape_string($db->conn, $_POST["size_name"]);
    $size_type = $_POST['type_name'];
    $arr1 = array(
        "size_name" => $size_name,
        "size_type" => $size_type
    );
    $where_condition = array(
        "size_id" => $size_id
    );
    $update = $db->update("product_size", $arr1, $where_condition);
    if ($update) {
        echo "<script type='text/javascript'>";
        echo "alert('แก้ไขข้อมูลสำเร็จ');";
        echo "window.location = 'prosize.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('แก้ไขข้อมูลไม่สำเร็จ');";
        echo "</script>";
    }
}
/// ลบข้อมูลขนาด ///
if (isset($_GET['size_id'])) {
    $size_id = $_GET['size_id'];
    $txtSQL = "UPDATE product_size SET status_type = 0 WHERE size_id = '$size_id'";
    $query = $db->query($txtSQL);
    if ($query) {
        echo "<script type='text/javascript'>";
        echo "alert('ลบข้อมูลสำเร็จ');";
        echo "window.location = 'prosize.php'; ";
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
                        <h2>ข้อมูลขนาดสินค้า</h2>
                        <form class="d-sm-flex justify-content-center" name="Search" id="Search">
                            <!-- Default input -->
                            <input type="search" name="txtsearch" id="txtsearch" placeholder="ค้นหา" class="form-control" autocomplete="off">
                            <input type="submit" name="in_search" id="in_search" class="btn btn-primary ml-2 mb-2" value="ค้นหา">
                        </form>
                    </div>
                </div>
                <form method="post" class="form-horizontal">
                    <div class="card mt-3">
                        <div class="col-md-4 mt-3 ml-1">
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModalLong">
                                เพิ่มข้อมูลขนาดสินค้า
                            </button>
                            <!-- Modal เพิ่ม -->
                            <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" style="z-index: 9999">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h2 class="modal-title" id="exampleModalLongTitle">ข้อมูลขนาดสินค้า</h2>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form method="post" class="form-horizontal" name="type_form" id="type_form">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>ประเภทสินค้า</label>
                                                    <select name="type_name" id="type_name" class="form-control custom-select">
                                                        <option selected disabled>เลือกประเภทสินค้า</option>
                                                        <?php foreach ($query_type as $value) { ?>
                                                            <option value="<?php echo $value['type_id'] ?>"><?php echo $value['type_name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span id="combobox" class="text-danger"><?php echo $combo_type; ?></span>
                                                </div>
                                                <div class="form-group">
                                                    <label>ขนาดสินค้า</label>
                                                    <div>
                                                        <input type="text" name="size_name" id="size_name" autocomplete="off" class="form-control" placeholder="ขนาดสินค้า">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิกข้อมูล</button>
                                                <button type="submit" name="size_insert" class="btn btn-primary">บันทึกข้อมูล</button>
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
                                        <th style="text-align:center">ขนาดสินค้า</th>
                                        <th style="text-align:center">เพิ่มเติม</th>
                                    </tr>
                                </thead>
                                <?php foreach ($result_size as $data) { ?>
                                    <tr>
                                        <th style="text-align:center"><?php echo $data['size_id'] ?></th>
                                        <th style="text-align:center"><?php echo $data['type_name'] . ' ' . $data['size_name'] ?></th>
                                        <th style="text-align:center">
                                            <a href="#edit<?php echo $data['size_id']; ?>" class="btn btn-secondary" data-toggle="modal">แก้ไข</a>
                                            <a href="?size_id=<?php echo $data['size_id']; ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
                                        </th>
                                    </tr>
                                    <!-- Modal แก้ไข -->
                                    <div class="modal fade" id="edit<?php echo $data['size_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" style="z-index: 9999">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h2 class="modal-title" id="exampleModalLongTitle">แก้ไขข้อมูลขนาดสินค้า</h2>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form method="post" class="form-horizontal" name="type_form" id="type_form">
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>ประเภทสินค้า</label>
                                                            <select name="type_name" id="type_name" class="form-control custom-select">
                                                                <option selected disabled>เลือกประเภทสินค้า</option>
                                                                <?php foreach ($query_type as $value) { ?>
                                                                    <option value="<?php echo $value['type_id'] ?>" <?php
                                                                                                                    if ($data['size_type'] == $value['type_id']) {
                                                                                                                        echo "selected";
                                                                                                                    }
                                                                                                                    ?>><?php echo $value['type_name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>ขนาดสินค้า</label>
                                                            <div>
                                                                <input type="text" name="size_name" id="size_name" autocomplete="off" class="form-control" placeholder="ขนาดสินค้า" value="<?= $data['size_name'] ?>">
                                                                <input type="hidden" name="size_id" value="<?=$data['size_id'] ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิกข้อมูล</button>
                                                        <button type="submit" name="size_edit" class="btn btn-primary">บันทึกข้อมูล</button>
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
                                    <h5>ข้อมูลขนาดสินค้าสินค้าทั้งหมด <?= $record . " รายการ " ?> </้h5>
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
                    url: "prosize_search.php",
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