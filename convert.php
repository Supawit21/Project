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
$txtSQL = "SELECT count(con_product) as total FROM convent where con_status = 1";
$result = $db->query($txtSQL);
$fetch_rec  = mysqli_fetch_array($result);
$record  = $fetch_rec['total'];
$total_pages   = ceil($record / $limit);
///  แสดงตารางแปลงหน่วย ///
$txtSQL = "SELECT con_product,con_ratio,pro_name,size_name,con_untf,con_unts,ut1.unit_name as unt_main ,ut2.unit_name as unt_sub FROM convent 
           INNER JOIN product ON (con_product=pro_id)
           INNER JOIN product_size ON (pro_size=size_id)
           INNER JOIN product_unit as ut1 ON (con_untf=ut1.unit_id)
           INNER JOIN product_unit as ut2 ON (con_unts=ut2.unit_id) where con_status = 1 LIMIT $start, $limit";
$result_show = $db->query($txtSQL);
/// ดึงข้อมูลชื่อสินค้า ///
$txtSQL = "SELECT pro_id,pro_name,size_name,unit_name FROM product
           INNER JOIN product_size ON (pro_size=size_id)
           INNER JOIN convent      ON (pro_id=con_product)
           INNER JOIN product_unit ON (con_unts=unit_id)";
