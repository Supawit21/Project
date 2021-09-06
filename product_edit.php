<?php
session_start();
require('config/connect.php');
$db = new DB();
/// ตัวแปร id ที่ส่งมากจาก ปุ่มแก้ไข ///
$id =  $_GET['id'];
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if (isset($_SESSION['pos_permit'])) {
//     if (substr($_SESSION['pos_permit'], 8, 1) != 1) {
//         echo "<script type='text/javascript'>";
//         echo "alert('ไม่มีสิทธิ์ในการใช้งาน');";
//         echo "window.location = 'index.php'; ";
//         echo "</script>";
//     }
// }
/// ดึงข้อมูลสินค้าค้ามาแก้ไข ///
$txtSQL   = "SELECT pro_img,pro_name,pro_amount,pro_price,size_type,pro_size,con_unts FROM product 
INNER JOIN product_size ON (product.pro_size=product_size.size_id)
INNER JOIN product_type ON (product_size.size_type=product_type.type_id)
INNER JOIN convent      ON (pro_id=con_product)
INNER JOIN product_unit ON (con_unts=unit_id)
WHERE pro_id = '$id'";
$result   = $db->query($txtSQL);
$row      = mysqli_fetch_array($result);
$row_size   = $row['size_type'];
/// ดึงข้อมูลประเภทสินค้า ///
$txtSQL   = "SELECT type_id,type_name FROM product_type";
$result_type  = $db->query($txtSQL);
/// ดึงข้อมูลขนาดสินค้า ///
$txtSQL    = "SELECT size_id,size_name FROM product_size where size_type = '$row_size'";
$result_size  = $db->query($txtSQL);
/// ดึงข้อมูลหน่วยสินค้า ///
$txtSQL   = "SELECT unit_id,unit_name FROM product_unit";
$result_unit = $db->query($txtSQL);
/// แก้ไขข้อมูลสินค้า ///
if (isset($_POST['pro_edit'])) {
    $pro_name    = $_POST['pro_name'];
    $pro_size    = $_POST['pro_size'];
    $pro_amount  = $_POST['pro_amount'];
    $pro_price   = $_POST['pro_price'];
    $con_unts    = $_POST['pro_unit'];
    // $pro_weight   = $_POST['pro_weight'];
    /// เก็บชื่อไฟล์ ///
    $imgFile = $_FILES['pro_img']['name'];
    $tmp = $_FILES['pro_img']['tmp_name'];
    /// เก็บ path ไฟล์ ///
    $folder = "image_pro/";
    /// check image ///
    if ($imgFile) {
        unlink($folder . $row['pro_img']);
        move_uploaded_file($tmp,$folder.$imgFile);
    } else {
        $imgFile = $row['pro_img'];
    }
    $arr = array(
        "pro_img" => $imgFile,
        "pro_name" => $pro_name,
        "pro_size" => $pro_size,
        "pro_amount" => $pro_amount,
        // "pro_weight" => $pro_weight,
        "pro_price" => $pro_price
    );
    $where_condition = array(
        "pro_id" => $id
    );
    $update = $db->update("product", $arr, $where_condition);
    $arr1 = array(
        "con_unts"    => $con_unts
    );
    $where_condition1 = array(
        "con_product" => $id
    );
    $update_con = $db->update("convent", $arr1, $where_condition1);
    if ($update_con) {
        echo "<script type='text/javascript'>";
        echo "alert('แก้ไขข้อมูลสำเร็จ');";
        echo "window.location = 'product.php'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('แก้ไขข้อมูลไม่สำเร็จ');";
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
                        <!--Card content-->
                        <div class="card-body">
                            <h2>ข้อมูลสินค้า</h2>
                        </div>
                    </div>
                    <form method="post" enctype="multipart/form-data">
                        <div class="card border-0  mt-3">
                            <div class="card-body">
                                <div class="form-group text-right">
                                    <input type="file" id="pro_img" name="pro_img" accept="image/*">
                                    <img id="show" alt="" width="150" height="150" src="image_pro/<?php echo $row['pro_img'] ?>">
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>ประเภทสินค้า</label>
                                        <select name="pro_type" id="pro_type" class="form-control custom-select">
                                            <option selected disabled>เลือกประเภทสินค้า</option>
                                            <?php foreach ($result_type as $value) { ?>
                                                <option value="<?php echo $value['type_id'] ?>" <?php if ($row['size_type'] == $value['type_id']) {
                                                                                                    echo "selected";
                                                                                                } ?>><?php echo $value['type_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>ชื่อสินค้า</label>
                                        <input type="text" name="pro_name" class="form-control" placeholder="ชื่อสินค้า" autocomplete="off" value="<?php echo $row['pro_name'] ?>">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>ขนาดสินค้า</label>
                                        <select name="pro_size" id="pro_size" class="form-control custom-select">
                                            <option>เลือกขนาดสินค้า</option>
                                            <?php foreach ($result_size as $value) { ?>
                                                <option value="<?php echo $value['size_id'] ?>" <?php if ($row['pro_size'] == $value['size_id']) {
                                                                                                    echo "selected";
                                                                                                } ?>><?php echo $value['size_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>จุดสั่งซื้อ</label>
                                        <input type="number" name="pro_amount" class="form-control" placeholder="จำนวน" value="<?php echo $row['pro_amount'] ?>">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>ราคาขาย</label>
                                        <input type="text" name="pro_price" id="pro_price" class="form-control" value="<?php echo $row['pro_price'] ?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>หน่วยสินค้า</label>
                                        <select name="pro_unit" id="pro_unit" class="form-control custom-select">
                                            <option selected disabled>เลือกหน่วยสินค้า</option>
                                            <?php foreach ($result_unit as $value) { ?>
                                                <option value="<?php echo $value['unit_id'] ?>" <?php if ($row['con_unts'] == $value['unit_id']) {
                                                                                                    echo "selected";
                                                                                                } ?>><?php echo $value['unit_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input type="submit" name="pro_edit" id="pro_edit" class="btn btn-primary btn-lg btn-block" value="บันทึกข้อมูล">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <a href="product.php" type="button" class="btn btn-secondary btn-lg btn-block">ยกเลิกข้อมูล</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/script.php') ?>
    <script>
        $(document).ready(function() {
            $('#pro_type').change(function() {
                //console.log('test');
                var type_id = $(this).val();
                $.ajax({
                    type: "post",
                    url: "size.php",
                    data: {
                        id: type_id,
                        function: 'type'
                    },
                    success: function(data) {
                        //console.log(data);
                        $('#pro_size').html(data);
                    }
                });
            });
        });
        var emp_file = document.getElementById('pro_img')
        emp_file.onchange = function() {
            var file = emp_file.files[0];
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function() {
                var result = reader.result;
                document.getElementById('show').src = result;
            }
        }
    </script>
</body>

</html>