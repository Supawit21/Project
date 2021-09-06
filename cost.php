<?php
session_start();
require('config/connect.php');
$db = new DB ();
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if (isset($_SESSION['pos_permit'])) {
//     if (substr($_SESSION['pos_permit'], 9, 1) != 1) {
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
$txtSQL = "SELECT count(cp_com) total FROM cost_price where status_co = 1";
$result = $db->query($txtSQL);
$fetch_rec  = mysqli_fetch_array($result);
$record  = $fetch_rec['total'];
$total_pages   = ceil($record / $limit);
/// แสดงตารางราคาทุน ///
$txtSQL = "SELECT cp_price,cp_com,cp_pro,com_name,pro_name,size_name FROM cost_price 
           INNER JOIN company ON (cost_price.cp_com=company.com_id) 
           INNER JOIN product ON (cost_price.cp_pro=product.pro_id) 
           INNER JOIN product_size ON (pro_size=size_id) where status_co = 1 GROUP BY cp_com LIMIT $start, $limit";
$result_cost  = $db->query($txtSQL);
/// แสดงข้อมูลบริษัทคู่ค้า ///
$txtSQL = "SELECT com_id,com_name FROM company";
$result_company = $db->query($txtSQL);
/// แสดงข้อมูลสินค้า///
$txtSQL = "SELECT pro_id,pro_name,size_name FROM product INNER JOIN product_size ON (pro_size=size_id)";
$result_product = $db->query($txtSQL);
/// บันทึกราคาทุน ///
if (isset($_POST['cp_insert'])) {
    $cp_com     = $_POST['cp_com'];
    $cp_pro     = $_POST['product'];
    $cp_price   = $_POST['cp_price'];
    foreach ($cp_pro as $key => $data) {
        $arr = array(
            "cp_com" => $cp_com,
            "cp_pro" => $data,
            "cp_price" => $cp_price[$key]
        );
        $insert = $db->insert("cost_price",$arr);
        if ($insert) {
            echo "<script type='text/javascript'>";
            echo "alert('เพิ่มข้อมูลสำเร็จ');";
            echo "window.location = 'cost.php'; ";
            echo "</script>";
        } else {
            echo "<script type='text/javascript'>";
            echo "alert('เพิ่มข้อมูลไม่สำเร็จ');";
            echo "</script>";
        }
    }
}
/// ลบข้อมูลราคาทุน ///
if (isset($_GET['cp_com'])) {
    $cp_com = $_GET['cp_com'];
    $sql = "UPDATE cost_price SET status_pro = 0 WHERE cp_com = '$cp_com'";
    $query = mysqli_query($conn, $sql);
    if ($query) {
        echo "<script type='text/javascript'>";
        echo "alert('ลบข้อมูลสำเร็จ');";
        echo "window.location = 'cost.php'; ";
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
                        <h2>ข้อมูลราคาทุน</h2>
                        <form class="d-sm-flex justify-content-center" name="Search" id="Search">
                            <input type="search" name="txtsearch" id="txtsearch" autocomplete="off" placeholder="ค้นหา" class="form-control">
                            <input type="submit" name="in_search" id="in_search" class="btn btn-primary ml-2 mb-2" value="ค้นหา">
                        </form>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body d-sm-flex justify-content-between">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align:center">ชื่อบริษัทคู่ค้า</th>
                                    <th style="text-align:center">ชื่อสินค้า</th>
                                    <th style="text-align:center">ราคาต้นทุน</th>
                                    <th style="text-align:center">เพิ่มเติม</th>
                                </tr>
                            </thead>
                            <?php foreach ($result_cost as $data) { ?>
                                <tr>
                                    <th style="text-align:center"><?php echo $data['com_name'] ?></th>
                                    <th style="text-align:center"><?php echo $data['pro_name'].' '.$data['size_name']?></th>
                                    <th style="text-align:center"><?php echo $data['cp_price'] ?></th>
                                    <th style="text-align:center">
                                        <a href="cost_edit.php?id=<?php echo $data['cp_com']; ?>" class="btn btn-secondary">แก้ไข</a>
                                        <a href="?cp_com=<?php echo $data['cp_com']; ?>" class="btn btn-danger" onclick="return confirm('ต้องการลบข้อมูลใช่หรือไม่')">ลบ</a>
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
                                <li class="page-item"><a class="page-link text-dark" href="cost.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                            <?php } ?>
                            <li class="page-item" <?php if ($page >= $total_pages) {
                                                        echo "class='disabled'";
                                                    } ?>>
                                <a class="page-link text-dark" <?php if ($page < $total_pages) {
                                                                    echo "href='?page_no=$next'";
                                                                } ?>>Next</a>
                            </li>
                            <li class="ml-auto">
                                <h5>ข้อมูลราคาทุนสินค้าทั้งหมด <?= $record . " รายการ " ?> </้h5>
                            </li>
                        </ul>
                    </nav>
                </div>
                <form method="post" class="form-horizontal" name="dep_form" id="dep_form1" novalidate>
                    <div class="card border-0 mt-3">
                        <div class="card-body ">
                            <h2>เพิ่มข้อมูลราคาทุน</h2>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>บริษัทคู่ค้า</label>
                                    <select name="cp_com" id="cp_com" class="form-control custom-select">
                                            <option value="" selected disabled>เลือกบริษัทคู่ค้า</option>
                                            <?php foreach ($result_company  as $value) { ?>
                                                <option value="<?php echo $value['com_id'] ?>"><?php echo $value['com_name']?></option>
                                            <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6 col-sm-6 col-xs-6" id="insert">
                                    <label>สินค้า</label>
                                    <div class="input-group control-group">
                                        <select name="product[]" id="product" class="form-control custom-select">
                                            <option value="" selected disabled>เลือกสินค้า</option>
                                            <?php foreach ($result_product  as $value) { ?>
                                                <option value="<?php echo $value['pro_id'] ?>"><?php echo $value['pro_name'].' '.$value['size_name']?></option>
                                            <?php } ?>
                                        </select>
                                        <div class="input-group control-group col-md-4">
                                            <input type="text" name="cp_price[]" class="form-control" placeholder="ราคาต้นทุน" autocomplete="off">
                                        </div>
                                        <div class="input-group-btn ml-2">
                                            <button class="btn btn-success add-more" type="button"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="submit" name="cp_insert" class="btn btn-primary btn-lg" value="บันทึกข้อมูล">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include('includes/script.php') ?>
    <script>
        $(function() {
            $('#Search').submit(function(e) {
                e.preventDefault();
                var txt = $('#txtsearch').val();
                $.ajax({
                    type: "post",
                    url: "cos_search.php",
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
                })
            });
            var p = 1;
            $(".add-more").click(function(e) {
                e.preventDefault();
                p++;
                var add_promotion = '<div class="control-group input-group delete">';
                add_promotion += '<select name="product[]" id="product" class="form-control custom-select">';
                add_promotion += '<option value="" selected disabled>เลือกสินค้า</option>';
                add_promotion += '<?php foreach ($result_product as $value) { ?>';
                add_promotion += '<option value="<?php echo $value['pro_id'] ?>"><?php echo $value['pro_name'].' '.$value['size_name']?></option>';
                add_promotion += '<?php } ?>';
                add_promotion += '</select>';
                add_promotion += '<div class="input-group control-group col-md-4">';
                add_promotion += '<input type="text" name="cp_price[]" class="form-control" placeholder="ราคาต้นทุน">';
                add_promotion += '</div>';
                add_promotion += '<div class="input-group-btn ml-2">';
                add_promotion += '<button class="btn btn-danger remove" type="button"><i class="fa fa-minus"></i></button>';
                add_promotion += '</div>';
                add_promotion += '</div>';
                $('#insert').append(add_promotion);
            });
            $('#insert').on("click", ".remove", function() {
                $(this).parents(".delete").remove();
                p--;
            });
        });
    </script>
</body>

</html>