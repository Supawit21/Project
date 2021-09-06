<?php
session_start();
require('config/connect.php');
$db = new DB();
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if(isset($_SESSION['pos_permit']))
// {
//     if(substr($_SESSION['pos_permit'],3,1) != 1){
//         echo "<script type='text/javascript'>";
//         echo "alert('ไม่มีสิทธิ์ในการใช้งาน');";
//         echo "window.location = 'index.php'; ";
//         echo "</script>";
//     } 
// }
/// ตัวแปร id ที่ส่งมากจาก ปุ่มแก้ไข ///
$id =  $_GET['id'];
/// ดึงข้อมูลลูกค้ามาแก้ไข ///
$txtSQL = "SELECT cus_title,cus_name,cus_surname,cus_email,cus_add,cus_zip,id_dis,DISTRICT_NAME,ID_AMPHUR,AMPHUR_NAME,ID_PROVINCE,PROVINCE_NAME FROM customer 
INNER JOIN zipcode     ON (customer.id_dis=zipcode.ID_DISTRICT)
INNER JOIN district    ON (zipcode.ID_DISTRICT=district.DISTRICT_ID)
INNER JOIN amphur      ON (district.ID_AMPHUR=amphur.AMPHUR_ID)
INNER JOIN province    ON (amphur.ID_PROVINCE=PROVINCE.PROVINCE_ID) WHERE cus_id = '$id'";
$result = $db->query($txtSQL);
$row    = mysqli_fetch_array($result);
/// check เงื่อนไขตอนแก้ไข ///
$row_province = $row['ID_PROVINCE'];
$row_amphur   = $row['ID_AMPHUR'];
/// ดึงข้อมูลจังหวัด ///
$txtSQL = "SELECT PROVINCE_ID,PROVINCE_NAME FROM province ORDER BY PROVINCE_NAME asc";
$query_province = $db->query($txtSQL);
/// ดึงข้อมูลตำบล ///
$txtSQL = "SELECT DISTRICT_ID,DISTRICT_NAME FROM district WHERE ID_AMPHUR = '$row_amphur'";
$query_dis = $db->query($txtSQL);
/// ดึงข้อมูลอำเภอ ///
$txtSQL = "SELECT AMPHUR_ID,AMPHUR_NAME FROM amphur WHERE ID_PROVINCE = '$row_province'";
$query_amp = $db->query($txtSQL);
/// แก้ไขข้อมูลลูกค้า ///
if(isset($_POST['cus_edit'])){
    $cus_title   = $_POST['cus_title'];
    $cus_name    = $_POST['cus_name'];
    $cus_surname = $_POST['cus_surname'];
    $cus_email   = $_POST['cus_email'];
    $cus_add     = $_POST['cus_add'];
    $id_dis      = $_POST['district'];
    $cus_zip     = $_POST['zipcode'];
    $cus_tel     = $_POST['cus_tel'];
    $arr = array(
        "cus_title" => $cus_title,
        "cus_name" => $cus_name,
        "cus_surname" => $cus_surname,
        "cus_email" => $cus_email,
        "cus_add" => $cus_add,
        "id_dis" => $id_dis,
        "cus_zip" => $cus_zip
    );
    $where_condition = array(
        "cus_id" => $id
    );
    $update = $db->update("customer", $arr, $where_condition);
    ///
    if($update){
        $txtSQL = "DELETE FROM customer_tel WHERE cus_id = '$id'";
        $result = $db->query($txtSQL);
        foreach ($cus_tel as  $tel) {
            $arr1 = array(
                "cus_id" => $id,
                "cus_tel" => $tel
            );
            $update_all = $db->insert("customer_tel", $arr1);
            if ($update_all) {
                echo "<script type='text/javascript'>";
                echo "alert('แก้ไขข้อมูลสำเร็จ');";
                echo "window.location = 'customer.php'; ";
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
                        <!--Card content-->
                        <div class="card-body">
                            <h2>ข้อมูลลูกค้า</h2>
                        </div>
                    </div>
                    <form method="post">
                        <div class="card border-0  mt-3">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="from-group col-md-2">
                                        <label>คำนำหน้า</label>
                                        <select name="cus_title" class="form-control custom-select">
                                            <option selected disabled>คำนำหน้า</option>
                                            <option value="นาย"
                                            <?php 
                                            if($row['cus_title']=="นาย"){
                                                echo "selected";
                                            }
                                            ?>>นาย</option>
                                            <option value="นาง"
                                            <?php 
                                            if($row['cus_title']=="นาง"){
                                                echo "selected";
                                            }
                                            ?>>นาง</option>
                                            <option value="นางสาว"
                                            <?php 
                                            if($row['cus_title']=="นางสาว"){
                                                echo "selected";
                                            }
                                            ?>>นางสาว</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label>ชื่อ</label>
                                        <input type="text" name="cus_name" class="form-control" placeholder="ชื่อ" value="<?php echo $row['cus_name']?>" autocomplete="off">
                                        <span id="validate"></span>
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label>นามสกุล</label>
                                        <input type="text" name="cus_surname" class="form-control" placeholder="นามสกุล" value="<?php echo $row['cus_surname']?>" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-row">
                                <div class="form-group col-md-7">
                                    <label>อีเมล</label>
                                    <input type="email" name="cus_email" id="cus_email" class="form-control" placeholder="อีเมล" autocomplete="off" value="<?=$row['cus_email']?>">
                                    <span id="validate_email"></span>
                                </div>
                                    <div class="form-group col-md-5" id="insert">
                                        <label>เบอร์โทรศัพท์</label>
                                        <?php
                                        $txtSQL = "SELECT cus_tel FROM customer_tel WHERE cus_id = '$id'";
                                        $query_tel = $db->query($txtSQL);
                                        $t = 1;
                                        foreach ($query_tel as $phone) { ?>
                                        <div class="input-group control-group">
                                            <input type="text" name="cus_tel[]" placeholder="เบอร์โทรศัพท์" class="form-control name_list" autocomplete="off" value="<?php echo $phone['cus_tel'] ?>">
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
                                <h2>ข้อมูลที่อยู่</h2>
                                <div class="form-group">
                                    <label>ที่อยู่</label>
                                    <textarea class="form-control" name="cus_add" rows="4"><?php echo $row['cus_add']?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>จังหวัด</label>
                                    <select name="province" id="province" class="form-control custom-select">
                                        <option selected disabled>จังหวัด</option>
                                        <?php foreach ($query_province as $value) { ?>
                                            <option value="<?php echo $value['PROVINCE_ID'] ?>"
                                            <?php
                                                if($row['ID_PROVINCE']==$value['PROVINCE_ID']){
                                                    echo "selected";
                                                }
                                            ?>
                                            ><?php echo $value['PROVINCE_NAME'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>อำเภอ</label>
                                    <select name="amphur" id="amphur" class="form-control custom-select">
                                    <option selected disabled>อำเภอ</option>
                                        <?php foreach ($query_amp as $value) { ?>
                                            <option value="<?php echo $value['AMPHUR_ID'] ?>"
                                            <?php
                                                if($row['ID_AMPHUR']==$value['AMPHUR_ID']){
                                                    echo "selected";
                                                }
                                            ?>
                                            ><?php echo $value['AMPHUR_NAME'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>ตำบล</label>
                                    <select name="district" id="district" class="form-control custom-select">
                                    <option selected disabled>ตำบล</option>
                                        <?php foreach ($query_dis as $value) { ?>
                                            <option value="<?php echo $value['DISTRICT_ID'] ?>"
                                            <?php
                                                if($row['id_dis']==$value['DISTRICT_ID']){
                                                    echo "selected";
                                                }
                                            ?>
                                            ><?php echo $value['DISTRICT_NAME'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>รหัสไปรษณีย์</label>
                                    <input type="text" name="zipcode" id="zipcode" class="form-control" value="<?php echo $row['cus_zip']?>" readonly>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input type="submit" name="cus_edit" id="cus_edit" class="btn btn-primary btn-lg btn-block" value="บันทึกข้อมูล">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <a href="customer.php" type="button" class="btn btn-secondary btn-lg btn-block">ยกเลิกข้อมูล</a>
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
    <script src="js/dropdown.js"></script>
    <script>
        $(document).ready(function() {
            var p = 1;
            $(".add-more").click(function(e) {
                e.preventDefault();
                p++;
                var add_tel = '<div class="control-group input-group delete">';
                add_tel += '<input type="text" name="cus_tel[]" placeholder="เบอร์โทรศัพท์" class="form-control name_list" autocomplete="off">';
                add_tel += '<div class="input-group-btn ml-2">';
                add_tel += '<button class="btn btn-danger remove" type="button"><i class="fa fa-minus"></i></button>';
                add_tel += '</div>';
                add_tel += '</div>';
                $('#insert').append(add_tel);
            });
            $('#insert').on("click", ".remove", function() {
                $(this).parents(".delete").remove();
                p--;
            });
        });
        //     var status = false;
        //     $('#cus_name').blur(function() {
        //         var cus_name = $('#cus_name').val();
        //         if (cus_name == "") {
        //             $('#validate').html("กรุณากรอกข้อมูลให้เรียบร้อย");
        //             $('#validate').addClass("text-danger");
        //             /// remove class ///
        //             $('#validate').removeClass("text-success");
        //             status = false;
        //             return;
        //         }
        //         $.ajax({
        //             type: "post",
        //             url: "check_cus.php",
        //             data: {
        //                 cus_name: cus_name
        //             },
        //             success: function(data) {
        //                 if (data != '0') {
        //                     $('#validate').html("ไม่สามารถใช้ชื่อนี้ได้");
        //                     $('#validate').addClass("text-danger");
        //                     /// remove class ///
        //                     $('#validate').removeClass("text-success");
        //                     status = false;
        //                 } else {
        //                     $('#validate').html("สามารถใช้ชื่อนี้ได้");
        //                     $('#validate').addClass("text-success");
        //                     /// remove class ///
        //                     $('#validate').removeClass("text-danger");
        //                     status = true;
        //                 }
        //             }
        //         });
        //     });
        //     $('#cus_email').blur(function() {
        //         var email = $('#cus_email').val();
        //         if (email == "") {
        //             $('#validate_email').html("กรุณากรอกข้อมูลให้เรียบร้อย");
        //             $('#validate_email').addClass("text-danger");
        //             /// remove class ///
        //             $('#validate_email').removeClass("text-success");
        //             status = false;
        //             return;
        //         }
        //         $.ajax({
        //             type: "post",
        //             url: "check_cus.php",
        //             data: {
        //                 cus_email: email
        //             },
        //             success: function(data) {
        //                 var result = IsEmail(email);
        //                 if (result == false) {
        //                     $('#validate_email').html('อีเมลไม่ถูกต้อง');
        //                     $('#validate_email').addClass('text-danger');
        //                     /// remove class ///
        //                     $('#validate_email').removeClass('text-success');
        //                     status = false;
        //                 } else if (data != '0') {
        //                     $('#validate_email').html('มีอีเมลนี้ในระบบแล้ว');
        //                     $('#validate_email').addClass('text-danger');
        //                     /// remove class ///
        //                     $('#validate_email').removeClass('text-success');
        //                     status = false;
        //                 } else {
        //                     $('#validate_email').html('อีเมลถูกต้อง');
        //                     $('#validate_email').addClass('text-success');
        //                     /// remove class ///
        //                     $('#validate_email').removeClass('text-danger');
        //                     status = true;
        //                 }
        //             }
        //         })
        //     });
        //     $('#cus_insert').click(function(e) {
        //         if (status == false) {
        //             e.preventDefault();
        //             $('#validate').html("กรุณากรอกข้อมูลให้เรียบร้อย");
        //             $('#validate').addClass("text-danger");
        //             $('#validate').removeClass("text-success");
        //         }
        //     });
        // });

        // function IsEmail(emp_email) {
        //     var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        //     if (!regex.test(emp_email)) {
        //         return false;
        //     } else {
        //         return true;
        //     }
        // }

        // function number() {
        //     key = event.keyCode
        //     if (key < 48 || key > 57)
        //         event.returnValue = false;
        // }
    </script>
</body>

</html>