<?php
session_start();
require('config/connect.php');
$db = new DB ();
/// ตัวแปร id ที่ส่งมากจาก ปุ่มแก้ไข ///
$id_cp = $_GET['id'];
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
/// ดึงข้อมูลราคาทุนมาแก้ไข ///
$txtSQL = "SELECT cp_price,cp_com,cp_pro,com_name,pro_name FROM cost_price INNER JOIN company ON (cost_price.cp_com=company.com_id)                                                                         
           INNER JOIN product ON (cost_price.cp_pro=product.pro_id) WHERE cp_com = '$id_cp'";
$result_update = $db->query($txtSQL);
$row = mysqli_fetch_assoc($result_update);
/// แสดงข้อมูลบริษัทคู่ค้า ///
$txtSQL = "SELECT com_id,com_name FROM company";
$result_company = $db->query($txtSQL);
/// แสดงข้อมูลสินค้า///
$txtSQL = "SELECT pro_id,pro_name,size_name FROM product INNER JOIN product_size ON (pro_size=size_id)";
$result_product = $db->query($txtSQL);
/// แก้ไขราคาทุน ///
if (isset($_POST['cp_edit'])) {
    $cp_com     = $_POST['cp_com'];
    $cp_pro     = $_POST['product'];
    $cp_price   = $_POST['cp_price'];
    // $txtSQL    = "DELETE FROM cost_price WHERE cp_com = '$id_cp'";
    $result_del  = $db->query($txtSQL);
    // if ($result_del) {
    foreach ($cp_pro as $key => $data) {
        $arr = array(
            "cp_pro" => $data,
            "cp_price" => $cp_price[$key]
        );
        $where_condition = array(
            "cp_com" => $cp_com[$key]
        );
        $update = $db->update("cost_price",$arr,$where_condition);
        if ($update) {
            echo "<script type='text/javascript'>";
            echo "alert('แก้ไขข้อมูลสำเร็จ');";
            echo "window.location = 'cost.php'; ";
            echo "</script>";
        } else {
            echo "<script type='text/javascript'>";
            echo "alert('แก้ไขข้อมูลไม่สำเร็จ');";
            echo "</script>";
        }
        }
    // }
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
                        <div class="card-body ">
                            <h2>แก้ไขข้อมูลราคาทุน</h2>
                            <form method="post" class="form-horizontal" name="dep_form">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                <label>บริษัทคู่ค้า</label>
                                    <select name="cp_com" id="cp_com" class="form-control custom-select">
                                            <option value="" selected disabled>เลือกบริษัทคู่ค้า</option>
                                            <?php foreach ($result_company  as $value) { ?>
                                                <option value="<?php echo $value['com_id'] ?>"<?php
                                                                                                    if ($row['cp_com'] == $value['com_id']) {
                                                                                                        echo "selected";
                                                                                                    }
                                                                                                    ?>><?php echo $value['com_name']?></option>
                                            <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6 col-sm-6 col-xs-6" id="insert">
                                    <label>สินค้า</label>
                                    <?php $t = 1 ?>
                                    <?php foreach ($result_update as $value1) { ?>
                                        <div class="input-group control-group">
                                            <select name="product[]" id="product" class="form-control custom-select">
                                                <option value="" selected disabled>เลือกสินค้า</option>
                                                <?php foreach ($result_product as $value) { ?>
                                                    <option value="<?php echo $value['pro_id'] ?>" <?php
                                                                                                    if ($value1['cp_pro'] == $value['pro_id']) {
                                                                                                        echo "selected";
                                                                                                    }
                                                                                                    ?>><?php echo $value['pro_name'].' '.$value['size_name']?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="input-group control-group col-md-4">
                                                <input type="text" name="cp_price[]" class="form-control" placeholder="ราคาต้นทุน" autocomplete="off" value="<?=$value1['cp_price'] ?>">
                                            </div>
                                            <div class="input-group-btn ml-2">
                                            <?php if ($t == 1) { ?>
                                                <button class="btn btn-success add-more" type="button"><i class="fa fa-plus"></i></button>
                                            <?php } else { ?>
                                                <button class="btn btn-danger remove" type="button"><i class="fa fa-minus"></i></button>
                                            <?php } ?>
                                            </div>
                                        </div>
                                    <?php $t++;
                                    } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="submit" name="cp_edit" class="btn btn-primary btn-lg" value="บันทึกข้อมูล">
                                <a href="cost.php" type="button" class="btn btn-secondary btn-lg">ยกเลิกข้อมูล</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
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