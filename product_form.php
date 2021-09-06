<?php
session_start();
require('config/connect.php');
$db = new DB();
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if (isset($_SESSION['pos_permit'])) {
//     if (substr($_SESSION['pos_permit'], 8, 1) != 1) {
//         echo "<script type='text/javascript'>";
//         echo "alert('ไม่มีสิทธิ์ในการใช้งาน');";
//         echo "window.location = 'index.php'; ";
//         echo "</script>";
//     }
// }
/// ดึงข้อมูลประเภทสินค้า ///
$txtSQL    = "SELECT type_id,type_name FROM product_type";
$result_type  = $db->query($txtSQL);
/// ดึงข้อมูลโปรโมชั่น ///
$txtSQL   = "SELECT promo_id,promo_name FROM promotion";
$result_promotion  = $db->query($txtSQL);
/// ดึงข้อมูลหน่วยสินค้า ///
$txtSQL   = "SELECT unit_id,unit_name FROM product_unit";
$result_unit = $db->query($txtSQL);
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
                    <form method="post" action="product_insert.php" enctype="multipart/form-data" novalidate>
                        <div class="card border-0  mt-3">
                            <div class="card-body">
                                <div class="form-group text-right">
                                    <input type="file" id="pro_img" name="pro_img" required accept="image/*">
                                    <img id="show" alt="" width="150" height="150">
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>ประเภทสินค้า</label>
                                        <select name="pro_type" id="pro_type" class="form-control custom-select">
                                            <option selected disabled>เลือกประเภทสินค้า</option>
                                            <?php foreach ($result_type as $value) { ?>
                                                <option value="<?php echo $value['type_id'] ?>"><?php echo $value['type_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span id="combobox"></span>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>ชื่อสินค้า</label>
                                        <input type="text" name="pro_name" id="pro_name" class="form-control" placeholder="ชื่อสินค้า" autocomplete="off">
                                        <span id="validate"></span>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>ขนาดสินค้า</label>
                                        <select name="pro_size" id="pro_size" id="pro_size" class="form-control custom-select">
                                            <option>เลือกขนาดสินค้า</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>จุดสั่งซื้อ</label>
                                        <input type="number" name="pro_amount" class="form-control" placeholder="จำนวน">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>ราคาขาย</label>
                                        <input type="text" name="pro_price" autocomplete="off" class="form-control" placeholder="ราคาขาย">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>หน่วยสินค้า</label>
                                        <select name="pro_unit" id="pro_unit" class="form-control custom-select">
                                            <option selected disabled>เลือกหน่วยสินค้า</option>
                                            <?php foreach ($result_unit as $value) { ?>
                                                <option value="<?php echo $value['unit_id'] ?>"><?php echo $value['unit_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input type="submit" name="pro_insert" id="pro_insert" class="btn btn-primary btn-lg btn-block" value="บันทึกข้อมูล">
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