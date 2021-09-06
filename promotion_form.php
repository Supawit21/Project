<?php
session_start();
require('config/connect.php');
$db = new DB();
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if (isset($_SESSION['pos_permit'])) {
//     if (substr($_SESSION['pos_permit'], 10, 1) != 1) {
//         echo "<script type='text/javascript'>";
//         echo "alert('ไม่มีสิทธิ์ในการใช้งาน');";
//         echo "window.location = 'index.php'; ";
//         echo "</script>";
//     }
// }
/// ดึงข้อมูลสินค้า ///
$txtSQL = "SELECT pro_id,pro_name,size_name FROM product INNER JOIN product_size ON (pro_size=size_id)";
$result_pro  =  $db->query($txtSQL);
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
                            <h2>ข้อมูลโปรโมชั่น</h2>
                        </div>
                    </div>
                    <form method="post" action="promotion_insert.php">
                        <div class="card border-0  mt-3">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>ชื่อโปรโมชั่น</label>
                                        <input type="text" name="promo_name" class="form-control" placeholder="ชื่อโปรโมชั่น" autocomplete="off">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>ส่วนลด%</label>
                                        <input type="text" name="count" class="form-control" placeholder="ส่วนลด%" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>วันที่เริ่มโปรโมชั่น</label>
                                        <input type="date" name="promo_start" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>วันที่สิ้นสุดโปรโมชั่น</label>
                                        <input type="date" name="promo_end" class="form-control">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6" id="insert">
                                        <label>สินค้าที่ร่วมโปรโมชั่น</label>
                                        <div class="input-group control-group">
                                            <select name="lpromo_pro[]" id="search_pro" class="form-control custom-select">
                                                <option value="">กรุณาเลือกสินค้า</option>
                                                <?php foreach ($result_pro as $fetch_pro) { ?>
                                                    <option value="<?= $fetch_pro['pro_id'] ?>"><?= $fetch_pro['pro_name'] . ' ' . $fetch_pro['size_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="input-group-btn ml-2">
                                                <button class="btn btn-success add-more" type="button"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input type="submit" name="promo_insert" id="promo_insert" class="btn btn-primary btn-lg btn-block" value="บันทึกข้อมูล">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <a href="promotion.php" type="button" class="btn btn-secondary btn-lg btn-block">ยกเลิกข้อมูล</a>
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
            var p = 1;
            $(".add-more").click(function(e) {
                e.preventDefault();
                p++;
                var add_promotion = '<div class="control-group input-group delete">';
                add_promotion += '<select name="lpromo_pro[]" id="search_pro" class="form-control custom-select">';
                add_promotion += '<option value="">กรุณาเลือกสินค้า</option>';
                add_promotion += '<?php foreach ($result_pro as $fetch_pro) { ?>';
                add_promotion += '<option value="<?= $fetch_pro['pro_id'] ?>"><?= $fetch_pro['pro_name'] . ' ' . $fetch_pro['size_name'] ?></option>';
                add_promotion += '<?php } ?>';
                add_promotion += '</select>';
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