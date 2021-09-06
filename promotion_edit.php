<?php
session_start();
require('config/connect.php');
$db = new DB();
/// ตัวแปร id ที่ส่งมากจาก ปุ่มแก้ไข ///
$id =  $_GET['promo_id'];
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if (isset($_SESSION['pos_permit'])) {
//     if (substr($_SESSION['pos_permit'], 10, 1) != 1) {
//         echo "<script type='text/javascript'>";
//         echo "alert('ไม่มีสิทธิ์ในการใช้งาน');";
//         echo "window.location = 'index.php'; ";
//         echo "</script>";
//     }
// }
/// แสดงตารางโปรโมชั่น ///
$txtSQL = "SELECT promo_id,promo_name,promo_start,promo_end,count,list_promo FROM promotion INNER JOIN list_promotion ON (promo_id=list_id) WHERE promo_id = '$id'";
$query_table = $db->query($txtSQL);
$result_promo = mysqli_fetch_array($query_table);
/// ดึงข้อมูลสินค้า ///
$txtSQL = "SELECT pro_id,pro_name,size_name FROM product INNER JOIN product_size ON (pro_size=size_id)";
$result_pro  =  $db->query($txtSQL);
/// แก้ไขข้อมูลโปรโมชั่น ///
if (isset($_POST['promo_edit'])){
    $promo_id = mysqli_real_escape_string($db->conn, $_POST["promo_id"]);
    $promo_name = $_POST['promo_name'];
    $promo_start = $_POST['promo_start'];
    $promo_end = $_POST['promo_end'];
    $count = $_POST['count'];
    $lpromo_pro = $_POST['lpromo_pro'];
    $arr1 = array(
        "promo_name" => $promo_name,
        "promo_start" => $promo_start,
        "promo_end" => $promo_end,
        "count" => $count
    );
    $where_condition = array(
        "promo_id" => $id
    );
    $update = $db->update("promotion", $arr1, $where_condition);
    if ($update) {
        foreach($lpromo_pro as $data){
            $arr2 = array(
                "list_promo" => $data
            );
            $where_condition2 = array(
                "list_id" => $id
            );
            $update2 = $db->update("list_promotion", $arr2, $where_condition2);
            if($update2){
                echo "<script type='text/javascript'>";
                echo "alert('แก้ไขข้อมูลสำเร็จ');";
                echo "window.location = 'promotion.php'; ";
                echo "</script>";
            } else {
                echo "<script type='text/javascript'>";
                echo "alert('แก้ไขข้อมูลไม่สำเร็จ');";
                echo "</script>";
            }
            }
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
                        <div class="card-body">
                            <h2>ข้อมูลโปรโมชั่น</h2>
                        </div>
                    </div>
                    <form method="post">
                        <div class="card border-0  mt-3">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>ชื่อโปรโมชั่น</label>
                                        <input type="text" name="promo_name" class="form-control" placeholder="ชื่อโปรโมชั่น" autocomplete="off" value="<?=$result_promo['promo_name']?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>ส่วนลด%</label>
                                        <input type="text" name="count" class="form-control" placeholder="ส่วนลด%" autocomplete="off" value="<?=$result_promo['count']?>">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>วันที่เริ่มโปรโมชั่น</label>
                                        <input type="date" name="promo_start" class="form-control" value="<?=$result_promo['promo_start']?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>วันที่สิ้นสุดโปรโมชั่น</label>
                                        <input type="date" name="promo_end" class="form-control" value="<?=$result_promo['promo_end']?>">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6" id="insert">
                                        <label>สินค้าที่ร่วมโปรโมชั่น</label>
                                        <div class="input-group control-group">
                                            <select name="lpromo_pro[]" id="search_pro" class="form-control custom-select">
                                                <option value="">กรุณาเลือกสินค้า</option>
                                                <?php foreach ($result_pro as $fetch_pro) { ?>
                                                    <option value="<?= $fetch_pro['pro_id'] ?>"
                                                    <?php
                                                    if($result_promo['list_promo']==$fetch_pro['pro_id']){
                                                    echo "selected";
                                                    }
                                                    ?>><?= $fetch_pro['pro_name'] . ' ' . $fetch_pro['size_name'] ?></option>
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
                                        <input type="submit" name="promo_edit" id="promo_edit" class="btn btn-primary btn-lg btn-block" value="บันทึกข้อมูล">
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