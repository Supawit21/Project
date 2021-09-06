<?php
session_start();
require('config/connect.php');
$db = new DB();
/// ตัวแปร id ที่ส่งมากจาก ปุ่มแก้ไข ///
$id =  $_GET['id'];
/// เช็คสิทธิ์การเข้าใช้งาน ///
// if (isset($_SESSION['pos_permit'])) {
//     if (substr($_SESSION['pos_permit'], 2, 1) != 1) {
//         echo "<script type='text/javascript'>";
//         echo "alert('ไม่มีสิทธิ์ในการใช้งาน');";
//         echo "window.location = 'index.php'; ";
//         echo "</script>";
//     }
// }
/// ดึงข้อมูลพนักงานมาแก้ไข ///
$txtSQL = "SELECT emp_img,emp_title,emp_name,emp_surname,emp_nic,emp_bdate,emp_iden,emp_nation,emp_email,emp_gen,emp_add,emp_zip,id_dis,emp_user,emp_pass,emp_pos,nat_name,DISTRICT_NAME,ID_AMPHUR,AMPHUR_NAME,ID_PROVINCE,PROVINCE_NAME,pos_name,pos_dep,dep_name FROM employee INNER JOIN nationality ON (employee.emp_nation=nationality.nat_id) 
INNER JOIN zipcode     ON (employee.id_dis=zipcode.ID_DISTRICT)
INNER JOIN district    ON (zipcode.ID_DISTRICT=district.DISTRICT_ID)
INNER JOIN amphur      ON (district.ID_AMPHUR=amphur.AMPHUR_ID)
INNER JOIN province    ON (amphur.ID_PROVINCE=PROVINCE.PROVINCE_ID)	 
INNER JOIN position    ON (employee.emp_pos=position.pos_id)
INNER JOIN department  ON (position.pos_dep=department.dep_id) WHERE emp_id = '$id'";
$result = $db->query($txtSQL);
$row    = mysqli_fetch_array($result);
/// check เงื่อนไขตอนแก้ไข ///
$row_province = $row['ID_PROVINCE'];
$row_amphur   = $row['ID_AMPHUR'];
/// ดึงข้อมูลแผนก ///
$txtSQL = "SELECT dep_id,dep_name FROM department";
$query_dep = $db->query($txtSQL);
/// ดึงข้อมูลตำแหน่ง ///
$txtSQL = "SELECT pos_id,pos_name FROM position";
$query_pos = $db->query($txtSQL);
/// ดึงข้อมูลจังหวัด ///
$txtSQL = "SELECT PROVINCE_ID,PROVINCE_NAME FROM province ORDER BY PROVINCE_NAME asc";
$query_province = $db->query($txtSQL);
/// ดึงข้อมูลตำบล ///
$txtSQL = "SELECT DISTRICT_ID,DISTRICT_NAME FROM district WHERE ID_AMPHUR = '$row_amphur'";
$query_dis = $db->query($txtSQL);
/// ดึงข้อมูลอำเภอ ///
$txtSQL = "SELECT AMPHUR_ID,AMPHUR_NAME FROM amphur WHERE ID_PROVINCE = '$row_province'";
$query_amp = $db->query($txtSQL);
/// ดึงข้อมูลสัญชาติ ///
$txtSQL = "SELECT nat_id,nat_name FROM nationality";
$result_nat = $db->query($txtSQL);
/// แก้ไขข้อมูลพนักงาน ///
if (isset($_POST['emp_edit'])) {
    $emp_title   = $_POST['emp_title'];
    $emp_name    = $_POST['emp_name'];
    $emp_surname = $_POST['emp_surname'];
    $emp_nic     = $_POST['emp_nic'];
    $emp_nation  = $_POST['emp_nat'];
    $emp_bdate   = $_POST['emp_bdate'];
    $emp_iden    = $_POST['emp_iden'];
    $emp_email   = $_POST['emp_email'];
    $emp_gen     = $_POST['emp_gen'];
    $emp_add     = $_POST['emp_add'];
    $emp_zip     = $_POST['zipcode'];
    $id_dis      = $_POST['district'];
    $emp_pos     = $_POST['emp_pos'];
    $emp_user    = $_POST['emp_user'];
    $emp_pass    = $_POST['emp_pass'];
    $emp_tel     = $_POST['emp_tel'];
    /// เก็บชื่อไฟล์ ///
    $imgFile = $_FILES['emp_img']['name'];
    $tmp = $_FILES['emp_img']['tmp_name'];
    /// เก็บ path ไฟล์ ///
    $folder = "image_emp/";
    /// check image ///
    if ($imgFile) {
        unlink($folder.$row['emp_img']);
        move_uploaded_file($tmp,$folder.$imgFile);
    } else {
        $imgFile = $row['emp_img'];
    }
    $arr = array(
        "emp_img" => $imgFile,
        "emp_title" => $emp_title,
        "emp_name" => $emp_name,
        "emp_surname" => $emp_surname,
        "emp_nic" => $emp_nic,
        "emp_bdate" => $emp_bdate,
        "emp_iden" => $emp_iden,
        "emp_nation" => $emp_nation,
        "emp_email" => $emp_email,
        "emp_gen" => $emp_gen,
        "emp_add" => $emp_add,
        "emp_zip" => $emp_zip,
        "id_dis" => $id_dis,
        "emp_pos" => $emp_pos,
        "emp_user" => $emp_user,
        "emp_pass" => $emp_pass
   );
   $where_condition = array(
        "emp_id" => $id
   );
   $update = $db->update("employee", $arr, $where_condition);
        ///
        if ($update) {
            $txtSQL = "DELETE FROM employee_tel WHERE emp_id = '$id'";
            $result = $db->query($txtSQL);
            foreach ($emp_tel as  $tel) {
                $arr1 = array(
                    "emp_id" => $id,
                    "emp_tel" => $tel
                );
                $edit_all = $db->insert("employee_tel", $arr1);
                if ($edit_all) {
                    echo "<script type='text/javascript'>";
                    echo "alert('แก้ไขข้อมูลสำเร็จ');";
                    echo "window.location = 'employee.php'; ";
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
                            <h2>ข้อมูลพนักงาน</h2>
                        </div>
                    </div>
                    <form name="txt_emp" id="emp_form" method="post" enctype="multipart/form-data" novalidate>
                        <div class="card border-0  mt-3">
                            <div class="card-body">
                                <div class="form-group text-right">
                                    <input type="file" id="emp_img" name="emp_img" accept="image/*">
                                    <img id="show" alt="" width="150" height="150" src="image_emp/<?php echo $row['emp_img'] ?>">
                                </div>
                                <div class="form-row">
                                    <div class="from-group col-md-2">
                                        <label>คำนำหน้า</label>
                                        <select name="emp_title" class="form-control custom-select">
                                            <option selected disabled>คำนำหน้า</option>
                                            <option value="นาย" <?php
                                                                if ($row['emp_title'] == "นาย") {
                                                                    echo "selected";
                                                                }
                                                                ?>>นาย</option>
                                            <option value="นาง" <?php
                                                                if ($row['emp_title'] == "นาง") {
                                                                    echo "selected";
                                                                }
                                                                ?>>นาง</option>
                                            <option value="นางสาว" <?php
                                                                    if ($row['emp_title'] == "นางสาว") {
                                                                        echo "selected";
                                                                    }
                                                                    ?>>นางสาว</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label>ชื่อ</label>
                                        <input type="text" name="emp_name" class="form-control" value="<?php echo $row['emp_name'] ?>" pattern="^[ก-๏\s]+$" required>
                                        <span id="validate"></span>
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label>นามสกุล</label>
                                        <input type="text" name="emp_surname" class="form-control" value="<?php echo $row['emp_surname'] ?>" pattern="^[ก-๏\s]+$" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label>ชื่อเล่น</label>
                                        <input type="text" name="emp_nic" class="form-control" value="<?php echo $row['emp_nic'] ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>สัญชาติ</label>
                                        <select name="emp_nat" id="emp_nat" class="form-control custom-select" required>
                                            <option value="">กรุณาเลือกสัญชาติ</option>
                                            <?php foreach ($result_nat as $value) { ?>
                                                <option value="<?php echo $value['nat_id'] ?>" <?php
                                                                                                if ($row['emp_nation'] == $value['nat_id']) {
                                                                                                    echo "selected";
                                                                                                }
                                                                                                ?>>
                                                    <?php echo $value['nat_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>วัน/เดือน/ปีเกิด</label>
                                        <input type="date" name="emp_bdate" class="form-control" value="<?php echo $row['emp_bdate'] ?>">
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label>รหัสประชาชน</label>
                                        <input type="text" name="emp_iden" class="form-control" value="<?php echo $row['emp_iden'] ?>">
                                        <!-- <span id="error"></span> -->
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>อีเมล</label>
                                        <input type="email" name="emp_email" class="form-control" value="<?php echo $row['emp_email'] ?>">
                                        <!-- <span id="validate_email"></span> -->
                                    </div>
                                    <div class="form-group col-md-4" id="insert">
                                        <label>เบอร์โทรศัพท์</label>
                                        <?php
                                        $txtSQL = "SELECT emp_tel FROM employee_tel WHERE emp_id = '$id'";
                                        $query_tel = $db->query($txtSQL);
                                        $t = 1;
                                        foreach ($query_tel as $phone) { ?>
                                            <div class="input-group control-group">
                                                <input type="text" name="emp_tel[]" placeholder="เบอร์โทรศัพท์" class="form-control name_list" value="<?php echo $phone['emp_tel'] ?>">
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
                                    <div class="form-group col-md-4">
                                        <label>เพศ</label>
                                        <div class="mt-3">
                                            <input type="radio" name="emp_gen" value="ชาย" <?php
                                                                                            if ($row['emp_gen'] == "ชาย") {
                                                                                                echo "checked";
                                                                                            }
                                                                                            ?>>&nbsp;ชาย&nbsp;&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="emp_gen" value="หญิง" <?php
                                                                                                                    if ($row['emp_gen'] == "หญิง") {
                                                                                                                        echo "checked";
                                                                                                                    }
                                                                                                                    ?>>&nbsp;หญิง
                                        </div>
                                    </div>

                                </div>
                                <h2>ข้อมูลที่อยู่</h2>
                                <div class="form-group">
                                    <label>ที่อยู่</label>
                                    <textarea class="form-control" name="emp_add" rows="4"><?php echo $row['emp_add'] ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>จังหวัด</label>
                                    <select name="province" id="province" class="form-control custom-select">
                                        <option selected disabled>กรุณาเลือกจังหวัด</option>
                                        <?php foreach ($query_province as $value) { ?>
                                            <option value="<?php echo $value['PROVINCE_ID'] ?>" <?php
                                                                                                if ($row['ID_PROVINCE'] == $value['PROVINCE_ID']) {
                                                                                                    echo "selected";
                                                                                                }
                                                                                                ?>><?php echo $value['PROVINCE_NAME'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>อำเภอ</label>
                                    <select name="amphur" id="amphur" class="form-control custom-select">
                                        <option selected disabled>กรุณาเลือกอำเภอ</option>
                                        <?php foreach ($query_amp as $value) { ?>
                                            <option value="<?php echo $value['AMPHUR_ID'] ?>" <?php
                                                                                                if ($row['ID_AMPHUR'] == $value['AMPHUR_ID']) {
                                                                                                    echo "selected";
                                                                                                }
                                                                                                ?>><?php echo $value['AMPHUR_NAME'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>ตำบล</label>
                                    <select name="district" id="district" class="form-control custom-select">
                                        <option selected disabled>กรุณาเลือกตำบล</option>
                                        <?php foreach ($query_dis as $value) { ?>
                                            <option value="<?php echo $value['DISTRICT_ID'] ?>" <?php
                                                                                                if ($row['id_dis'] == $value['DISTRICT_ID']) {
                                                                                                    echo "selected";
                                                                                                }
                                                                                                ?>><?php echo $value['DISTRICT_NAME'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>รหัสไปรษณีย์</label>
                                    <input type="text" name="zipcode" id="zipcode" class="form-control" value="<?php echo $row['emp_zip'] ?>" readonly>
                                </div>
                                <h2>ข้อมูลบริษัท</h2>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>แผนก</label>
                                        <select name="department" id="department" class="form-control custom-select">
                                            <option selected disabled>กรุณาเลือกแผนก</option>
                                            <?php foreach ($query_dep as $value) { ?>
                                                <option value="<?php echo $value['dep_id'] ?>" <?php
                                                                                                if ($row['pos_dep'] == $value['dep_id']) {
                                                                                                    echo "selected";
                                                                                                }
                                                                                                ?>><?php echo $value['dep_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>ตำแหน่ง</label>
                                        <select name="emp_pos" id="emp_pos" class="form-control custom-select">
                                            <option selected disabled>กรุณาเลือกตำแหน่ง</option>
                                            <?php foreach ($query_pos as $value) { ?>
                                                <option value="<?php echo $value['pos_id'] ?>" <?php
                                                                                                if ($row['emp_pos'] == $value['pos_id']) {
                                                                                                    echo "selected";
                                                                                                }
                                                                                                ?>><?php echo $value['pos_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <h2>ข้อมูลการเข้าใช้งาน</h2>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>ชื่อผู้เข้าใช้งาน</label>
                                        <input type="text" name="emp_user" id="emp_user" autocomplete="off" class="form-control" value="<?php echo $row['emp_user'] ?>" placeholder="ชื่อผู้เข้าใช้งาน" required>
                                        <span id="validate_user"></span>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>รหัสผ่าน</label>
                                        <input type="password" name="emp_pass" id="emp_pass" class="form-control" value="<?php echo $row['emp_pass'] ?>" placeholder="รหัสผ่าน" required>
                                        <span id="validate_pass"></span>
                                    </div>
                                    <!-- <div class="form-group col-md-4">
                                        <label>ยืนยันรหัสผ่าน</label>
                                        <input type="password" name="emp_con" id="emp_con" class="form-control" placeholder="ยืนยันรหัสผ่าน">
                                        <span id="validate_check"></span>
                                    </div> -->
                                    <div class="form-group col-md-6">
                                        <input type="submit" name="emp_edit" id="emp_edit" class="btn btn-primary btn-lg btn-block" value="บันทึกข้อมูล">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <a href="employee.php" type="button" class="btn btn-secondary btn-lg btn-block">ยกเลิกข้อมูล</a>
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
                add_tel += '<input type="text" name="emp_tel[]" class="form-control" placeholder="เบอร์โทรศัพท์" autocomplete="off">';
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
            $('#emp_title').change(function() {
                var emp_title = $('#emp_title').val()
                if (emp_title == "นาย") {
                    $('#emp_female').attr('disabled', true);
                    $('#emp_male').attr('disabled', false);
                } else if (emp_title == "นาง" || emp_title == "นางสาว") {
                    $('#emp_male').attr('disabled', true);
                    $('#emp_female').attr('disabled', false);
                }
            });
        });
        var emp_file = document.getElementById('emp_img')
        emp_file.onchange = function() {
            var file = emp_file.files[0];
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function() {
                var result = reader.result;
                document.getElementById('show').src = result;
            }
        }
        //     $('#emp_name').blur(function() {
        //         var emp_name = $('#emp_name').val();
        //         if (emp_name == "") {
        //             $('#validate').html("กรุณากรอกข้อมูลให้เรียบร้อย");
        //             $('#validate').addClass("text-danger");
        //             /// remove class ///
        //             $('#validate').removeClass("text-success");
        //             status = false;
        //             return;
        //         }
        //         $.ajax({
        //             type: "post",
        //             url: "check_emp.php",
        //             data: {
        //                 emp_name: emp_name
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
        //     $('#emp_user').blur(function() {
        //         var username = $('#emp_user').val();
        //         if (username == "") {
        //             $('#validate_user').html("กรุณากรอกข้อมูลให้เรียบร้อย");
        //             $('#validate_user').addClass("text-danger");
        //             /// remove class ///
        //             $('#validate_user').removeClass("text-success");
        //             status = false;
        //             return;
        //         }
        //         $.ajax({
        //             type: "post",
        //             url: "check_emp.php",
        //             data: {
        //                 emp_user: username
        //             },
        //             success: function(data) {
        //                 if (data != '0') {
        //                     $('#validate_user').html("มีชื่อผู้ใช้นี้ในระบบแล้ว");
        //                     $('#validate_user').addClass("text-danger");
        //                     /// remove class ///
        //                     $('#validate_user').removeClass("text-success");
        //                     status = false;
        //                 } else {
        //                     $('#validate_user').html("สามารถใช้ชื่อผู้ใช้นี้ได้");
        //                     $('#validate_user').addClass("text-success");
        //                     /// remove class ///
        //                     $('#validate_user').removeClass("text-danger");
        //                     status = true;
        //                 }
        //             }
        //         })
        //     });
        //     $("#emp_pass").blur(function() {
        //         var pass = $("#emp_pass").val();
        //         var test = password(pass);
        //         if (test == false) {
        //             $('#validate_pass').html('รหัสผ่านไม่ถูกต้อง');
        //             $('#validate_pass').addClass('text-danger');
        //             /// remove class ///
        //             $('#validate_pass').removeClass('text-success');
        //             status = false;
        //         } else {
        //             $('#validate_pass').html('รหัสผ่านถูกต้อง');
        //             $('#validate_pass').addClass('text-success');
        //             /// remove class ///
        //             $('#validate_pass').removeClass('text-danger');
        //             status = true;
        //         }
        //     });
        //     $('#emp_con').blur(function() {
        //         var emp_pass = $('#emp_pass').val();
        //         var emp_con = $('#emp_con').val();
        //         if (emp_con == "") {
        //             $('#validate_check').html('');
        //             return;
        //         } else if (emp_pass != emp_con) {
        //             $('#validate_check').html('รหัสผ่านไม่ตรงกัน');
        //             $('#validate_check').addClass('text-danger');
        //             /// remove class ///
        //             $('#validate_check').removeClass('text-success');
        //             status = false;
        //         } else {
        //             $('#validate_check').html('รหัสผ่านตรงกัน');
        //             $('#validate_check').addClass('text-success');
        //             /// remove class ///
        //             $('#validate_check').removeClass('text-danger');
        //             status = true;
        //         }
        //     });
        //     $('#emp_insert').click(function(e){
        //         if(status==false){
        //             e.preventDefault();
        //             $('#validate').html("กรุณากรอกข้อมูลให้เรียบร้อย");
        //             $('#validate').addClass("text-danger");
        //             $('#validate').removeClass("text-success");
        //             ///
        //             $('#show-list').html("กรุณากรอกข้อมูลให้เรียบร้อย");
        //             $('#show-list').addClass("text-danger");
        //             $('#show-list').removeClass("text-success");
        //             ///
        //             $('#validate_check').html('กรุณากรอกรหัสผ่านยืนยัน');
        //             $('#validate_check').addClass('text-danger');
        //             $('#validate_check').removeClass('text-success');
        //         }
        //     });
        // });
    </script>
</body>

</html>