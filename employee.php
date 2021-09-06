<?php
session_start();
require('config/connect.php');
$db = new DB();
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if (isset($_SESSION['pos_permit'])) {
//     if (substr($_SESSION['pos_permit'], 2, 1) != 1) {
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
$txtSQL = "SELECT count(emp_id) total FROM employee where status_emp = 1";
$result = $db->query($txtSQL);
$fetch_rec  = mysqli_fetch_array($result);
$record  = $fetch_rec['total'];
$total_pages   = ceil($record / $limit);
///  แสดงตารางพนักงาน ///
$txtSQL = "SELECT employee.emp_id,emp_img,emp_title,emp_name,emp_surname,emp_email,emp_tel FROM employee 
           INNER JOIN employee_tel ON (employee.emp_id=employee_tel.emp_id) where status_emp = 1 group by emp_id LIMIT $start, $limit";
$result = $db->query($txtSQL);
/// ลบข้อมูลพนักงาน ///
if (isset($_GET['emp_id'])) {
    $emp_id = $_GET['emp_id'];
    /// ลบชื่อภาพจาก database ///
    $txtSQL = "SELECT emp_img FROM employee WHERE emp_id = '$emp_id'";
    $result = $db->query($txtSQL);
    $row_delete  = mysqli_fetch_assoc($result);
    /// ลบรูปจาก folder ///
    $dir = "image_emp/";
    $pic = $row_delete['emp_img'];
    unlink($dir . $pic);
    /// ลบข้อมูลโดยการเปลี่ยนสถานะ จาก 1 เป็น 0 ///
    $txtSQL = "UPDATE employee SET status_emp = 0 WHERE emp_id = '$emp_id'";
    $result = $db->query($txtSQL);
    if ($result) {
        echo "<script type='text/javascript'>";
        echo "alert('ลบข้อมูลสำเร็จ');";
        echo "window.location = 'employee.php'; ";
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
                            <h2>ข้อมูลพนักงาน</h2>
                            <form class="d-sm-flex justify-content-center" name="Search" id="Search">
                                <!-- Default input -->
                                <input type="search" name="txtsearch" id="txtsearch" autocomplete="off" placeholder="ค้นหา" class="form-control">
                                <input type="submit" name="in_search" id="in_search" class="btn btn-primary ml-2 mb-2" value="ค้นหา">
                            </form>
                        </div>
                    </div>
                    <form action="employee.php" method="post" class="form-horizontal">
                        <div class="card border-0 mt-3">
                            <div class="col-md-4 mt-3 ml-1">
                                <a href="employee_form.php" class="btn btn-success">เพิ่มข้อมูลพนักงาน</a>
                            </div>
                            <div class="card-body d-sm-flex justify-content-between table-responsive-md">
                                <table class="table table-bordered" id="table-data">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="text-align:center">ลำดับ</th>
                                            <th style="text-align:center">รูปพนักงาน</th>
                                            <th style="text-align:center">ชื่อ-นามสกุล</th>
                                            <th style="text-align:center">อีเมล</th>
                                            <th style="text-align:center">เบอร์โทรศัพท์</th>
                                            <th style="text-align:center">เพิ่มเติม</th>
                                        </tr>
                                    </thead>
                                    <?php foreach ($result as $data) { ?>
                                        <tr>
                                            <th style="text-align:center"><?php echo $data['emp_id'] ?></th>
                                            <th style="text-align:center"><img src="image_emp/<?php echo $data['emp_img'] ?>" width="100px" height="100px" alt=""></th>
                                            <th style="text-align:center"><?php echo $data['emp_title'].' '.$data['emp_name'].' '.$data['emp_surname']?></th>
                                            <th style="text-align:center"><?php echo $data['emp_email'] ?></th>
                                            <th style="text-align:center"><?php echo $data['emp_tel'] ?></th>
                                            <th style="text-align:center">
                                                <a href="employee_edit.php?id=<?php echo $data['emp_id'] ?>" class="btn btn-secondary">แก้ไข</a>
                                                <a href="?emp_id=<?php echo $data['emp_id'] ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
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
                                        <li class="page-item"><a class="page-link text-dark" href="employee.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                                    <?php } ?>
                                    <li class="page-item" <?php if ($page >= $total_pages) {
                                                                echo "class='disabled'";
                                                            } ?>>
                                        <a class="page-link text-dark" <?php if ($page < $total_pages) {
                                                                            echo "href='?page_no=$next'";
                                                                        } ?>>Next</a>
                                    </li>
                                    <li class="ml-auto">
                                        <h5>ข้อมูลพนักงานทั้งหมด <?= $record . " รายการ " ?> </้h5>
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
                    url: "emp_search.php",
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