$result_product = $db->query($txtSQL);
/// ดึงข้อมูลหน่วย ///
$txtSQL = "SELECT unit_id,unit_name FROM product_unit";
$result_unit = $db->query($txtSQL);
/// เพิ่มข้อมูลแผนก ///
if (isset($_POST['con_insert'])) {
    $con_product = $_POST['con_product'];
    $con_ratio   = $_POST['con_ratio'];
    $con_untf    = $_POST['con_untf'];
    $arr_n = array(
        "con_ratio" => $con_ratio,
        "con_untf" => $con_untf
    );
    $where_condition_n = array(
        "con_product" => $con_product
    );
    $update_n = $db->update("convent", $arr_n, $where_condition_n);
    if ($update_n) {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มข้อมูลสำเร็จ');";
        echo "window.location = 'convert.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('เพิ่มข้อมูลไม่สำเร็จ');";
        echo "</script>";
    }
}
/// แก้ไขข้อมูลแผนก ///
if (isset($_POST['con_edit'])) {
    $con_product = $_POST['con_product'];
    $con_ratio   = $_POST['con_ratio'];
    $con_untf    = $_POST['con_untf'];
    $con_unts    = $_POST['con_unts'];
    $arr_u = array(
        "con_ratio" => $con_ratio,
        "con_untf" => $con_untf
    );
    $where_condition_u = array(
        "con_product" => $con_product
    );
    $update_u = $db->update("convent", $arr_u, $where_condition_u);
    if ($update_u) {
        echo "<script type='text/javascript'>";
        echo "alert('แก้ไขข้อมูลสำเร็จ');";
        echo "window.location = 'convert.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('แก้ไขข้อมูลไม่สำเร็จ');";
        echo "</script>";
    }
}
/// ลบข้อมูลแผนก ///
if (isset($_GET['con_product'])) {
    $con_product = $_GET['con_product'];
    $arr = array(
        "con_status" => 0
    );
    $where_condition = array(
        "con_product" => $con_product
    );
    $delete = $db->update("convent", $arr, $where_condition);
    if ($delete) {
        echo "<script type='text/javascript'>";
        echo "alert('ลบข้อมูลสำเร็จ');";
        echo "window.location = 'convert.php'; ";
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
            <div class="col-md-10 col-lg-10 col-xl-10 ml-auto">
                <div class="card border-0 mt-5">
                    <div class="card-body d-sm-flex justify-content-between">
                        <h2>ข้อมูลการแปลงหน่วย</h2>
                        <form class="d-sm-flex justify-content-center" name="Search" id="Search">
                            <input type="search" name="txtsearch" id="txtsearch" autocomplete="off" placeholder="ค้นหา" class="form-control">
                            <input type="submit" name="in_search" id="in_search" class="btn btn-primary ml-2 mb-2" value="ค้นหา">
                        </form>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="col-md-4 mt-3 ml-1">
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModalLong">
                            เพิ่มการแปลงหน่วย
                        </button>
                        <!-- Modal เพิ่ม -->
                        <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" style="z-index: 9999">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h2 class="modal-title" id="exampleModalLongTitle">ข้อมูลแปลงหน่วย</h2>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form method="post" class="form-horizontal" name="dep_form" id="dep_form">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>ชื่อสินค้า/ชื่อหน่วยสินค้า</label>
                                                <select name="con_product" id="con_product" class="form-control custom-select">
                                                    <option value="" selected disabled>เลือกสินค้า/หน่วยสินค้า</option>
                                                    <?php foreach ($result_product  as $value) { ?>
                                                        <option value="<?php echo $value['pro_id'] ?>"><?php echo $value['pro_name'] . ' ' . $value['size_name'] . '/' . $value['unit_name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-5">
                                                    <label>หน่วยใหญ่</label>
                                                    <select name="con_untf" id="con_untf" class="form-control custom-select">
                                                        <option value="" selected disabled>เลือกหน่วยสินค้า</option>
                                                        <?php foreach ($result_unit  as $value) { ?>
                                                            <option value="<?php echo $value['unit_id'] ?>"><?php echo $value['unit_name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-7">
                                                    <label>อัตราส่วนสินค้า</label>
                                                    <input type="number" name="con_ratio" id="con_ratio" class="form-control" placeholder="อัตราส่วน" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิกข้อมูล</button>
                                            <button type="submit" name="con_insert" class="btn btn-primary">บันทึกข้อมูล</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body d-sm-flex justify-content-between">
                        <table class="table table-bordered ">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align:center">ชื่อสินค้า</th>
                                    <th style="text-align:center">หน่วยใหญ่</th>
                                    <th style="text-align:center">อัตราส่วน/หน่วย</th>
                                    <th style="text-align:center">เพิ่มเติม</th>
                                </tr>
                            </thead>
                            <?php foreach ($result_show as $data) { ?>
                                <tr>
                                    <th style="text-align:center"><?php echo $data['pro_name'] . ' ' . $data['size_name'] ?></th>
                                    <th style="text-align:center"><?php echo $data['unt_main'] ?></th>
                                    <th style="text-align:center"><?php echo $data['con_ratio'] . ' ' . $data['unt_sub'] ?></th>
                                    <th style="text-align:center">
                                        <a href="#edit<?php echo $data['con_product']; ?>" class="btn btn-secondary" data-toggle="modal">แก้ไข</a>
                                        <a href="?con_product=<?php echo $data['con_product']; ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
                                    </th>
                                </tr>
                                <!-- Modal แก้ไข -->
                                <div class="modal fade" id="edit<?php echo $data['con_product']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" style="z-index: 9999">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h2 class="modal-title" id="exampleModalLongTitle">ข้อมูลแปลงหน่วย</h2>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form method="post" class="form-horizontal" name="dep_form" id="dep_form">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>ชื่อสินค้า</label>
                                                        <select name="con_product" id="con_product" class="form-control custom-select">
                                                            <option value="" selected disabled>เลือกสินค้า</option>
                                                            <?php foreach ($result_product  as $value) { ?>
                                                                <option value="<?php echo $value['pro_id'] ?>" <?php
                                                                                                                if ($data['con_product'] == $value['pro_id']) {
                                                                                                                    echo "selected";
                                                                                                                }
                                                                                                                ?>><?php echo $value['pro_name'] . ' ' . $value['size_name'] . '/' . $value['unit_name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="form-group col-md-4">
                                                            <label>หน่วยสินค้า</label>
                                                            <select name="con_untf" id="con_untf" class="form-control custom-select">
                                                                <option value="" selected disabled>เลือกหน่วย</option>
                                                                <?php foreach ($result_unit  as $value) { ?>
                                                                    <option value="<?php echo $value['unit_id'] ?>" <?php
                                                                                                                    if ($data['con_untf'] == $value['unit_id']) {
                                                                                                                        echo "selected";
                                                                                                                    }
                                                                                                                    ?>><?php echo $value['unit_name'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-8">
                                                            <label>อัตราส่วนสินค้า</label>
                                                            <input type="number" name="con_ratio" id="con_ratio" class="form-control" placeholder="อัตราส่วน" autocomplete="off" value=<?= $data['con_ratio'] ?>>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิกข้อมูล</button>
                                                    <button type="submit" name="con_edit" class="btn btn-primary">บันทึกข้อมูล</button>
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
                                <li class="page-item"><a class="page-link text-dark" href="convert.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                            <?php } ?>
                            <li class="page-item" <?php if ($page >= $total_pages) {
                                                        echo "class='disabled'";
                                                    } ?>>
                                <a class="page-link text-dark" <?php if ($page < $total_pages) {
                                                                    echo "href='?page_no=$next'";
                                                                } ?>>Next</a>
                            </li>
                            <li class="ml-auto">
                                <h5>ข้อมูลการแปลงหน่วยทั้งหมด <?= $record . " รายการ " ?> </้h5>
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
            $('#in_search').click(function(e) {
                e.preventDefault();
                var txt = $('#txtsearch').val();
                $.ajax({
                    type: "post",
                    url: "convert_search.php",
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